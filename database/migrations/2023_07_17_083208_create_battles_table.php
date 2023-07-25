<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attacker_id');
            $table->unsignedBigInteger('defender_id');
            $table->integer('resources_looted');
            $table->timestamps();

            // Ajout des détails des vaisseaux engagés dans le combat pour chaque camp
            $table->json('attacker_ships')->nullable();
            $table->json('defender_ships')->nullable();

            $table->foreign('attacker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('defender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
