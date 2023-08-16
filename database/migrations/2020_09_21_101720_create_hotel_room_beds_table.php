<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomBedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_room_beds', function (Blueprint $table) {
            $table->foreignId('room_id');
            $table->foreignId('hotel_id');
            $table->unsignedTinyInteger('bed_room')->default(0);
            $table->foreignId('bed_type_id');
            $table->unsignedTinyInteger('beds');
            
            $table->foreign('room_id')->references('id')->on('hotel_rooms')->onDelete('cascade');
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
        Schema::dropIfExists('hotel_room_beds');
    }
}
