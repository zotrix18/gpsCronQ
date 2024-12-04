<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\EmpresaMedioPago;

class EmpresasMediosPagoForm extends Form
{
    public ?EmpresaMedioPago $empmediopago;

    public $empresa_id = '';

    #[Validate('required')]
    public $mediospagos_id = '';

    #[Validate('required|max:150')]
    public $url = '';

    #[Validate('required|max:255')]
    public $clave_acceso = '';

    #[Validate('required|max:100')]
    public $payment_type = '';

    #[Validate('required|max:150')]
    public $url_exito = '';

    #[Validate('required|max:150')]
    public $url_pendiente = '';

    #[Validate('required|max:150')]
    public $url_cancelado = '';

    public function setEmpresaMedioPago(EmpresaMedioPago $empresamediopago)
    {
        $this->empmediopago = $empresamediopago;

        $this->empresas_id = $this->empmediopago->empresas_id;
        $this->mediospagos_id = $this->empmediopago->mediospagos_id;
        $this->url = $this->empmediopago->url;
        $this->clave_acceso = $this->empmediopago->clave_acceso;
        $this->payment_type = $this->empmediopago->payment_type;
        $this->url_exito = $this->empmediopago->url_exito;
        $this->url_pendiente = $this->empmediopago->url_pendiente;
        $this->url_cancelado = $this->empmediopago->url_cancelado;
        $this->activo = $this->empmediopago->activo;
    }

    public function store()
    {
        $this->validate();

        $empresamediopago = new EmpresaMedioPago;
        $empresamediopago->empresas_id = $this->empresa_id;
        $empresamediopago->mediospagos_id = $this->mediospagos_id;
        $empresamediopago->url = $this->url;
        $empresamediopago->clave_acceso = $this->clave_acceso;
        $empresamediopago->payment_type = $this->payment_type;
        $empresamediopago->url_exito = $this->url_exito;
        $empresamediopago->url_pendiente = $this->url_pendiente;
        $empresamediopago->url_cancelado = $this->url_cancelado;

        $empresamediopago->save();

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $empresamediopago = EmpresaMedioPago::find($this->empmediopago->id);
        $empresamediopago->empresas_id = $this->empmediopago->empresas_id;
        $empresamediopago->mediospagos_id = $this->mediospagos_id;
        $empresamediopago->url = $this->url;
        $empresamediopago->clave_acceso = $this->clave_acceso;
        $empresamediopago->payment_type = $this->payment_type;
        $empresamediopago->url_exito = $this->url_exito;
        $empresamediopago->url_pendiente = $this->url_pendiente;
        $empresamediopago->url_cancelado = $this->url_cancelado;

        $empresamediopago->update();
    }
}
