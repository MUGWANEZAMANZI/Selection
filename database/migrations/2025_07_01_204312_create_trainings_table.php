<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('trainings', function (Blueprint $table) {
        $table->id(); // training_id
        $table->string('name');
        $table->decimal('fees', 10, 2); // price in RWF
        $table->string('duration'); // e.g. "3 months", "6 weeks"
        $table->string('description')->nullable(); // optional description
        $table->string('class'); // e.g. "Cybersecurity", "Programming"
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
