<?php
use App\Models\User;
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
        Schema::create('rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid',100);
            $table->foreignIdFor(User::class)
            ->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->string('planetary_system_name')->nullable();
            $table->boolean('is_defender');
            $table->boolean('is_winner');
            $table->integer('nb_fighter');
            $table->integer('nb_frigate');
            $table->integer('nb_cruiser');
            $table->integer('nb_destroyer');
            $table->integer('nb_round')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
