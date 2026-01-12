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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('study_id');
            $table->unsignedBigInteger('medico_id')->nullable();
            $table->text('serie_external_id');
            $table->enum('modality', ['DX', 'CR', 'CT']);
            $table->text('body_part_examined')->nullable();
            $table->text('laudo')->nullable();
            $table->text('laudo_path')->nullable();
            $table->boolean('laudo_assinado')->default(false);
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('users')->onDelete(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
