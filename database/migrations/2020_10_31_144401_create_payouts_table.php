<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->enum('list_type', ['hotel'])->default('hotel');
            $table->foreignId('reservation_id');
            $table->foreignId('user_id');
            $table->foreignId('list_id');
            $table->string('transaction_id',50);
            $table->enum('user_type',['Guest','Host']);
            $table->string('payout_account',50);
            $table->string('currency_code',5);
            $table->decimal('amount');
            $table->decimal('penalty');
            $table->enum('status',['Future','Processing','Completed'])->default("Future");
            $table->timestamps();

            /*ExperienceUnCommentStart
            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->foreign('list_id')->references('id')->on('hotels');
            ExperienceUnCommentEnd*/
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('payouts');
    }
}
