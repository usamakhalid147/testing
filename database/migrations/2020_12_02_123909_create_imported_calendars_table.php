<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportedCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imported_calendars', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ical','google','airbnb'])->default('ical');
            $table->foreignId('user_id');
            $table->foreignId('hotel_id');
            $table->text('url');
            $table->string('name',50);
            $table->dateTime('last_sync');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imported_calendars');
    }
}
