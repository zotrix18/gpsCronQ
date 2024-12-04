<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talonario extends Model
{
    use HasFactory;
    protected $table = 'talonarios';

    public function comprobante()
    {
        return $this->belongsTo(ComprobanteTipo::class, 'comprobantestipos_id');
    }
}
