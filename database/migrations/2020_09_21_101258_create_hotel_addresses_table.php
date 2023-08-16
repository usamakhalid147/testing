<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_addresses', function (Blueprint $table) {
            $table->foreignId('hotel_id')->unique();
            $table->string('address_line_1',100);
            $table->string('address_line_2',100);
            $table->string('city',25);
            $table->string('state',20);
            $table->string('country_code', 5);
            $table->string('postal_code',10);
            $table->string('latitude',20);
            $table->string('longitude',20);
            
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('country_code')->references('name')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_addresses');
    }
}
