<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->index();
            $table->unsignedBigInteger('question_id')->index();
            $table->unsignedBigInteger('answer_id')->index()->nullable();
            $table->text('value')->nullable();
            $table->json('sentiment');
            $table->timestamps();

            $table->foreign('session_id')
                ->references('id')
                ->on('sessions')
                ->onDelete('cascade');

            $table->foreign('question_id')
                ->references('id')
                ->on('questions')
                ->onDelete('cascade');

            $table->foreign('answer_id')
                ->references('id')
                ->on('answers')
                ->onDelete('cascade');

            $table->primary(['session_id', 'question_id', 'answer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responses');
    }
}
