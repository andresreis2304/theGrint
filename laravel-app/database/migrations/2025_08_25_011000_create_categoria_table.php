<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->increments('categoria_id');      // INT UNSIGNED AUTO_INCREMENT (PK)
            $table->string('nombre');               // e.g. Drivers, Irons, etc.
            // match your current defaults
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
