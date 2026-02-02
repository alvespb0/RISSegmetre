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
        Schema::create('medicos_laudo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresas_laudo_id')->nullable();
            $table->string('nome');
            $table->string('especialidade');
            $table->string('conselho_classe');
            $table->softDeletes();
            $table->foreign('empresas_laudo_id')->references('id')->on('empresas_laudo')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos_laudo');
    }
};
