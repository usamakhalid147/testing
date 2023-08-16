<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopularCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popular_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('address');
            $table->string('place_id');
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->text('viewport');
            $table->string('country_code', 5);
            $table->boolean('status')->default(1);
            $table->timestamps();

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
        Schema::dropIfExists('popular_cities');
    }
}
