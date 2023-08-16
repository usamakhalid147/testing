<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopularLocalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popular_localities', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('popular_city_id');
            $table->string('name', 50);
            $table->string('address');
            $table->string('place_id');
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->string('country_code', 5);
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            $table->foreign('popular_city_id')->references('id')->on('popular_cities')->onDelete('cascade');
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
        Schema::dropIfExists('popular_localities');
    }
}
