<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_room_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id');
            $table->foreignId('room_id');
            $table->enum('type',['early_bird','min_max','day_before_checkin']);
            $table->string('name')->nullable();
            $table->string('currency_code',5);
            $table->enum('value_type',['percentage','fixed']);
            $table->decimal('value');
            $table->integer('days')->default(0);
            $table->integer('min_los')->default(0);
            $table->integer('max_los')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('hotel_room_promotions');
    }
};
