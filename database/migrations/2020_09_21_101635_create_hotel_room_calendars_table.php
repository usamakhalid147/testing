<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_room_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('hotel_id');
            $table->foreignId('room_id');
            $table->unsignedInteger('number')->nullable();
            $table->enum('source', ['Calendar', 'Reservation','Sync'])->default('Reservation');
            $table->date('reserve_date');
            $table->string('currency_code',5)->nullable();
            $table->integer('price')->nullable();
            $table->string('notes',255)->nullable();
            $table->enum('status',['available', 'not_available'])->default('available');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('hotel_rooms')->onDelete('cascade');
            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_room_calendars');
    }
}
