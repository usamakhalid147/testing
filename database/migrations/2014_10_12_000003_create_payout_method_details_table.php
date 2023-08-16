<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutMethodDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_method_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_method_id');
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city',30)->nullable();
            $table->string('state',30)->nullable();
            $table->string('postal_code',10)->nullable();
            $table->string('country_code',5)->nullable();
            $table->string('payout_id');
            $table->string('currency_code',5);
            $table->string('routing_number', 25)->nullable();
            $table->string('account_number', 30)->nullable();
            $table->string('ssn_last_4',10)->nullable();
            $table->string('holder_name', 50);
            $table->string('document_id',50)->nullable();
            $table->string('document_path',100)->nullable();
            $table->string('additional_document_id',50)->nullable();
            $table->string('additional_document_path',100)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('address_kanji')->nullable();
            $table->string('bank_name',30)->nullable();
            $table->string('bank_location',100)->nullable();
            $table->string('branch_name',30)->nullable();
            $table->string('branch_code',20)->nullable();
            $table->timestamps();

            $table->foreign('payout_method_id')->references('id')->on('payout_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_method_details');
    }
}
