<?php namespace Minorjet\Aircraft\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateFeaturesTable extends Migration
{

    public function up()
    {
        Schema::create('minorjet_aircraft_features', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->integer('priority')->nullable()->default(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('mirnorjet_aircraft_features');
    }

}
