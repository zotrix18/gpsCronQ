<?php

namespace App\Http\Livewire\Chatbot;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client; 
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Files as Archivos;
use App\Models\Functions as Funciones;
use App\Models\Assistant as Assistants;

class Files extends Component{
    use WithFileUploads;

    public $assistants = null;
    public $fileAccept = '.pdf';
    public $vectorStoreIds = null;
    public $title = 'Archivos';
    public $archivos = [];
    public $modalData = [];
    public $file;
    public $currentStore = [
        'purpose' => 'assistants',
        'vector_store_ids' => '',
        'name' => '',
        'file' => null,
        'id' => null
    ];

    public function mount($assistants_id){       
       $this->loadAssistantData($assistants_id);
       $this->loadVectorStores();       
    }

    /**
     * Carga los datos del asistente desde la base de datos.
     *
     * Este método carga los datos del asistente que se pasa como parámetro
     * y los guarda en la propiedad `assistants`. Si el asistente
     * no existe, el valor de `assistants` será `null`.
     *
     * @param int $assistants_id El id del asistente que se quiere cargar.
     * @return void
     */
    public function loadAssistantData($assistants_id){
        $this->assistants = Assistants::where('assistants_id', $assistants_id)->whereNull('deleted_at')->first();
    }

    /**
     * Carga los archivos de vector store de un asistente
     *
     * Este método carga los archivos de vector store asociados a un asistente
     * y los guarda en la propiedad `archivos`. Si hay archivos
     * cargados, el valor de `currentStore['vector_store_ids']` se
     * actualiza con el valor del primer archivo cargado.
     *
     * @return void
     */
    public function loadVectorStores(){
        $this->archivos = Archivos::where('assistants_id', $this->assistants->id)->whereNull('deleted_at')->get();
        if(count($this->archivos) > 0){            
            $this->currentStore['vector_store_ids'] = $this->archivos[0]->vector_store_ids;
        }
    }

    /**
     * Abre el formulario para subir un nuevo archivo.
     *
     * Este método borra los valores actuales de la propiedad `currentStore`
     * y activa el dispatch de `openModal` para abrir el formulario de
     * subida de archivo.
     */
    public function new(){
        $this->currentStore['name'] = null;
        $this->currentStore['id'] = null;
        $this->currentStore['file'] = null;
        $this->currentStore['file_url'] = null;
        $this->currentStore['file_name'] = null;

        $this->dispatch('openModal');
    }

    /**
     * Editar un archivo existente.
     *
     * Este método carga los detalles de un archivo específico en el 
     * sistema, verificando su existencia en el almacenamiento. Si el 
     * archivo existe, se actualizan las propiedades `file_url` y 
     * `file_name` en `currentStore` con la URL y el nombre del archivo 
     * respectivamente. En caso contrario, se establece un mensaje de 
     * error en la sesión. Finalmente, se abre un modal para mostrar 
     * detalles o mensajes adicionales al usuario.
     *
     * @param array $archivo Los datos del archivo a editar, que incluyen 
     *                       el identificador del archivo.
     */
    public function editArchivo($archivo){
    
        try {            
            $this->currentStore['id'] = $archivo['id'];
            $archivo = Archivos::where('id', $archivo['id'])->whereNull('deleted_at')->first();
            
            if ($archivo != null && $archivo->path != null) {
                $filePath = 'public/'.$archivo->path;
                if (Storage::exists($filePath)) {
                    $this->currentStore['file_url'] = Storage::url($filePath);
                    $this->currentStore['file_name'] = basename($filePath);
                } else {    
                    $this->currentStore['file_url'] = null;
                    $this->currentStore['file_name'] = null;                
                    session()->flash('errorModal', 'El archivo no existe en el sistema de almacenamiento.');
                }
            }else{
                $this->currentStore['file_url'] = null;
                $this->currentStore['file_name'] = null;
            }
            $this->currentStore['file'] = null;
            
            $this->dispatch('openModal');        
        } catch (\Throwable $th) {
            session()->flash('error', 'Ocurrio un error al cargar el archivo: ' . $th->getMessage());
        }
    }

