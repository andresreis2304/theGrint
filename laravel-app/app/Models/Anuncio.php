<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    protected $table = 'anuncio';
    protected $primaryKey = 'anuncio_id';
    public $timestamps = true; // we added created_at/updated_at

    protected $fillable = [
        'usuario_id', 'categoria_id',
        'titulo', 'precio', 'estado', 'descripcion', 'fecha_fin',
        'is_canceled',
    ];

    protected $casts = [
        'precio' => 'float',
        'is_canceled' => 'boolean',
        'fecha_fin' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'categoria_id');
    }
}
