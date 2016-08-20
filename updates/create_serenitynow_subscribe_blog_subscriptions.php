<?php namespace SerenityNow\Subscribe\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSerenitynowSubscribeBlogSubscriptions extends Migration
{
    public function up()
    {
        Schema::create('serenitynow_subscribe_blog_subscriptions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('serenitynow_subscribe_blog_subscriptions');
    }
}
