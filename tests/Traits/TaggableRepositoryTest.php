<?php namespace Waavi\Tagging\Test\Traits;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\Post;
use Waavi\Tagging\Test\PostRepository;
use Waavi\Tagging\Test\TestCase;

class TaggableRepositoryTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        \Schema::create('posts', function ($table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('text')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        $this->postRepository = \App::make(PostRepository::class);
        $this->post           = Post::create(['title' => 'David Alcaide win the world pool championship.', 'text' => 'Lorep ipsum...']);
        $this->post2          = Post::create(['title' => 'Jaime Serrano win the world pool championship.', 'text' => 'Lorep ipsum...']);
        $this->tag1           = Tag::create(['name' => '8 Ball']);
        $this->tag2           = Tag::create(['name' => '9 Ball']);
        $this->tag3           = Tag::create(['name' => '10 Ball']);
    }

    /**
     * @test
     */
    public function all_models_with_any_tags()
    {
        $this->post->addTags($this->tag1->name)->save();
        $this->post2->addTags($this->tag2->name)->save();
        $this->assertEquals(2, $this->postRepository->withAnyTag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->count());
    }

    /**
     * @test
     */
    public function all_models_with_all_tags()
    {
        $this->post->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->post2->addTags($this->tag2->name)->save();
        $this->assertEquals(1, $this->postRepository->withAllTags([$this->tag1->name, $this->tag2->name])->count());
    }
}
