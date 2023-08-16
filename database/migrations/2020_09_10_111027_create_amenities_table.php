<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmenitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amenity_type_id');
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('image');
            $table->tinyInteger('upload_driver')->default(0);
            $table->enum('list_type',['room','hotel'])->default('hotel');
            $table->boolean('status')->default(1);
            
            $table->foreign('amenity_type_id')->references('id')->on('amenity_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amenities');
    }
}
