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
        Schema::create('producto_carrito_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_carrito_id')->constrained('producto_carritos');
            $table->string('nombre');
            $table->string('path');
            $table->softDeletes();
            $table->integer('cantidad')->default(1)->nullable();
            $table->unsignedInteger('minutos')->nullable();
            $table->decimal('precio')->nullable();
            $table->decimal('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_carrito_archivos');
    }
};
