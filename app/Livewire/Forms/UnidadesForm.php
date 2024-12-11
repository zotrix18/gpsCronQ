<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Unidads;

class UnidadesForm extends Form
{
    public ?Unidads $unidad = null;

    #[Validate('required|string|max:125')]
    public $unidad_nombre = ''; // Renombrado para evitar conflictos con el modelo.

    #[Validate('required|string|max:45')]
    public $codigo = '';

    #[Validate('boolean')]
    public $activo = true;

    public function setUnidad(Unidads $unidad)
    {
        $this->unidad = $unidad;
        $this->unidad_nombre = $unidad->unidad;
        $this->codigo = $unidad->codigo;
        $this->empresas_id = $unidad->empresas_id;
        $this->activo = $unidad->activo ?? true;
    }

    public function store()
    {
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

        // Actualizar los datos.
        $this->unidad->update([
            'unidad' => $this->unidad_nombre,
            'codigo' => $this->codigo,
            'empresas_id' => $this->empresas_id,
            'activo' => $this->activo,
        ]);
    }
}
