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
        Schema::table('examinations', function (Blueprint $table) {
            // Remove the questions JSON column


            // Add a json_file_path column to store the reference to the JSON file

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            // Remove the json_file_path column
            $table->dropColumn('json_file_path');

            // Add back the questions JSON column
            $table->json('questions');
        });
    }
};
