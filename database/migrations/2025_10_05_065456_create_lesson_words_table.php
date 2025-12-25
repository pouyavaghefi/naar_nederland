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
        Schema::create('lesson_words', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('word_lesson_id');
            $table->foreign('word_lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            // Word fields
            $table->string('nl_word');       // e.g. "Bier"
            $table->string('article')->nullable(); // e.g. "das"
            $table->string('nl_synonym')->nullable(); // e.g. "Getränk"
            $table->string('en_word')->nullable();    // English translation
            $table->string('pronunciation')->nullable(); // e.g. "/biːɐ̯/"
            $table->string('fa_meaning')->nullable(); // Persian translation

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_words');
    }
};
