<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSavedCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_saved_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('customer_id');
            $table->string('payment_method');
            $table->string('brand');
            $table->string('last4');
            $table->string('exp_month');
            $table->string('exp_year');
            $table->timestamps();

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
        Schema::dropIfExists('user_saved_cards');
    }
}
