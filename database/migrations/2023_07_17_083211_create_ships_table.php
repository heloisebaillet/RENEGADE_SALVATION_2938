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
        Schema::create('ships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('type', 100);
            $table->integer('quantity');
            $table->integer('attacker_id')->nullable();
            $table->integer('defender_id')->nullable();
            $table->integer('attack_points')->nullable();; // Ajout de la colonne attack_points
            $table->integer('defense_points')->nullable();; // Ajout de la colonne defense_points
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
