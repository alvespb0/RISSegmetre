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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serie_id');
            $table->unsignedBigInteger('medico_id')->nullable();
            $table->text('instance_external_id');
            $table->text('file_uuid');
            $table->text('anamnese')->nullable();
            $table->enum('status', ['pendente', 'laudado', 'rejeitado'])->default('pendente');
            $table->boolean('liberado_tec')->default(false); #liberado pelo técnico para DRA visualizar
            $table->foreign('serie_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('users')->onDelete(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
