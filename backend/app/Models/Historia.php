<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historia extends Model
{
    protected $table = 'historias_clinicas';
    protected $fillable = ['mascotas_id', 'veterinarios_id', 'fecha_consulta', 'diagnostico', 'tratamiento'];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascotas_id');
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class, 'veterinarios_id');
    }
}
