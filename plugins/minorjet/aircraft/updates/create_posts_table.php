<?php namespace Minorjet\Aircraft\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePostsTable extends Migration
{

    public function up()
    {
        Schema::create('minorjet_aircraft_aircrafts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('title')->nullable();
            $table->string('slug')->index();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->text('secondary_content')->nullable();
            $table->text('secondary_content_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('focused')->default(false);
            $table->boolean('content_ratio')->default(6);
            $table->timestamps();
        });

        Schema::create('minorjet_aircraft_aircrafts_features', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('aircraft_id')->unsigned();
            $table->integer('feature_id')->unsigned();
            $table->primary(['aircraft_id', 'feature_id'], 'minorjet_aircraft_aircrafts_features_id');
        });
    }

    public function down()
    {
        Schema::drop('minorjet_aircraft_aircrafts');
        Schema::drop('minorjet_aircraft_aircrafts_features');
    }

}
