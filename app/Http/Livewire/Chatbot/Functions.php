<?php

namespace App\Http\Livewire\Chatbot;

use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\App;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\AssistantForm;
use App\Models\Assistant;
use App\Models\Functions as Funciones;
use GuzzleHttp\Client;

class Functions extends Component{
    public AssistantForm $asistente;
    public $title = '';
    public $assistant_data = [
        'name' => '',
        'tools' => []
    ];
    public $assistants_id;
    public $toolsJson;
    public $modalData = [
        'type' => null,
        'name' => null,
        'description' => null,
        'parameters' => [],
        'strict' => null,
        'file_search' => [
            'ranking_options' => [
                'ranker' => null,
                'score_threshold' => null,
            ],
        ],
    ];
    public $toogleView = true;

    public function mount($assistants_id){                
        $this->assistants_id = $assistants_id;
        $this->loadAssistantData();
    }

   
    /**
     * Carga los datos del asistente desde la API y la base de datos.
     * 
     * @return void
     */
    public function loadAssistantData(){
        $client = new Client();
        try {    
            $assistantController = App::make(ChatbotController::class);
            $response = $assistantController->getAssistantsByIDBD($this->assistants_id);
            // $data = $response['data'];
            $data = json_decode($response);
            $this->assistant_data = json_decode(json_encode($data->data[0]), true);
            $this->toolsJson = json_encode($this->assistant_data['tools'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $this->title = $this->assistant_data['name'];
            // $this->assistantsFunctions = json_encode($data['tools'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo obtener los datos del asistente.'. $e->getMessage());
        }
    }

    /**
     * Abre el modal para crear una nueva función
     * Se envia un objeto con los datos por defecto
     * para que se pueda manipular en el modal
     * @return void
     */
    public function new () {
        $tool = [
            "id"=> null,
            "type" => "function",
            "function" => [
                "name" => "",
                "description" => "",
                "parameters" => [
                    "required" => [],
                    "properties" => []
                ],
                "strict" => false
            ]
        ];
        $this->prepareDataForModal($tool);        
    }

    /**
     * Elimina una función de un asistente en la plataforma de OpenAI.
     * 
     * Este método elimina una función identificada por $tool['id'] en el 
     * asistente identificado por $this->assistants_id. Si la eliminación
     * es exitosa, retorna un mensaje de éxito. Si ocurre una excepción, 
     * retorna un mensaje de error.
     * 
     * @param array $tool Los datos de la función a eliminar, con la clave
     *                     'id' que contiene el identificador de la función.
     * 
     * @return void
     */
    public function deleteFunction($tool) {       
        try {            
            $tools = json_decode($this->toolsJson, true);
                
            foreach ($tools as $key => &$tul) {            
                if ($tul['id'] == $tool['id']) {                
                    unset($tools[$key]);
                } else {                
                    unset($tul['id']);
                }
            }
                
            $tools = array_values($tools);
                
            $client = new Client();
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                "Content-Type" => "application/json"
            ];
                
            $url = 'https://api.openai.com/v1/assistants/' . $this->assistants_id;
                
            $body = [
                'tools' => $tools
            ];
                
            $jsonBody = json_encode($body);
                
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $jsonBody
            ]);
            $statusCode = $response->getStatusCode();
            if($statusCode == 200){
                $function = Funciones::where('id', $tool['id'])->whereNull('deleted_at')->first();
                $function->deleted_at = now();
                $function->update();
                $this->loadAssistantData();
                session()->flash('success', 'Función eliminada correctamente.');
            }
        } catch (\Throwable $th) {
            session()->flash('error', 'Error al eliminar la función.');
        } 

    }
        
    /**
     * Prepara los datos para el modal de edición
     *
     * @param array $tool La herramienta a editar
     *
     * @return void
     */
    public function prepareDataForModal($tool){
        if ($tool['type'] === 'function') {
            $parameters = [];
            foreach ($tool['function']['parameters']['properties'] as $name => $details) {
                $parameters[] = [
                    'name' => $name,
                    'type' => $details['type'] ?? 'Tipo no disponible',
                    'description' => $details['description'] ?? 'Descripción no disponible',
                    'required' => in_array($name, $tool['function']['parameters']['required'] ?? []),
                ];
            }

            $this->modalData = [
                'id' => $tool['id'],
                'type' => $tool['type'] ?? 'Tipo no disponible',
                'name' => $tool['function']['name'] ?? 'Nombre no disponible',
                'description' => $tool['function']['description'] ?? 'Descripción no disponible',
                'parameters' => $parameters,
                'strict' => $tool['function']['strict'] ?? false,
            ];
        } else {
            // En caso de otro tipo de tool
            $this->modalData = [
                'type' => $tool['type'] ?? 'Tipo no disponible',
                'file_search' => [
                    'ranking_options' => [
                        'ranker' => $tool['file_search']['ranking_options']['ranker'] ?? null,
                        'score_threshold' => $tool['file_search']['ranking_options']['score_threshold'] ?? null,
                    ],
                ],
            ];
        }

        if(count($this->modalData['parameters']) == 0){
            $this->addParameter();
        }

        // Emitir evento para abrir el modal
        $this->dispatch('openModal');
    }

    /**
     * Agrega un parámetro a la lista de parámetros de una función
     *
     * @return void
     */
    public function addParameter(){
        $this->modalData['parameters'][] = [
            'name' => '',
            'type' => 'object',
            'description' => '',
            'required' => false,
        ];
    }

    /**
     * Elimina un parámetro de la lista de parámetros de una función
     * 
     * @param int $index Posición del parámetro a eliminar
     * 
     * @return void
     */
    public function removeParameter($index){
        if (isset($this->modalData['parameters'][$index])) {
            unset($this->modalData['parameters'][$index]);
            // Reindexar el arreglo para evitar problemas en el frontend
            $this->modalData['parameters'] = array_values($this->modalData['parameters']);
        }
    }

    /**
     * Guarda los cambios realizados en el modal de edición de funciones
     * en la base de datos de OpenAI y en la base de datos local.
     *
     * @return void
     */
    public function saveModal(){
        try {
            //Verificar campos
            $name = $this->modalData['name'];
            $description = $this->modalData["description"];
            // Validar que sean cadenas de texto y no superen los 255 caracteres
            if (!is_string($this->modalData['name']) || strlen($this->modalData['name']) > 100) {
                $this->modalData['name'] = '';
                session()->flash('errorName', 'El campo "Nombre" debe ser una cadena de texto de máximo 255 caracteres.');
                return;
            }

            if (!is_string($this->modalData["description"]) || strlen($this->modalData["description"]) > 255) {
                $this->modalData["description"] = '';
                session()->flash('errorDescription', 'El campo "Descripción" debe ser una cadena de texto de máximo 255 caracteres.');
                return;
            }

            if($name == '' || $description == ''){
                session()->flash('errorModal', 'Faltan campos obligatorios.');
                return;
            }
            
            $properties = [];
           
            foreach ($this->modalData['parameters'] as $index => $parameter) {
                if (!isset($parameter['name']) || !is_string($parameter['name']) || strlen($parameter['name']) > 64) {
                    session()->flash('errorParamName'.$index, 'El campo "Nombre" debe ser una cadena de texto de máximo 64 caracteres.');
                    return;
                }
                if(isset($parameter['name']) && isset($parameter['type']) && isset($parameter['description'])){
                    $data = [                
                            'type' => $parameter['type']?? '',
                            'description' => $parameter['description']?? '',
                    ];
                    $properties[$parameter['name']] = $data;
                }
            }
               
            $required = [];

            foreach ($this->modalData['parameters'] as $parameter) {
                if ($parameter['required']) {
                    $required[] = $parameter['name'];
                }
            }

            $decodedFunctions = [
                [
                    "id" => $this->modalData['id'],
                    "type" => $this->modalData['type'],
                    "function" => [
                        'name' => $this->modalData['name'],
                        'description' => $this->modalData['description'],
                        "parameters" => [
                            'type' => 'object',
                            'properties' => $properties ?? [],
                            'required' => $required,
                        ],
                        'strict' => $this->modalData['strict'],
                    ],
                ],
            ];
            
            $client = new Client();
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/assistants/'. $this->assistants_id;

            $tools = json_decode($this->toolsJson, true);
           
            foreach ($tools as &$tool) {
                unset($tool['id']);
            }

            $found = false;
            foreach ($tools as &$tool) {
                if ($tool['function']['name'] === $this->modalData['name']) {
                    $tool['function'] = [
                        'name' => $this->modalData['name'],
                        'description' => $this->modalData['description'],
                        "parameters" => [
                            'type' => 'object',
                            'properties' => $properties ?? [],
                            'required' => $required,
                        ],
                        'strict' => $this->modalData['strict'],
                    ];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $tools[] = [
                    "type" => $this->modalData['type'],
                    "function" => [
                        'name' => $this->modalData['name'],
                        'description' => $this->modalData['description'],
                        "parameters" => [
                            'type' => 'object',
                            'properties' => $properties ?? [],
                            'required' => $required,
                        ],
                        'strict' => $this->modalData['strict'],
                    ],
                ];
            }

            // Construye el cuerpo para la solicitud
            $body = [
                'tools' => $tools
            ];

            // Convierte el cuerpo a JSON
            $jsonBody = json_encode($body);
           
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $jsonBody
            ]);
            

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            $options = [
                'tool_resources' => $data['tool_resources'],
                'top_p' => $data['top_p'],
                'temperature' => $data['temperature'],
                'response_format' => $data['response_format']
            ];
            $options = json_encode($options);
    
            DB::begintransaction();
            if($statusCode == 200) {
                $this->saveFunctionsBD($decodedFunctions);
                $this->loadAssistantData();
                // $this->dispatch('refreshComponent');
                $this->dispatch('closeModal');
            }
            DB::commit();
            session()->flash('message', 'Asistente actualizado con éxito.');
        } catch (\Exception $e) {
            session()->flash('errorModal', 'Error al actualizar el asistente: ' . $e->getMessage());
        }
    }

    /**
     * Guarda los datos del asistente en la base de datos y actualiza la variable asistente_data
     *
     * @throws \Exception si el JSON no es válido
     *
     * @return void
     */
    public function save(){            
        try {
            $decodedData = json_decode($this->toolsJson, true);            
            $this->assistant_data['tools'] = $decodedData;
            // $this->dispatch('refreshComponent');
            session()->flash('message', 'Tools guardados correctamente!');
        } catch (\Exception $e) {                
            session()->flash('error', 'El JSON no es válido.'. $e->getMessage());
        }
    }

    /**
     * Guarda las funciones en la base de datos, actualizando si ya existen.
     *
     * @param array $decodedFunctions Arreglo con las funciones decodificadas
     *
     * @return void
     *
     * @throws \Throwable si ocurre un error al guardar las funciones
     */
    private function saveFunctionsBD ($decodedFunctions){
        $assistant = Assistant::where('assistants_id', $this->assistants_id)->first();
        $funciones = null;
        if($decodedFunctions[0]['id'] != 0){
            $funciones = Funciones::where('id', $decodedFunctions[0]['id'])->get();
        }
        $others = [
            'top_p'=> $this->assistant_data['top_p'],
            'temperature'=> $this->assistant_data['temperature'],
            'metadata'=> $this->assistant_data['metadata'],
            'response_format'=> $this->assistant_data['response_format'],
        ];
        try {            
            DB::beginTransaction();
            foreach ($decodedFunctions as $funcion) {
                $existingFunction = $funciones ? $funciones->firstWhere('id', $decodedFunctions[0]['id']) : null;                
                if ($existingFunction) {    
                    $existingFunction->name = $funcion['function']['name'];               
                    $existingFunction->description = $funcion['function']['description'];
                    $existingFunction->parameters = json_encode($funcion['function']['parameters']);
                    $existingFunction->strict = $funcion['function']['strict'];
                    // $existingFunction->others = json_encode($others);
                    $existingFunction->update(); 
                } else {
                    $newFunction = new Funciones();
                    $newFunction->assistants_id = $assistant->id;
                    $newFunction->name = $funcion['function']['name'];
                    $newFunction->description = $funcion['function']['description'];
                    $newFunction->parameters = json_encode($funcion['function']['parameters']);
                    $newFunction->save(); 
                }                
            }
    
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Cambia el estado de visibilidad entre vista de tabla y vista JSON.
     *
     * Alterna el valor booleano de la propiedad `toogleView`, 
     * que determina qué vista se muestra al usuario.
     *
     * @return void
     */
    public function toggleView () {
        $this->toogleView = !$this->toogleView;
    }
    public function render(){
        return view('livewire.chatbot.function');
    }
}
