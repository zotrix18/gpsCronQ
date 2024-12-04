<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'empresa',
        'cuit',
        'direccion',
        'ciudad',
        'ingresosbrutos',
        'activo',
        'users_id',
        'wee_key',
        'ivasresponsabilidads_id',
        'ivascategorias_id',
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relación con la tabla iva responsabilidades.
     */
    // public function ivaResponsabilidad()
    // {
    //     return $this->belongsTo(IvaResponsabilidad::class, 'ivasresponsabilidads_id');
    // }

    /**
     * Relación con la tabla iva categorías.
     */
    public function ivaCategoria()
    {
        return $this->belongsTo(IvaCategoria::class, 'ivascategorias_id');
    }

    public function responsabilidad()
    {
        return $this->belongsTo(IvaResponsabilidad::class, 'ivasresponsabilidads_id');
    }

    public function empresaMedioPago()
    {
        return $this->hasMany(EmpresaMedioPago::class, 'empresas_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'usersempresas', 'empresas_id', 'users_id');
    }
}
