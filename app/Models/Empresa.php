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
        'key',
        'logoPath',
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }   
    
       public function users()
    {
        return $this->belongsToMany(User::class, 'usersempresas', 'empresas_id', 'users_id');
    }
}
