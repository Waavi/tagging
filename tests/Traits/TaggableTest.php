<?php namespace Waavi\Tagging\Test\Traits;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\Post;
use Waavi\Tagging\Test\TestCase;

class TaggableTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        \Schema::create('posts', function ($table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('text')->nullable();
            $table->timestamps();
        });
        $this->post = Post::create(['title' => 'David Alcaide win the world pool championship.', 'text' => 'Lorep ipsum...']);
        $this->tag1 = Tag::create(['name' => '8 Ball']);
        $this->tag2 = Tag::create(['name' => '9 Ball']);
        $this->tag2 = Tag::create(['name' => '10 Ball']);
    }

    /**
     * @test
     */
    public function it_saves_a_tag()
    {
        return false;
    }
}
