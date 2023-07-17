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
        Schema::create('battles', function (Blueprint $table) {
            $table->increments('id')->foreign('battle_history.battle_id');
            $table->integer('ships_id');
            $table->integer('attacker_id');
            $table->integer('defender_id');
            $table->integer('winner_id');
            $table->integer('resources_looted');
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
