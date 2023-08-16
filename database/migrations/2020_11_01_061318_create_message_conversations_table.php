<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id');
            $table->foreignId('user_from');
            $table->foreignId('user_to');
            $table->unsignedTinyInteger('message_type');
            $table->text('message')->nullable();
            $table->boolean('read')->default(0);
            $table->foreignId('special_offer_id')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('user_from')->references('id')->on('users');
            $table->foreign('user_to')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_conversations');
    }
}
