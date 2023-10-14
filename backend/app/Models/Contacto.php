<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    protected $fillable = ['nombre','telefono','ciudad'];
    public static $rules = [
        'nombre' => 'required|string|max:50',
        'telefono' => 'required|string|size:10', // Usar 'size:10' para especificar la longitud
        'ciudad' => 'required|string|max:50',
    ];
}