    public function deleteArchivo($file, $confirm = false){
        try {
            if(count($this->archivos) == 1){
                if (!$confirm) {
                    $this->dispatch('sweetAlertConfirm', ['file' => $file]);
                    return;
                }else{
                    //Logica de eliminacion openAI, vector store y bd
                    $response = $this->deleteFileOpenAI($file['files_id']);
                    if( isset($response['error'])){
                        switch ($response['code']) {
                            case 404:
                                throw new \Exception('No se encontraron archivos para borrar.');
                                break;
                            case 400:
                                throw new \Exception('Error al eliminar el archivo.');
                                break;
                            default:
                                throw new \Exception($response['error']);
                                break;
                        }                        
                    }

                    $response = $this->deleteVectorStore($file["vector_store_ids"]);
                    if( isset($response['error']) ){
                        switch ($response['code']) {
                            case 404:
                                throw new \Exception('No se encontro vector Store para borrar.');
                                break;
                            case 403:
                                throw new \Exception('Accion no permitida.');
                                break;
                            default:
                                throw new \Exception($response['error']);
                                break;
                        }
                    }


                    //Eliminar el archivo de la base de datos y fisicamente del store
                    $archivo = Archivos::where('id', $file['id'])->whereNull('deleted_at')->first();
                    if($archivo != null){
                        if(Storage::exists('public/'.$archivo->path)){
                            Storage::disk('public')->delete($file["path"]);
                        }
                        $archivo->deleted_at = now();
                        $archivo->update();
                    }
                    //Eliminar funcion de file_search de la bd
                    $funcion = Funciones::where('assistants_id', $this->assistants->id)->where('type', 'file_search')->whereNull('deleted_at')->first();
                    if($funcion != null){
                        $funcion->deleted_at = now();
                        $funcion->update();
                    }
                    $this->currentStore['vector_store_ids'] = '';
                    $this->loadVectorStores(); 
                }
            }else{
                //Solo eliminar el archivo de openAI y bd
                $response = $this->deleteFileOpenAI($file['files_id']);
                if( isset($response['error'])){
                    switch ($response['code']) {
                        case 404:
                            throw new \Exception('No se encontraron archivos para borrar.');
                            break;
                        case 400:
                            throw new \Exception('Error al eliminar el archivo.');
                            break;
                        default:
                            throw new \Exception($response['error']);
                            break;
                    }                        
                }

                //Eliminar el archivo de la base de datos y fisicamente del store
                $archivo = Archivos::where('id', $file['id'])->whereNull('deleted_at')->first();
                if($archivo != null){
                    if(Storage::exists('public/'.$archivo->path)){
                        Storage::disk('public')->delete($file["path"]);
                    }
                    $archivo->deleted_at = now();
                    $archivo->update();
                }
                $this->currentStore['vector_store_ids'] = '';
                $this->loadVectorStores(); 

            }
        } catch (\Throwable $th) {
            session()->flash('error', 'Ocurrio un error al eliminar el archivo: ' . $th->getMessage());
        }
    }


    private function deleteFileOpenAI($file_id){
        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            // 'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            // "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/files/'.$file_id;
        try {            
            $response = $client->request('DELETE', $url, [
                'headers' => $headers,                    
            ]);
            $statusCode = $response->getStatusCode();                                    
            return ['data' => 'ok'];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
            }
    }

    private function deleteVectorStore($vectorStore){
        if($vectorStore != null){
            $client = new Client();
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/vector_stores/'.$vectorStore;
            try {
                $response = $client->request('DELETE', $url, [
                    'headers' => $headers,                
                ]);
                $statusCode = $response->getStatusCode();                        
                return ['data' => 'ok'];
            } catch (\Throwable $th) {
                return ['error' => $th->getMessage(), 'code' => $th->getCode()];
            }
        }else{
            return ['data' => null];
        }
    }

