<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'categoria_id';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class, 'categoria_id', 'categoria_id');
    }
}
