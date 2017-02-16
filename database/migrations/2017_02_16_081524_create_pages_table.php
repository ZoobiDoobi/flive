<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('fb_pages' , function(Blueprint $table){
            $table->increments('id');
            $table->string('fb_page_id');
            $table->string('fb_page_name',500);
            $table->smallInteger('active');
            $table->string('fb_user_id',500);
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
        //
        Schema::dropIfExists('fb_pages');
    }
}