    /**
     * Guarda o actualiza el archivo actual.
     *
     * @return void
     */
    public function save(){
        if($this->currentStore['file'] == null){
            session()->flash('messageModal', 'No se ha seleccionado ningun archivo');
            return;
        }        
        $tempStore = [
            'assistants_id' => $this->assistants->id,
            'vector_store_ids' => $this->currentStore['vector_store_ids'],
            'files_id' => '',
            'object' => 'file',
            'filename' => '',
            'purpose' => $this->currentStore['purpose']
        ];
        try {            
            $files = Archivos::where('id', $this->currentStore['id'])->whereNull('deleted_at')->first();
            //Si existe un archivo anterior debe eliminarlo
            if($files != null){
                $response = $this->deleteFile($files);
            }
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }
    
            //carga de archivo
            $response = $this->uploadFile();                
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }

            $tempStore['files_id'] = $response['data']['id'];
            $tempStore['filename'] = $response['data']['filename'];
            
    
            //Vincular file con vector store existente
            $response = $this->addFileToVector($tempStore['files_id'], $tempStore['vector_store_ids']);
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }
    
            $path = $this->currentStore['file']->store('files', 'public');
            $tempStore['path'] = $path;
            
            if($this->currentStore['id'] != null){       
                $tempStore['id'] = $this->currentStore['id'];
                DB::table('files')->where('id', $this->currentStore['id'])->update($tempStore);
            }else{
                DB::table('files')->insert($tempStore);
            }
    
            $this->loadAssistantData($this->assistants->assistants_id);
            $this->loadVectorStores();
            $this->currentStore['file'] = null;
            $this->currentStore['file_url'] = null;
            $this->currentStore['file_name'] = null;
            $this->dispatch('closeModal');
            session()->flash('message', 'Archivo guardado.');
        } catch (\Throwable $th) {
            session()->flash('errorModal', 'Ocurrio un error al guardar el archivo.' . $th->getMessage());
        }
    }

    /**
     * Guarda un archivo nuevo en el sistema, crea un vector store 
     * correspondiente y lo vincula con el asistente actual.
     * 
     * @return void
     */
    public function saveNew(){
        if($this->currentStore['name'] == null || $this->currentStore['name'] == '' || !is_string($this->currentStore['name']) || strlen($this->currentStore['name']) > 64){
            session()->flash('errorVectorStoreName', 'El campo debe ser una cadena de texto de máximo 64 caracteres.');
            return;
        }
        if($this->currentStore['file'] == null){
            session()->flash('errorInputFile', 'No se ha seleccionado ningun archivo');
            return;
        }
        $tempStore = [
            'assistants_id' => $this->assistants->id,
            'vector_store_ids' => '',
            'files_id' => '',
            'object' => 'file',
            'filename' => '',
            'purpose' => $this->currentStore['purpose'],    
            'path'=> null,
        ];
        try {
            
            //postfile
            $response = $this->uploadFile();
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }
            
            $tempStore['files_id'] = $response['data']['id'];
            $tempStore['filename'] = $response['data']['filename'];

            //PostVectorStoreCreate           
            $response = $this->createVectorStore($response);
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }
            $tempStore['vector_store_ids'] = $response['data']['id'];                    

            //Unirambos
            $response = $this->addFileToVector($tempStore['files_id'], $tempStore['vector_store_ids']);
            if( isset($response['error'])){
                throw new \Exception($response['error']);
            }

            //Vincular el store con asistente
            $response = $this->postAddFunctionFile($tempStore['vector_store_ids']);
            if( isset($response['error'])){
                throw new \Exception($response['error']);            
            }
            
            // $tempStore['files_id'] = $response['id'];
                                    
            $path = $this->currentStore['file']->store('files', 'public');
            $tempStore['path'] = $path;
            DB::table('files')->insert($tempStore);
            $this->loadAssistantData($this->assistants->assistants_id);
            $this->loadVectorStores();
            $this->currentStore['file'] = null;
            $this->currentStore['file_url'] = null;
            $this->currentStore['file_name'] = null;
            $this->dispatch('closeModal');
            session()->flash('message', 'Archivo guardado.');
        } catch (\Throwable $th) {
            session()->flash('errorModal', 'No se pudo guardar el archivo.'. $th->getMessage());
        }
    }

    /**
     * Elimina un archivo de la plataforma de OpenAI.
     *
     * @param $files objeto con los datos del archivo
     * @return array con los datos de la respuesta o un mensaje de error
     */
    private function deleteFile($files){
        try {
         if($files != null && $files->path != null){
            $client = new Client();
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                // 'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                // "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/files/'. $files->files_id;            
    
            $response = $client->request('DELETE', $url, [
                'headers' => $headers,                
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            Storage::disk('public')->delete($files->path);
            return ['data' => $data];         
            

         }
        }catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    /**
     * Sube un archivo a la plataforma de OpenAI.
     *
     * Este método prepara y envía una solicitud HTTP POST al endpoint de OpenAI
     * para subir un archivo. Los datos del archivo y su propósito son enviados 
     * como parte de la solicitud multipart/form-data.
     *
     * @return array Retorna un arreglo con los datos de la respuesta si la carga es exitosa
     *               o un mensaje de error si ocurre una excepción.
     */
    private function uploadFile(){
        $client = new Client();
        try {
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                // 'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                // "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/files';
            $bodyForm = [
                [
                    'name' => 'purpose',
                    'contents' => $this->currentStore['purpose'],
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($this->currentStore['file']->getPathname(), 'r'),
                    'filename' => $this->currentStore['file']->getClientOriginalName(),
                ]
            ];
    
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'multipart' => $bodyForm
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            return ['data' => $data];            
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    /**
     * Crea un vector store en la plataforma de OpenAI.
     *
     * Este método prepara y envía una solicitud HTTP POST al endpoint de OpenAI
     * para crear un vector store. El nombre del vector store es el que se encuentra
     * en `currentStore['name']`.
     *
     * @param $dataFile objeto con los datos del archivo
     * @return array con los datos de la respuesta si la creación es exitosa
     *               o un mensaje de error si ocurre una excepción.
     */
    private function createVectorStore($dataFile){
        
        $client = new Client();
        try {
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/vector_stores';
            $body = [                
                'name' => $this->currentStore['name'],                
            ];
            $jsonBody = json_encode($body);
                                  
            //postVectorStore
            $response = $client->request('POST', 'https://api.openai.com/v1/vector_stores', [
                'headers' => $headers,
                'body' => $jsonBody
            ]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $dataVectorStore = json_decode($body, true);
            return ['data' => $dataVectorStore];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }

    }

    /**
     * Agrega un archivo a un vector store.
     *
     * Este método prepara y envía una solicitud HTTP POST al endpoint de OpenAI
     * para agregar un archivo a un vector store. El archivo a agregar se
     * identifica con el valor de `fileIdOpenAI` y el vector store con el valor
     * de `vectorStoreIds`.
     *
     * @param string $fileIdOpenAI Identificador del archivo en OpenAI.
     * @param string $vectorStoreIds Identificador del vector store en OpenAI.
     * @return array con los datos de la respuesta si la creación es exitosa
     *               o un mensaje de error si ocurre una excepción.
     */
    private function addFileToVector($fileIdOpenAI, $vectorStoreIds){
        $client = new Client();
        try {
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/vector_stores/'. $vectorStoreIds .'/files';
            $body = [
                'file_id'=> $fileIdOpenAI,
            ];
            $jsonBody = json_encode($body);
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $jsonBody
            ]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            return ['data' => $data];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }                    
    }

    /**
     * Actualiza la configuración de un asistente en OpenAI
     *
     * Este método prepara y envía una solicitud HTTP POST al endpoint de OpenAI
     * para actualizar la configuración de un asistente. En particular, actualiza
     * la configuración de file_search para que busque en el vector store con
     * el identificador $vectorStoreIds.
     *
     * @param string $vectorStoreIds Identificador del vector store en OpenAI.
     * @return array con los datos de la respuesta si la actualización es exitosa
     *               o un mensaje de error si ocurre una excepción.
     */
    private function putOpenAI($vectorStoreIds){
        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/assistants';

        $body = [
            'tool_resources' =>[
                'file_search' => [
                    'vector_store_ids' => $vectorStoreIds
                ]                         
            ],
        ];

        $jsonBody = json_encode($body);

        try {    
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $jsonBody
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            DB::begintransaction();
            // if($statusCode == 200) {          
            // }        
            DB::commit();        
            return ['data' => $data];
        } catch (\Exception $e) {
            DB::rollback();
            return ['error' => $e->getMessage()];
        }

    }

    /**
     * Agrega un nuevo recurso de tipo "file_search" a un asistente en OpenAI
     * y lo configura para que busque en el vector store con el identificador
     * $vector_store_ids.
     *
     * Este método hace una solicitud HTTP POST al endpoint de OpenAI para
     * actualizar el asistente con el nuevo recurso.
     *
     * @param string $vector_store_ids Identificador del vector store en OpenAI.
     * @return array con los datos del asistente actualizado si la actualización
     *               es exitosa o un mensaje de error si ocurre una excepción.
     */
    private function postAddFunctionFile($vector_store_ids){
        $assistantController = App::make(ChatbotController::class);
        $response = $assistantController->getAssistantsByIDBD($this->assistants->assistants_id);
        $data = json_decode($response);
        $data = json_decode(json_encode($data->data[0]), true);
        $tools = $data['tools'];
        foreach ($tools as &$tool) {
            unset($tool['id']);
        }
        $tools [] = [
                "type"=> "file_search",
                "file_search"=> [
                    "ranking_options"=> [
                        "ranker"=> "default_2024_08_21",
                        "score_threshold"=> 0.0
                    ]
                ]
        ];

        $tool_resources = [
            "file_search"=> [
                "vector_store_ids"=> [$vector_store_ids]
            ]            
        ];

        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/assistants/'. $this->assistants->assistants_id;

        $body = [
            'tool_resources' => $tool_resources,
            'tools' => $tools,
        ];

        $jsonBody = json_encode($body);

        try {    
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $jsonBody
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            if($statusCode == 200) {
                DB::begintransaction();
                $funciones = new Funciones();         
                $funciones->assistants_id = $this->assistants->id;       
                $funciones->type = 'file_search';
                $funciones->parameters = json_encode($tool_resources);
                $funciones->active = 1;
                $funciones->save();
                DB::commit();
            }
            return ['assistant' => $data];
        } catch (\Exception $e) {
            DB::rollback();
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cambia el tipo de archivo aceptado dependiendo del valor de purpose en currentStore.
     * 
     * Si purpose es fine-tune o batch, se aceptan archivos .jsonl, en caso 
     * contrario se aceptan archivos .pdf.
     * 
     * Adem s, se resetea el valor de currentStore['file'] a null.
     * 
     * @return void
     */
    public function changePurpose(){
        switch ($this->currentStore['purpose']) {
            case 'fine-tune':
                $this->fileAccept = '.jsonl';
                break;
            case 'batch':
                $this->fileAccept = '.jsonl';
                break;
            case 'assistants':
                $this->fileAccept = '.pdf';
                break;
            default:
                $this->fileAccept = '.pdf';
                break;
        }        
        $this->currentStore['file'] = null;
    }
    
    public function render(){
        return view('livewire.chatbot.files');
    }
}
