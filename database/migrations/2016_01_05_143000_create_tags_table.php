<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration
{

    public function up()
    {
        Schema::create('tagging_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('name_translation')->nullable();
            $table->string('slug')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('tagging_tags');
    }
}
