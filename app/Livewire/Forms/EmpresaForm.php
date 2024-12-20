<?php

namespace App\Livewire\Forms;

use App\Models\Empresa;
use App\Models\EmpresaMedioPago;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;
use Illuminate\Validation\Rule;

class EmpresaForm extends Form
{
    public ?Empresa $emp;


    #[Validate('required|string|max:254')]
    public $empresa = '';

    #[Validate('required|string|max:125')]
    public $key = '';

    #[Validate('nullable')]
    public $logoPath = null;

    public function setEmpresa(Empresa $empresa)
    {
        $this->emp = $empresa;

        $this->empresa = $this->emp->empresa;
        $this->key = $this->emp->key;
        $this->logoPath = $this->emp->logoPath;
    }

    public function store()
    {
        $this->activo = true;
        $this->validate();
        try {
            DB::beginTransaction();
            $newEmpresa = Empresa::create($this->all());
            //Guardado de imagen
            if($this->logoPath ){
                $basePath = "images/" . session('empresa')->id . "/" . $newEmpresa->id."/logo"; //images/empresa_id/unidad_id
                $path = $this->logoPath->store($basePath, 'public'); 
                $newEmpresa->logoPath = $path;
                $newEmpresa->update();
            }
            DB::commit();
            $this->reset();
            $this->logoPath = $this->emp->logoPath;            
        } catch (\Throwable $th) {
            DB::rollback();
        }


    }

    public function update()
    {
        $this->validate();
        $path = null;

        // Verificar imagen personalizada.
        
        //Cargar la nueva imagen
        if($this->logoPath && $this->logoPath != null && $this->logoPath != $this->emp->logoPath){
            $basePath = "images/" . session('empresa')->id . "/" . $this->emp->id."/logo"; //images/empresa_id/unidad_id/logo
            $path = $this->logoPath->store($basePath, 'public');
        }else{
            $path = $this->emp->logoPath;
        }

        //Verificar y eliminar la anterior si existe
        if ($this->emp->logoPath && $path != $this->emp->logoPath) {
            //Si existe eliminar la imagen antigua
            if(Storage::exists('public/'.$this->emp->logoPath)){
                Storage::disk('public')->delete($this->emp->logoPath);
            }
        }


        $this->emp->update([
                'empresa' => $this->empresa,
                'key' => $this->key,
                'logoPath' => $path
            ]
        );
        session('empresa')->logoPath = $path;
        $this->logoPath = $this->emp->logoPath;

    }

}