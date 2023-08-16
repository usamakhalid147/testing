<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->from(10001)->startingValue(10001);
            $table->enum('user_type',['user','host','sub_host'])->default('user');
            $table->unsignedInteger('host_id')->default('0');
            $table->unsignedInteger('role_id')->default('0');
            $table->string('username',50);
            $table->string('title',50);
            $table->string('first_name',30);
            $table->string('last_name',30);
            $table->string('email');
            $table->string('password');
            $table->string('city',50);
            $table->string('country_code',5);
            $table->string('phone_code',5);
            $table->string('phone_number', 20)->nullable();
            $table->string('telephone_number', 20)->nullable();
            $table->string('google_id', 50)->nullable();
            $table->string('facebook_id', 50)->nullable();
            $table->string('apple_id', 100)->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('user_language',5)->nullable();
            $table->string('user_currency',5)->nullable();
            $table->enum('device_type',['web','android','ios'])->default('web')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('src',100)->nullable();
            $table->string('document_src',100)->nullable();
            $table->string('resubmit_reason')->nullable();
            $table->enum('photo_source',['site', 'facebook', 'google'])->default('site');
            $table->tinyInteger('upload_driver')->default(0);
            $table->enum('status',['pending','active', 'inactive','disabled'])->default('pending');
            $table->enum('verification_status',['no','pending','verified', 'resubmit'])->default('no');
            $table->rememberToken();
            $table->timestamp('last_active_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
