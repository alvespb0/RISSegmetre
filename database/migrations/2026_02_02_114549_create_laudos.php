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
        Schema::create('laudos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serie_id');
            $table->unsignedBigInteger('empresa_id')->nullable(); # se não for laudado externamente, não vai receber empresa_id
            $table->unsignedBigInteger('medico_id');
            $table->text('laudo')->nullable();
            $table->text('laudo_path')->nullable();
            $table->boolean('laudo_assinado')->default(false);
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->foreign('serie_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('empresa_id')->references('id')->on('empresas_laudo')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('medicos_laudo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laudos');
    }
};
