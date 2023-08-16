<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_informations', function (Blueprint $table) {
            $table->foreignId('user_id')->unique();
            $table->date('dob')->nullable();
            $table->enum('gender',['Male', 'Female', 'Other'])->nullable();
            $table->text('about')->nullable();
            $table->string('location',50)->nullable();
            $table->string('work',100)->nullable();
            $table->string('languages')->nullable();
            $table->string('address_line_1',100);
            $table->string('address_line_2',100);
            $table->string('city',25)->nullable();
            $table->string('state',20);
            $table->string('country_code', 5);
            $table->string('postal_code',10);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_informations');
    }
}
