<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('full_name',30);
            $table->string('email',30);
            $table->string('phone_number', 20)->nullable();
            $table->string('username',30);
            $table->string('password', 100);
            $table->string('timezone')->default('UTC');
            $table->string('user_language',5)->nullable();
            $table->string('user_currency',5)->nullable();
            $table->boolean('primary')->default(0);
            $table->boolean('status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
