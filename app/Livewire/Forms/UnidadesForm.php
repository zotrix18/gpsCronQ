<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Form;
use App\Models\Unidads;

class UnidadesForm extends Form
{
    use WithFileUploads;
    public ?Unidads $unidad = null;

    #[Validate('required|string|max:125')]
    public $unidad_nombre = ''; // Renombrado para evitar conflictos con el modelo.

    #[Validate('required|string|max:45')]
    public $codigo = '';
 
    #[Validate('nullable')]
    public $imgCustomPath = '';

    #[Validate('required|string')]
    public $imgId = '';
    
    #[Validate('boolean')]
    public $activo = true;

    public function setUnidad(Unidads $unidad){
        $this->unidad = $unidad;
        $this->unidad_nombre = $unidad->unidad;
        $this->codigo = $unidad->codigo;
        $this->empresas_id = $unidad->empresas_id;
        $this->imgId = $unidad->primaryPath;
        $this->imgCustomPath = $unidad->path;
        $this->activo = $unidad->activo ?? true;
    }

    public function store(){
        $this->activo = true;
        $this->validate();

        $data = $this->all();
        $data['empresas_id'] = session('empresa')->id;
        $data['unidad'] = $this->unidad_nombre;
        $data['activo'] = false;

        Unidads::create($data);
        $this->reset();
    }

    public function update()
    {
        // Validar los datos.
        $this->validate();

        // Verificar si la unidad está inicializada.
        if (!$this->unidad) {
            throw new \Exception('La unidad no está inicializada.');
        }
        $path = null;

        // Verificar imagen personalizada.
        
        //Cargar la nueva imagen
        if($this->imgCustomPath && $this->imgCustomPath != null && $this->imgCustomPath != $this->unidad->path){
            $basePath = "customImg/" . session('empresa')->id . "/" . $this->unidad->id;
            $path = $this->imgCustomPath->store($basePath, 'public');
        }else{
            $path = $this->unidad->path;
        }

        //Verificar y eliminar la anterior si existe
        if ($this->unidad->path && $path != $this->unidad->path) {
            //Si existe eliminar la imagen antigua
            if(Storage::exists('public/'.$this->unidad->path)){
                Storage::disk('public')->delete($this->unidad->path);
            }
        }

        

        // Actualizar los datos.
        $this->unidad->update([
            'unidad' => $this->unidad_nombre,
            'codigo' => $this->codigo,            
            'primaryPath' => $this->imgId,
            'path' => $path,
            'activo' => $this->activo,
        ]);
    }
}
