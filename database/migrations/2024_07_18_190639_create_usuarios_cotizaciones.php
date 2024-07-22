<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('path');
            $table->integer('cantidad')->default(1)->nullable();
            $table->unsignedInteger('minutos')->nullable();
            $table->decimal('precio')->nullable();
            $table->decimal('total')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_cotizaciones');
    }
};
