<?php
namespace App\Livewire\Forms;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\App;

use App\Models\Assistant;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Livewire\Form;
use GuzzleHttp\Client;

class AssistantForm extends Form
{
    public ?Assistant $assistants = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|string|max:500')]
    public $description = '';

    #[Validate('nullable|string|max:256000')]
    public $instructions = '';

    #[Validate('required|string|max:255')]
    public $model = '';

    public $metadata = [];


    public function setAssistant(Assistant $assistantData)
    {
        $this->assistants = $assistantData;
        $this->name = $assistantData['name'] ?? '';
        $this->description = $assistantData['description'] ?? '';
        $this->instructions = $assistantData['instructions'] ?? '';
        $this->model = $assistantData['model'] ?? '';
        $this->metadata = $assistantData['metadata'] ?? [];
    }


    public function update()
    {
        $this->validate();

        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/assistants/'. $this->assistants->assistants_id;

        $body = [
            'name' => $this->name,
            'description' => $this->description,
            'model' => $this->model,
            'instructions' => $this->instructions,            
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

            $options = [
                'tool_resources' => $data['tool_resources'],
                'top_p' => $data['top_p'],
                'temperature' => $data['temperature'],
                'response_format' => $data['response_format']        
            ];
            $options = json_encode($options);
    
            DB::begintransaction();
            if($statusCode == 200) {
                $this->assistants->name=$this->name;
                $this->assistants->description=$this->description;
                $this->assistants->model=$this->model;
                $this->assistants->instructions=$this->instructions;
                $this->assistants->metadata=$this->metadata;
                $this->assistants->update();       
            }        
            DB::commit();        
            return ['assistant' => $this->assistants];
            } catch (\Exception $e) {
                DB::rollback();
                return ['error' => $e->getMessage()];
            }
    

        
    }

    public function save(){
        $this->validate();

        $client = new Client();
        $token = env('OPENAI_API_TOKEN');
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $token,
            "Content-Type" => "application/json"
        ];
        $url = 'https://api.openai.com/v1/assistants';

        $body = [
            'name' => $this->name,
            'description' => $this->description,
            'model' => $this->model,
            'instructions' => $this->instructions,            
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

        //Para pruebas simuladas sin consumo de api
        // $assistantController = App::make(ChatbotController::class);
        // $statusCode = 200;
        // $response = $assistantController->newAssistantStatic($this->name, $this->description, $this->model, $this->instructions);
        // $data= $response->getData();
        // $data = json_decode(json_encode($response->getData()), true);

        $options = [
            'tool_resources' => $data['tool_resources'],
            'top_p' => $data['top_p'],
            'temperature' => $data['temperature'],
            'response_format' => $data['response_format']        
        ];
        $options = json_encode($options);

        DB::begintransaction();
        if($statusCode == 200) {
            $assistant = new Assistant();
            $assistant->assistants_id = $data['id'];
            $assistant->name = $data['name'];
            $assistant->description = $data['description'];
            $assistant->instructions = $data['instructions'];
            $assistant->model = $data['model'];
            $assistant->tools =  'function';
            $assistant->options = $options;
            $assistant->metadata = json_encode($data['metadata']);
            $assistant->created_at_openia = $data['created_at'];
            $assistant->save();            
        }        
        DB::commit();        
        return ['assistant' => $assistant];
        } catch (\Exception $e) {
            DB::rollback();
            return ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }

       
    }

    public function reload(){
        $empresas_id = session('empresa')->id;
        $assistants = DB::table('empresas')
            ->join('numeros', 'empresas.id', '=', 'numeros.empresas_id')
            ->join('assistants', 'numeros.assistants_id', '=', 'assistants.assistants_id')
            ->where('empresas.id', session('empresa')->id)
            ->whereNull('assistants.deleted_at')
            ->whereNull('numeros.deleted_at')
            ->select(
                'empresas.empresa as nombre_empresa',
                'assistants.assistants_id as assistants_id',
                'assistants.name as nombre_asistente',
                'assistants.description as descripcion_asistente',
                DB::raw('GROUP_CONCAT(numeros.numero SEPARATOR ", ") as numeros'),
                DB::raw('GROUP_CONCAT(numeros.identificador SEPARATOR ", ") as prefijos')
            )
            ->groupBy('assistants.assistants_id', 'empresas.empresa', 'assistants.name', 'assistants.description')
            ->get();
        session(['asistentes' => $assistants]);
    }
}
