<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id()->from(1);
            $table->foreignId('reservation_id');
            $table->foreignId('hotel_id');
            $table->foreignId('room_id');
            $table->unsignedBigInteger('adults');
            $table->unsignedBigInteger('extra_adults');
            $table->unsignedBigInteger('children');
            $table->unsignedBigInteger('extra_children');
            $table->string('currency_code', 5);
            $table->decimal('day_price');
            $table->decimal('total_days_price');
            $table->decimal('extra_adults_amount');
            $table->decimal('extra_children_amount');
            $table->string('meal_plan')->nullable();
            $table->decimal('meal_plan_amount');
            $table->string('extra_bed')->nullable();
            $table->decimal('extra_bed_amount');
            $table->json('applied_promotions')->nullable();
            $table->decimal('promotion_amount');
            $table->decimal('coupon_price');
            $table->decimal('sub_total');
            $table->decimal('property_tax');
            $table->decimal('service_charge');
            $table->decimal('service_fee');
            $table->decimal('total_price');
            // $table->decimal('host_fee');
            $table->decimal('guest_refund_amount');
            $table->enum('guest_refund_status',['not_applicable','future','processing','completed'])->default('not_applicable');
            $table->decimal('host_payout_amount');
            $table->enum('host_payout_status',['not_applicable','future','processing','completed'])->default('not_applicable');
            $table->json('cancellation_policy')->nullable();
            $table->enum('status', ['Accepted','Cancelled'])->nullable();
            $table->enum('cancelled_by', ['Guest', 'Host'])->nullable();
            $table->text('cancel_reason');
            $table->timestamp('cancelled_at')->nullable();

            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('hotel_rooms');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreign('currency_code')->references('code')->on('currencies');
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
        Schema::dropIfExists('room_reservations');
    }
}
