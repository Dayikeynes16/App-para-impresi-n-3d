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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre');
            $table->string('path');
            $table->integer('cantidad')->default(1)->nullable();
            $table->unsignedInteger('minutos')->nullable();
            $table->decimal('precio')->nullable();
            $table->unsignedBigInteger('morphable_id');
            $table->string('morphable_type');

        });
    }

    /**0
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
