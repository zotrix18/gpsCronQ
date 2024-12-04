<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmpresa extends Model
{
    use HasFactory;

    protected $table = "usersempresas";

    protected $fillable = ["empresas_id", "users_id"];
}
