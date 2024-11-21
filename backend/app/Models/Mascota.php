<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Mascota extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'peso',
        'fecha_nacimiento',
        'amo_id',
    ];

    // Definimos la relación con el modelo Amo
    public function amo()
    {
        return $this->belongsTo(Amo::class);
    }

    public function historias()
    {
        return $this->hasMany(Historia::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class);
    }
}
