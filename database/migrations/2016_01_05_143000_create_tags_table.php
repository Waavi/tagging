<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

class CreateTagsTable extends Migration
{

    public function up()
    {
        Schema::create('tagging_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('name_translation');
            $table->string('slug')->index();
            if (Config::get('tagging.uses_tags_for_different_models')) {
                $table->string('taggable_type', 255)->index();
            }
            $table->integer('count')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('tagging_tags');
    }
}
