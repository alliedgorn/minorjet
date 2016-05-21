<?php namespace Minorjet\Aircraft\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('minorjet_aircraft_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->timestamps();
        });

        Schema::create('minorjet_aircraft_aircrafts_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('aircraft_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['aircraft_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::drop('minorjet_aircraft_categories');
        Schema::drop('minorjet_aircraft_aircrafts_categories');
    }

}
