<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggedTable extends Migration
{

    public function up()
    {
        Schema::create('tagging_taggables', function (Blueprint $table) {
            $table->string('tag_id')->index();
            $table->integer('taggable_id')->unsigned()->index();
            $table->string('taggable_type')->index();
        });
    }

    public function down()
    {
        Schema::drop('tagging_taggables');
    }
}
