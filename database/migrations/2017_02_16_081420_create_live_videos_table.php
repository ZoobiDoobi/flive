<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('live_videos', function(Blueprint $table){
            $table->increments('id');
            $table->string('live_vidoe_id',500);
            $table->string('live_video_name', 500);
            $table->smallInteger('active');
            $table->string('fb_user_id', 500);
            $table->string('fb_page_id' , 500);
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
        Schema::dropIfExists('live_videos');
    }
}
