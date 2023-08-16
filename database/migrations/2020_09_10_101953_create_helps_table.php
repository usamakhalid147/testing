<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id');
            $table->string('slug');
            $table->json('title');
            $table->json('content');
            $table->string('tags');
            $table->boolean('is_recommended')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('help_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('helps');
    }
}
