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
        Schema::create('background_removals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('original_filename');
            $table->string('original_path');
            $table->string('processed_path')->nullable();
            $table->string('mime_type');
            $table->integer('file_size');
            $table->string('replicate_prediction_id')->nullable();
            $table->decimal('processing_cost', 8, 4)->default(0.018); // Cost in dollars
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('replicate_prediction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_removals');
    }
};
