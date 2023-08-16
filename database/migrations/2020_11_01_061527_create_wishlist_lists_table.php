<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishlistListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wishlist_lists', function (Blueprint $table) {
            $table->id();
            $table->enum('list_type', ['hotel'])->default('hotel');
            $table->foreignId('wishlist_id');
            $table->foreignId('user_id');
            $table->foreignId('list_id');
            $table->timestamps();

            $table->foreign('wishlist_id')->references('id')->on('wishlists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            /*ExperienceUnCommentStart
            $table->foreign('list_id')->references('id')->on('hotels')->onDelete('cascade');
            ExperienceUnCommentEnd*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wishlist_lists');
    }
}
