<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id()->from(10001)->startingValue(10001);
            $table->enum('list_type', ['hotel'])->default('hotel');
            $table->enum('type', ['reservation', 'admin_resubmit','admin_verify'])->default('reservation');
            $table->foreignId('list_id');
            $table->foreignId('reservation_id')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('host_id')->nullable();
            $table->text('guest_message')->nullable();
            $table->text('host_message')->nullable();
            $table->boolean('guest_archive')->default(0);
            $table->boolean('host_archive')->default(0);
            $table->boolean('guest_star')->default(0);
            $table->boolean('host_star')->default(0);
            $table->boolean('guest_read')->default(0);
            $table->boolean('host_read')->default(0);
            $table->foreignId('special_offer_id')->nullable();
            $table->timestamps();

            /*ExperienceUnCommentStart
            $table->foreign('list_id')->references('id')->on('hotels');
            ExperienceUnCommentEnd*/
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('host_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
