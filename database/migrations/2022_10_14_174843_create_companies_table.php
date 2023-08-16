<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('company_name');
            $table->string('company_tax_number');
            $table->string('company_tele_phone_number');
            $table->string('company_fax_number');
            $table->string('address_line_1',100);
            $table->string('address_line_2',100);
            $table->string('city',25);
            $table->string('state',20);
            $table->string('country_code', 5)->nullable();
            $table->string('postal_code',10);
            $table->string('company_website');
            $table->string('company_email');
            $table->string('company_logo');
            $table->tinyInteger('upload_driver')->default(0);
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
        Schema::dropIfExists('companies');
    }
};
