<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturedCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('featured_cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_name', 100);
            $table->string('display_name', 50);
            $table->string('latitude',50);
            $table->string('longitude',50);
            $table->string('place_id',100);
            $table->tinyInteger('order_id');
            $table->string('image',100);
            $table->tinyInteger('upload_driver')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('featured_cities');
    }
}
