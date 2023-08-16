<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 5)->unique();
            $table->string('full_name', 50);
            $table->string('iso3', 5)->nullable();
            $table->unsignedInteger('numcode')->length(3);
            $table->unsignedInteger('phone_code')->length(5);
            $table->string('currency_code',5)->nullable();
            $table->string('language_code',5)->default('en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
