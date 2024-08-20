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
        Schema::create('producto_carritos', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('producto_id')->nullable()->constrained('products','id');
            $table->integer('cantidad')->default(1);
            $table->integer('total');
            $table->foreignId('carrito_id')->constrained('carritos','id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_carritos');
    }
};
