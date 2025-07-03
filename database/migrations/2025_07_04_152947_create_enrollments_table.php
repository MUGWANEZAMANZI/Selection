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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unsignedBigInteger('user_id'); // Authenticated user who enrolled
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Class: A, B, or C
            $table->enum('class', ['A', 'B', 'C']);

            // Payment status
            $table->boolean('has_paid')->default(false);

            // Optional columns
            $table->timestamp('enrolled_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
