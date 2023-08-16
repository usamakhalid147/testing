<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->json('name')->nullable();
            $table->json('custom_name')->nullable();
            $table->json('description')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('hotel_id');
            $table->unsignedInteger('beds')->default(1);
            $table->string('room_size')->nullable();
            $table->string('room_type')->nullable();
            $table->string('bed_type')->nullable();
            $table->enum('room_size_type',['sqft','m2'])->default('sqft');
            $table->string('amenities')->nullable();
            $table->unsignedInteger('number')->default(1);
            $table->unsignedInteger('adults')->default(0);
            $table->unsignedInteger('max_adults')->default(0);
            $table->unsignedInteger('children')->default(0);
            $table->unsignedInteger('max_children')->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('cancellation_policy',['flexible','moderate','strict'])->default('flexible');
            $table->enum('status',['In Progress','Listed', 'Unlisted'])->default('In Progress');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rooms');
    }
}
