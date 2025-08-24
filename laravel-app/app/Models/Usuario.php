<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false; // nombre, apellido only (no created_at/updated_at)

    protected $fillable = ['nombre', 'apellido', 'api_token'];

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class, 'usuario_id', 'usuario_id');
    }
}