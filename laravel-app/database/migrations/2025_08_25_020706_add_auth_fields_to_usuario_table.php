<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('email')->unique()->after('apellido');
            $table->string('password')->after('email');
            $table->rememberToken()->after('password');
        });
    }
    public function down(): void {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token']);
        });
    }
};

