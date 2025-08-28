<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anuncio', function (Blueprint $table) {
            $table->increments('anuncio_id'); 

            
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('categoria_id');

            $table->string('titulo', 500);
            $table->decimal('precio', 10, 2);

            
            $table->enum('estado', ['nuevo', 'usado', 'restaurado', 'como_nuevo']);

            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_fin');
            $table->boolean('is_canceled')->default(false);

            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            
            $table->foreign('usuario_id')
                  ->references('usuario_id')->on('usuario');

            $table->foreign('categoria_id')
                  ->references('categoria_id')->on('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anuncio');
    }
};
