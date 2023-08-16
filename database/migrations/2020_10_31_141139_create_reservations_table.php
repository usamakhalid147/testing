<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    protected $reservation_status = ['Pending', 'Inquiry','Pre-Accepted','Pre-Approved','Accepted', 'Cancelled', 'Declined', 'Expired', 'Completed','Waiting For Approval'];
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id()->from(10001)->startingValue(10001);
            $table->enum('type',['reservation','inquiry'])->default('reservation');
            $table->string('code',10);
            $table->foreignId('hotel_id');
            $table->foreignId('host_id');
            $table->foreignId('user_id');
            $table->foreignId('special_offer_id')->nullable();
            $table->date('checkin');
            $table->date('checkout');
            $table->string('checkin_at',20);
            $table->string('checkout_at',20);
            $table->unsignedBigInteger('total_rooms');
            $table->unsignedBigInteger('adults');
            $table->unsignedBigInteger('children');
            $table->string('currency_code', 5);
            $table->unsignedBigInteger('total_nights');
            $table->enum('coupon_type',['admin','host','referral'])->nullable();
            $table->string('coupon_code',50);
            $table->decimal('coupon_price');
            $table->decimal('cleaning_fee');
            $table->decimal('service_fee');
            $table->decimal('sub_total');
            $table->decimal('host_fee');
            $table->decimal('service_charge');
            $table->decimal('extra_charges');
            $table->decimal('promotion_amount');
            $table->decimal('property_tax');
            $table->decimal('total')->comment('Guest Total Includes Service fee, discounts and Coupon');
            $table->decimal('security_fee')->comment('Security Fee Can host Claim after checkout');
            $table->boolean('penalty_enabled')->default(0);
            $table->decimal('host_penalty');
            $table->string('payment_currency',5)->nullable();
            $table->string('transaction_id',50);
            $table->enum('payment_method',['paypal','stripe','bank_transfer','pay_at_hotel'])->nullable();
            $table->enum('cancellation_policy', ['flexible', 'moderate', 'strict'])->default('flexible');
            $table->enum('status', $this->reservation_status)->nullable();
            $table->boolean('not_available')->default(0);
            $table->enum('cancelled_by', ['Guest', 'Host'])->nullable();
            $table->text('cancel_reason');
            $table->enum('expired_on', ['Guest', 'Host'])->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('pre_accepted_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreign('host_id')->references('id')->on('users');
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
        Schema::dropIfExists('reservations');
    }
}
