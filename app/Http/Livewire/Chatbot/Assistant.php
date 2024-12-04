<?php

namespace App\Http\Livewire\Chatbot;

use Livewire\Component;
use App\Livewire\Forms\AssistantForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Assistant as Assistants;
use App\Models\Functions as Funciones;
use App\Models\Files as Archivos;
use App\Models\Numero;

use GuzzleHttp\Client;  

class Assistant extends Component
{
    public AssistantForm $asistente;
    public $title = 'Actualizar Asistente';
    public $assistant_data = [];  
    public $numeros = [];
    public $new = false;
    public $tempNumeros = [ 
        'identificador' => '',
        'numero' => '',
        'id' => '',
    ];
    public $avisoDelete = false;
    public $assistants_id;

    public function mount($assistants_id)
    {                
        $this->assistants_id = $assistants_id;
        $this->loadAssistantData();
        $this->asistente->setAssistant($this->assistant_data);
    }

    
    /**
     * Carga los datos del asistente y sus n meros asociados.
     *
     * Esta función carga los datos del asistente correspondiente al id
     * especificado en la propiedad assistants_id y los guarda en la
     * propiedad assistant_data. Tambien carga los números asociados al
     * asistente y los guarda en la propiedad numeros.
     *
     * Si el asistente no existe, se guarda un mensaje de error en la
     * sesión.
     *
     * @throws \Exception si el JSON no es v lido
     *
     * @return void
     */
    public function loadAssistantData(){
        try {
            $asistente = Assistants::where('assistants_id', $this->assistants_id)->first();
            $this->numeros = Numero::select('id','numero', 'identificador', 'empresas_id', 'assistants_id','activo')->where('assistants_id', $this->assistants_id)->whereNull('deleted_at')->get()->map(function ($numero) {
                return [
                    'id' => $numero->id,
                    'empresas_id' => $numero->empresas_id,
                    'assistants_id' => $numero->assistants_id,
                    'identificador' => $numero->identificador,
                    'numero' => $numero->numero,
                    'activo' => $numero->activo,
                ];
            })->toArray();

            if ($asistente) {
                $this->title = "Detalle asistente: " . $asistente->name;
                $this->assistant_data = $asistente; // Convertimos el modelo a array
            } else {
                session()->flash('error', 'Asistente no encontrado en la base de datos.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo obtener los datos del asistente.');
        }
    }
  
    /**
     * Agrega un número a la lista de números del asistente.
     *
     * Si el número tiene un id, se asume que es una edición y se agrega
     * directamente a la lista de números. Si no tiene id, se crea un
     * nuevo número con los valores especificados y se agrega a la lista.
     *
     * Después de agregar el número, se reinician los valores de la
     * variable temporal $this->tempNumeros.
     *
     * @return void
     */
    public function addNumero(){
        if($this->tempNumeros['id'] != ''){
            $this->numeros [] = $this->tempNumeros;        
        }else{
            //Consulta si existe un numero con el identificador y el numero ya registrado y no eliminado
            $numero = Numero::where('identificador', $this->tempNumeros['identificador'])->where('numero', $this->tempNumeros['numero'])->whereNull('deleted_at')->get();
            if (count($numero) > 0) {
                session()->flash('infoUser', 'Ya existe un número con ese identificador y número');
                return;
            }else{
                $this->numeros [] = [
                    'identificador' => $this->tempNumeros['identificador'],
                    'numero' => $this->tempNumeros['numero'],
                    'id' => null,
                    'activo' => 1
                ];
            }
        }
        $this->tempNumeros = [
            'identificador' => '',
            'numero' => '',
            'id' => '',
        ];        
    }
    
    //Metodo para cambiar el estado activo de un número
    public function toogleActivacionNumero($numeroObj){
        if($numeroObj['id'] != null){
            foreach ($this->numeros as $key => $item) {
                if ($item['id'] == $numeroObj['id']) {
                    $this->numeros[$key]['activo'] = $this->numeros[$key]['activo'] == 1 ? 0 : 1;
                    break;
                }                
            }
        }else{
            foreach ($this->numeros as $key => $item) {
                if ($item['identificador'] == $numeroObj['identificador'] && $item['numero'] == $numeroObj['numero']) {
                    $this->numeros[$key]['activo'] = $this->numeros[$key]['activo'] == 1 ? 0 : 1;
                    break;
                }                
            }
        }
    }

    /**
     * Edita un número, si el número existe en el array se actualiza,
     * sino se agrega con el estado activo en 1
     * 
     * @param array $numeroObj Array con los datos del número a editar.
     *                          Si tiene un id, se actualiza el número,
     *                          sino se crea uno nuevo.
     * 
     * @return void
     */
    public function editNumer($numeroObj){
        if($numeroObj["id"] != null){
            $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
            $this->tempNumeros = $numeroObj;
        }else{
            $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
            $this->tempNumeros = [
                'identificador' => $numeroObj["identificador"],
                'numero' => $numeroObj["numero"],
                'id' => null,
                'activo' => 1
            ];
        }
    }

    /**
     * Quita un elemento del array $this->numeros que tenga el mismo
     * identificador o numero que los parámetros recibidos.
     * 
     * @param string $identificador Identificador del número a quitar
     * @param string $numero Número a quitar
     * 
     * @return void
     */
    private function quitarDeArray($identificador, $numero){
        $index = null;

        foreach ($this->numeros as $key => $item) {        
            $itemNumero = $item['numero'];
            $itemIdentificador =$item['identificador'];
            $paramNumero =$numero;
            $paramIdentificador =$identificador;

            if ($itemNumero == $paramNumero || $itemIdentificador == $paramIdentificador) {
                $index = $key;
                break;
            }
        }        
        if ($index !== null) {
            array_splice($this->numeros, $index, 1);
        }
    }
    
    /**
     * Guarda los cambios del asistente en la base de datos y en la API de OpenAI.
     * 
     * Verifica que se haya agregado al menos un número antes de proceder. Si hay errores
     * durante el proceso de guardado, se lanza una excepción y se muestra un mensaje de error
     * al usuario. Si el asistente se guarda correctamente, se actualizan los números asociados 
     * y se recarga el asistente.
     * 
     * @return void
     */
    public function save(){
        try {                    
            if($this->tempNumeros['identificador'] != '' && $this->tempNumeros['numero'] != ''){
                session()->flash('infoUser', 'Debe añadir el número antes de continuar.');
                return;
            }

            if(count($this->numeros) < 1){
                session()->flash('infoUser', 'Debe añadir UN número antes de continuar.');
                return;
            }
            $response = $this->asistente->update();
            if((isset($response['error']))){
                throw new \Exception($response['error']);                
            }
            //actualizar numeros
            $response = $this->saveNumbers();
            if((isset($response['error']))){
                throw new \Exception($response['error']);                
            }
            
            //actualizar session
            $this->asistente->reload();
            $this->dispatch('refreshComponent');
            // Mensaje de éxito
            session()->flash('message', 'Asistente actualizado con éxito.');
        } catch (\Exception $e) {
            // Manejo de errores si la actualización de la API o base de datos falla
            session()->flash('error', 'Error al actualizar el asistente.'.$e->getMessage());
        }
    }

    /**
     * Guarda los números en la base de datos. Si el número tiene un id,
     * se actualiza, si no, se crea uno nuevo.
     * 
     * @return string Mensaje de error, o null si se guardó correctamente
     */
    private function saveNumbers(){
        DB::beginTransaction();
        try {
            foreach ($this->numeros as $item) {
                if($item["id"] == null){
                    $numero = new Numero;
                    $numero->identificador = $item["identificador"];
                    $numero->numero = $item["numero"];
                    $numero->empresas_id = session('empresa')->id;
                    $numero->assistants_id = $this->assistants_id;
                    $numero->activo = $item["activo"];
                    $numero->save();                                                                
                }else{
                    Numero::where('id', $item["id"])->update([
                        'identificador' => $item["identificador"],
                        'numero' => $item["numero"],
                        'activo' => $item["activo"],
                    ]);
                }
            }            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    /**
     * Elimina un número del asistente.
     *
     * Si el número que se va a eliminar es el único que tiene el asistente,
     * se pide confirmación al usuario antes de borrar el número. Si el usuario
     * confirma la eliminación, se elimina el número y se actualiza la sesión.
     *
     * @param array $numeroObj Array con los datos del número a eliminar.
     *                          Si el número tiene un id, se asume que es una
     *                          edición y se elimina directamente. Si no tiene
     *                          id, se busca el número en la base de datos y
     *                          se elimina.
     *
     * @param boolean $avisoDelete Si es true, se omite el mensaje de confirmación
     *                              al usuario.
     *
     * @return void
     */
    public function delNumber($numeroObj, $avisoDelete = false){
        try {
            if(count($this->numeros) == 1){
                if (!$avisoDelete) {
                    $this->dispatch('sweetAlertConfirm', ['numeroObj' => $numeroObj]);
                    return;
                }else{
                    if($numeroObj["id"] == null){
                        $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
                    }else{
                        DB::beginTransaction();
                        $numero = Numero::where('id', $numeroObj["id"])->whereNull('deleted_at')->first();
                        if($numero != null){
                            $numero->activo = 0;
                            $numero->deleted_at = now();
                            $numero->update();
                        }
                        $response = $this->deleteAssistant();
                        if((isset($response['error']))){
                            throw new \Exception($response['error']);
                        }
                        $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
                        DB::commit();
                    }

                    //actualizar session
                    // $this->asistente->resetAssistant();
                    $this->asistente->reload();
                    return redirect()->route('chatbot.assistant.new');

                }
            }else{
                if($numeroObj["id"] == null){
                    $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
                }else{
                    $numero = Numero::where('id', $numeroObj["id"])->whereNull('deleted_at')->first();
                    if($numero != null){
                        $numero->activo = 0;
                        $numero->deleted_at = now();
                        $numero->update();   
                        $this->quitarDeArray($numeroObj["identificador"], $numeroObj["numero"]);
                    }
                }
            }            
        } catch (\Throwable $th) {
            DB::rollBack();            
            session()->flash('error', 'Error al borrar el número: '.$th->getMessage());
        }
    }

    /**
     * Elimina un Asistente de la plataforma de OpenAI.
     * 
     * Este método elimina todos los archivos de vector store asociados al asistente,
     * el vector store en si mismo y todos los numeros asociados al asistente, ademas de los datos del asistente en la base de datos.
     * 
     * @throws \Exception si hubo un error al borrar los archivos de vector store, 
     *                    el vector store en si mismo o los numeros asociados al asistente.
     * 
     * @return array con los datos de la respuesta o un mensaje de error.
     */
    private function deleteAssistant(){
        try {
            // Borrar todos los archivos de openAI
            $response = $this->deleteAllFiles();
            if( isset($response['error'])){
                switch ($response['code']) {
                    case 404:
                        throw new \Exception('No se encontraron archivos para borrar.');
                        break;
                    default:
                        throw new \Exception($response['error']);
                        break;
                }
                
            }    

            //Borrar el vectorStore
            $response = $this->deleteVectorStore($response['data']);
            if( isset($response['error'])){
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
            // Borrar todos los numeros
            $response = $this->deleteAssistantOpenAI();
            if( isset($response['error'])){
                switch ($response['code']) {
                            case 404:
                                throw new \Exception('No se encontro Asistente para eliminar.');
                                break;
                            default:
                                throw new \Exception($response['error']);
                                break;
                        }
            }

            //Eliminar files BD 
            $files = Archivos::where('assistants_id', $this->asistente->assistants->id)->whereNull('deleted_at')->get();
            foreach ($files as $file) {                
                $file->deleted_at = now();
                $file->update();
                //Eliminar archivo fisico

                //añadir if exist
                Storage::disk('public')->delete($file->path);
            }

            //Eliminar Funciones BD
            $functions = Funciones::where('assistants_id', $this->asistente->assistants->id)->whereNull('deleted_at')->get();
            foreach ($functions as $function) {
                $function->deleted_at = now();
                $function->update();
            }

            //Eliminar Asistente BD
            $Assistants = Assistants::where('id', $this->asistente->assistants->id)->whereNull('deleted_at')->first();
            $Assistants->deleted_at = now();
            $Assistants->update();            

            return ['data' => 'ok'];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }

    }

    /**
     * Elimina todos los archivos asociados a un asistente en OpenAI.
     *
     * @return array con el vector store id o un mensaje de error
     */
    private function deleteAllFiles(){
        $files_id = [];
        $vectorStore = '';
        $archivos = Archivos::where('assistants_id', $this->asistente->assistants->id)->whereNull('deleted_at')->get();
        foreach ($archivos as $archivo) {
            $files_id []= $archivo->files_id;
            $vectorStore = $archivo->vector_store_ids;
        }
        if(count($archivos) > 0){
            $client = new Client();
            $token = env('OPENAI_API_TOKEN');
            $headers = [
                // 'OpenAI-Beta' => 'assistants=v2',
                'Authorization' => 'Bearer ' . $token,
                // "Content-Type" => "application/json"
            ];
            $url = 'https://api.openai.com/v1/files/';
            try {
                foreach ($files_id as $file_id) {
                    $file_id;
                    $response = $client->request('DELETE', $url.$file_id, [
                        'headers' => $headers,                    
                    ]);
                    $statusCode = $response->getStatusCode();                        
                }
                return ['data' => $vectorStore];
            } catch (\Throwable $th) {
                return ['error' => $th->getMessage(), 'code' => $th->getCode()];
            }
        }else{
            return ['data' => null];
        }
    }

    /**
     * Elimina un vector store en la plataforma de OpenAI.
     *
     * Este método envía una solicitud HTTP DELETE al endpoint de OpenAI para
     * eliminar un vector store identificado por $vectorStore. Si el vector store
     * es nulo, no se realiza ninguna acción.
     *
     * @param string|null $vectorStore Identificador del vector store a eliminar.
     * @return array con el resultado de la operación, 'ok' si se elimina exitosamente,
     *               o un mensaje de error si ocurre una excepción.
     */
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
     * Elimina un asistente en la plataforma de OpenAI.
     *
     * Este método envía una solicitud HTTP DELETE al endpoint de OpenAI para
     * eliminar un asistente identificado por $this->assistants_id. Si la eliminación
     * es exitosa, retorna 'ok'. Si ocurre una excepción, retorna un mensaje de error.
     *
     * @return array Resultado de la operación, 'ok' si se elimina exitosamente,
     *               o un mensaje de error si ocurre una excepción.
     */
    private function deleteAssistantOpenAI(){
        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/assistants/'. $this->assistants_id;
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
    
    public function render()
    {
        return view('livewire.chatbot.assistant');
    }
}
