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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->foreign('battle_history.user_id');
            $table->integer('planetary_system_id')->nullable();
            $table->string('type_user', 50)->nullable();
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->string('username', 50)->unique();
            $table->date('date_of_birth');
            $table->string('picture', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
