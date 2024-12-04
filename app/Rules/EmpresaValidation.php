<?php

namespace App\Rules;

class EmpresaValidation
{
  public static function rules()
  {
    return [
      'empresa.empresa' => 'required|string|max:254',
      'empresa.cuit' => 'required|string|max:11|unique:empresas,cuit',
      'empresa.direccion' => 'required|string|max:254',
      'empresa.ciudad' => 'required|string|max:125',
      'empresa.ingresosbrutos' => 'nullable|string|max:254',
      'empresa.activo' => 'boolean',
      'empresa.users_id' => 'required|exists:users,id',
      'empresa.wee_key' => 'nullable|string|max:45',
      // 'empresa.ivasresponsabilidads_id' => 'exists:ivasresponsabilidades,id',
      'empresa.ivascategorias_id' => 'required|exists:ivascategorias,id',
    ];
  }
}
