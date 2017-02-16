<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('comments', function(Blueprint $table){
            $table->increments('id');
            $table->string('comment_id',500);
            $table->string('comment_body',1000);
            $table->string('comment_author_id' , 500); //Maybe we need these columns in future
            $table->string('comment_author_name');
            $table->integer('keyword_id');
            $table->integer('campaign_id');
            $table->smallInteger('active');
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
        Schema::dropIfExists('comments');
    }
}
