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
        Schema::create('delivery_protocols', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laudo_id')->unique();
            $table->string('protocolo', 255);
            $table->string('senha');
            $table->string('protocolo_path');
            $table->boolean('visualizado')->default(false);
            $table->dateTime('first_view_at')->nullable();
            $table->dateTime('last_view_at')->nullable();
            $table->foreign('laudo_id')->references('id')->on('series')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_protocol');
    }
};
