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
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('credits')->default(0); // Available credits
            $table->integer('total_purchased')->default(0); // Total credits ever purchased
            $table->integer('total_used')->default(0); // Total credits used
            $table->timestamps();

            $table->index('user_id');
        });

        // Add default credits to existing users
        $now = now();
        DB::statement("INSERT INTO user_credits (user_id, credits, created_at, updated_at) SELECT id, 5, '{$now}', '{$now}' FROM users");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};
