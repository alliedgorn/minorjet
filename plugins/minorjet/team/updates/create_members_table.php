<?php namespace Minorjet\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateMembersTable extends Migration
{

    public function up()
    {
        Schema::create('minorjet_team_members', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('firstname')->nullable();
            $table->string('lastname')->index();
            $table->text('position')->nullable();
            $table->integer('priority')->unsigned()->default(0);
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('minorjet_team_members');
    }

}
