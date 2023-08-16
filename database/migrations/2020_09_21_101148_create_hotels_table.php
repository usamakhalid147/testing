<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id()->from(10001);
            $table->foreignId('user_id');
            $table->json('name')->nullable();
            $table->json('description')->nullable();
            $table->json('your_space')->nullable();
            $table->json('interaction_with_guests')->nullable();
            $table->json('your_neighborhood')->nullable();
            $table->json('getting_around')->nullable();
            $table->json('other_things_to_note')->nullable();
            $table->string('star_rating')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('cancel_email')->nullable();
            $table->string('email');
            $table->string('tele_phone_number',20);
            $table->string('extension_number');
            $table->string('fax_number');
            $table->string('website');
            $table->string('logo');
            $table->tinyInteger('upload_driver')->default(0);
            $table->string('contact_no')->nullable();
            $table->string('notice_days')->nullable();
            $table->integer('min_los')->default(1);
            $table->string('max_los')->nullable();
            $table->string('checkin_time',50)->default('flexible');
            $table->string('checkout_time',50)->default('flexible');
            $table->string('currency_code',5);
            $table->foreignId('property_type')->nullable();
            $table->unsignedInteger('parking')->default(0);
            $table->unsignedInteger('breakfast')->default(0);
            $table->unsignedInteger('extra_beds')->default(0);
            $table->unsignedInteger('no_of_extra_beds')->default(0);
            $table->enum('service_charge_type',['fixed','percentage'])->default('fixed');
            $table->decimal('service_charge')->nullable();
            $table->enum('property_tax_type',['fixed','percentage'])->default('fixed');
            $table->decimal('property_tax')->nullable();
            $table->string('amenities')->nullable();
            $table->string('hotel_rules')->nullable();
            $table->binary('hotel_policy');
            $table->string('guest_accesses')->nullable();
            $table->boolean('is_recommended')->default(0);
            $table->boolean('is_top_picks')->default(0);
            $table->enum('status',['In Progress','Completed', 'Pending', 'Resubmit', 'Listed', 'Unlisted'])->default('In Progress')->nullable();
            $table->string('guidance',255);
            $table->enum('admin_status',['Pending', 'Approved','Resubmit'])->default('Pending');
            $table->unsignedBigInteger('admin_commission');
            $table->unsignedDecimal('rating');
            $table->unsignedBigInteger('total_rating');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_type')->references('id')->on('property_types');
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
        Schema::dropIfExists('hotels');
    }
}
