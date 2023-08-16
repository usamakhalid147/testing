<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->enum('list_type', ['room','experience'])->nullable();
            $table->string('code',20);
            $table->enum('type', ['amount','percentage'])->default('amount');
            $table->string('currency_code',5)->nullable();
            $table->unsignedBigInteger('value');
            $table->unsignedBigInteger('min_amount');
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->unsignedInteger('per_list_limit')->default(1);
            $table->boolean('visible_on_public')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(1);
            $table->timestamps();
            
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
        Schema::dropIfExists('coupon_codes');
    }
}
