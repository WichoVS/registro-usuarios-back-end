<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->string('CURP', 18)->primary();
            $table->string('Nombre', 128);
            $table->string('ApellidoPaterno', 64);
            $table->string('ApellidoMaterno', 64);
            $table->date('FechaNacimiento');
            $table->foreignId('EntidadFederativaNacimiento')->references('id')->on('entidades_federativas');
            $table->foreignId('EstadoCivil')->references('id')->on('estados_civiles');
            $table->foreignId('Genero')->references('id')->on('generos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};