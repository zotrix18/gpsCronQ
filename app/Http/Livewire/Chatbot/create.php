<?php

namespace App\Http\Livewire\Chatbot;

use Livewire\Component;
use App\Http\Controllers\ChatbotController;
use App\Livewire\Forms\AssistantForm;
use Illuminate\Support\Facades\DB;
use App\Models\Numero;
use App\Models\Assistant as Assistants;
use GuzzleHttp\Client; 

class create  extends Component{
    public AssistantForm $asistente;
    public $title = 'Nuevo asistente';    
    public $numeros = [];
    public $new = true;
    public $tempNumeros = [ 
        'identificador' => '',
        'numero' => '',
        'id' => '',
    ];

    public function mount(){          

    }

    /**
     * Agrega un nuevo número a la lista de números
     * Si el objeto $tempNumeros tiene un id, se agrega el objeto completo
     * de lo contrario se crea un nuevo objeto con los datos del 
     * $tempNumeros y se agrega a la lista
     * Finalmente se resetea el objeto $tempNumeros
     * @return void
     */
    public function addNumero(){
        if($this->tempNumeros['id'] != ''){
            $this->numeros [] = $this->tempNumeros;        
        }else{
            $this->numeros [] = [
                'identificador' => $this->tempNumeros['identificador'],
                'numero' => $this->tempNumeros['numero'],
                'id' => null,
                'activo' => 1
            ];
        }
        $this->tempNumeros = [
            'identificador' => '',
            'numero' => '',
            'id' => '',
        ];        
    }

    /**
     * Metodo para cambiar el estado activo de un número
     * @param array $numeroObj
     * @return void
     */
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
     * Metodo para editar un número, si el número existe en el array se actualiza,
     * sino se agrega con el estado activo en 1
     * @param array $numeroObj
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
     * Guarda los números en la base de datos. Si el número tiene un id,
     * se actualiza, si no, se crea uno nuevo.
     * 
     * @param int $asistente_id Id del asistente al que se le asignan los números
     * 
     * @return string Mensaje de error, o null si se guardó correctamente
     */
    private function saveNumbers($asistente_id){
        DB::beginTransaction();
        try {
            foreach ($this->numeros as $item) {
                if($item["id"] == null){
                    $numero = new Numero;
                    $numero->identificador = $item["identificador"];
                    $numero->numero = $item["numero"];
                    $numero->empresas_id = session('empresa')->id;
                    $numero->assistants_id = $asistente_id;
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
     * Guarda el asistente en la base de datos y en la API de OpenAI.
     * 
     * Verifica que se haya agregado al menos un número antes de proceder. Si hay errores
     * durante el proceso de guardado, se lanza una excepción y se muestra un mensaje de error
     * al usuario. Si el asistente se guarda correctamente, se actualizan los números asociados 
     * y se recarga el asistente.
     * 
     * @return void
     */
    public function save(){
        if($this->tempNumeros['identificador'] != '' && $this->tempNumeros['numero'] != ''){
            session()->flash('infoUser', 'Debe añadir el número antes de continuar.');
            return;
        }

        if(count($this->numeros) < 1){
            session()->flash('infoUser', 'Debe añadir UN número antes de continuar.');
            return;
        }

        try {
            $response = $this->asistente->save();
            if((isset($response['error']))){
                switch ($response['code']) {                    
                    case 400:
                        if (preg_match('/"message":\s*"([^"]+)"/', $response['error'], $matches)) {                                                        
                            throw new \Exception($matches[1]);
                        } else {                            
                            throw new \Exception('Error desconocido durante la petición.');
                        }
                        break;
                    default:
                        throw new \Exception($response['error']);
                        break;
                }              
            }
            if(isset($response['assistant'])){
                $this->saveNumbers($response['assistant']['assistants_id']);                
            }

            $this->asistente->reload();
            session()->flash('message', 'Asistente creado.');
            $this->dispatch('refreshComponent');
        } catch (\Throwable $th) {
            session()->flash('error', 'No se pudo obtener crear el asistente: '.$th->getMessage());
        }
        
    }
    
    /**
     * Elimina un número del sistema.
     *
     * Si el número no tiene un ID, se elimina del array local utilizando su identificador
     * y número. Si el número tiene un ID, se marca como inactivo en la base de datos y se
     * actualiza su campo de eliminación. Posteriormente, se elimina del array local y se
     * llama a la función para eliminar el asistente relacionado.
     *
     * @param array $numeroObj Datos del número a eliminar, debe incluir 'id', 'identificador', y 'numero'.
     *
     * @return void
     */
    public function delNumber($numeroObj){    
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
            $this->deleteAssistant();
        }                    
    }
    
    public function render(){
        return view('livewire.chatbot.assistant');
    }
}
