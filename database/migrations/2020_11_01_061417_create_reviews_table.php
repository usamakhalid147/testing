<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id');
            $table->foreignId('hotel_id');
            $table->foreignId('user_from');
            $table->foreignId('user_to');
            $table->enum('review_by', ['guest', 'host']);
            $table->unsignedInteger('rating');
            $table->unsignedInteger('cleanliness')->nullable();
            $table->unsignedInteger('communication')->nullable();
            $table->unsignedInteger('check_in')->nullable();
            $table->unsignedInteger('accuracy')->nullable();
            $table->unsignedInteger('location')->nullable();
            $table->unsignedInteger('value')->nullable();
            $table->text('public_comment');
            $table->text('public_reply');
            $table->text('private_comment');
            $table->boolean('recommend')->default(1);
            $table->text('feedback_to_admin')->nullable();
            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels');
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
        Schema::dropIfExists('reviews');
    }
}
