<?php

namespace Waavi\Tagging\Test\Traits;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\Expense;
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
            $table->softDeletes();
            $table->timestamps();
        });
        \Schema::create('expenses', function ($table) {
            $table->increments('id');
            $table->string('concept')->nullable();
            $table->integer('price')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        $this->post     = Post::create(['title' => 'David Alcaide win the world pool championship.', 'text' => 'Lorep ipsum...']);
        $this->post2    = Post::create(['title' => 'Jaime Serrano win the world pool championship.', 'text' => 'Lorep ipsum...']);
        $this->expense  = Expense::create(['concept' => 'Lorep ipsum..', 'price' => 10]);
        $this->expense2 = Expense::create(['concept' => 'Lorep ipsum 2..', 'price' => 15]);
        $this->tag1     = Tag::create(['name' => '8 Ball']);
        $this->tag2     = Tag::create(['name' => '9 Ball']);
        $this->tag3     = Tag::create(['name' => '10 Ball']);
    }

    /**
     * @test
     */
    public function add_one_tag_as_string()
    {
        $this->post->tag($this->tag1->name);
        $this->assertEquals($this->tag1->name, $this->post->tagNames);
        $this->assertEquals([$this->tag1->name], $this->post->tagArray);
        $this->assertEquals(1, $this->post->tags->count());
        $this->assertEquals(3, Tag::count());
    }

    /**
     * @test
     */
    public function add_several_tags_as_string()
    {
        $this->post->tag("{$this->tag1->name}, {$this->tag2->name}");
        $this->assertEquals("{$this->tag1->name},{$this->tag2->name}", $this->post->tagNames);
        $this->assertEquals([$this->tag1->name, $this->tag2->name], $this->post->tagArray);
        $this->assertEquals(2, $this->post->tags->count());
        $this->assertEquals(3, Tag::count());
    }

    /**
     * @test
     */
    public function add_several_tags_as_array()
    {
        $this->post->tag([$this->tag1->name, $this->tag2->name]);
        $this->assertEquals("{$this->tag1->name},{$this->tag2->name}", $this->post->tagNames);
        $this->assertEquals([$this->tag1->name, $this->tag2->name], $this->post->tagArray);
        $this->assertEquals(2, $this->post->tags->count());
        $this->assertEquals(3, Tag::count());
    }

    /**
     * @test
     */
    public function add_new_tag()
    {
        $this->post->tag('new TaG');
        $this->assertEquals('new tag', $this->post->tagNames);
        $this->assertEquals(['new tag'], $this->post->tagArray);
        $this->assertEquals(1, $this->post->tags->count());
        $this->assertEquals(4, Tag::count());
    }

    /**
     * @test
     */
    public function tag_doesnt_delete_old_tags()
    {
        $this->post->tag('new TaG');
        $this->post->tag('anoTher TaG');
        $this->assertEquals('new tag,another tag', $this->post->tagNames);
        $this->assertEquals(2, $this->post->tags->count());
    }

    /**
     * @test
     */
    public function retag_deletes_old_tags()
    {
        $this->post->tag('new TaG');
        $this->post->retag('anoTher TaG');
        $this->assertEquals('another tag', $this->post->tagNames);
        $this->assertEquals(1, $this->post->tags->count());
    }

    /**
     * @test
     */
    public function can_remove_one_tag()
    {
        $this->post->tag(['one', 'two', 'three']);
        $this->assertEquals(3, $this->post->tags->count());
        $this->post->untag('two');
        $this->assertEquals(2, $this->post->tags->count());
        $this->assertEquals('one,three', $this->post->tagNames);
    }

    /**
     * @test
     */
    public function can_remove_several_tags()
    {
        $this->post->tag(['one', 'two', 'three']);
        $this->assertEquals(3, $this->post->tags->count());
        $this->post->untag(['two', 'three']);
        $this->assertEquals(1, $this->post->tags->count());
        $this->assertEquals('one', $this->post->tagNames);
    }

    /**
     * @test
     */
    public function can_remove_all_tags()
    {
        $this->post->tag(['one', 'two', 'three']);
        $this->assertEquals(3, $this->post->tags->count());
        $this->post->detag();
        $this->assertEquals(0, $this->post->tags->count());
    }

    /**
     * @test
     */
    public function deletes_tag_relationships_on_delete_if_on_delete_cascade()
    {
        $this->assertEquals(0, \DB::table('tagging_taggables')->count());
        $this->post->tag(['one', 'two']);
        $this->assertEquals(2, \DB::table('tagging_taggables')->count());
        $this->post->delete();
        $this->assertEquals(0, \DB::table('tagging_taggables')->count());
    }

    /**
     * @test
     */
    public function does_not_delete_relationships_on_delete_if_not_on_delete_cascade()
    {
        \Config::set('tagging.on_delete_cascade', false);
        $this->assertEquals(0, \DB::table('tagging_taggables')->count());
        $this->post->tag(['one', 'two']);
        $this->assertEquals(2, \DB::table('tagging_taggables')->count());
        $this->post->delete();
        $this->assertEquals(2, \DB::table('tagging_taggables')->count());
    }

    /**
     * @test
     */
    public function get_all_models_with_any_tags()
    {
        $this->post->tag($this->tag1->name)->save();
        $this->post2->tag($this->tag2->name)->save();
        $this->expense->tag($this->tag1->name)->save();
        $this->expense2->tag($this->tag3->name)->save();
        $this->assertEquals(2, Post::withAnyTag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->count());
        $this->assertEquals(2, Expense::withAnyTag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->count());
    }

    /**
     * @test
     */
    public function get_all_models_with_all_tags()
    {
        $this->post->tag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->post2->tag($this->tag2->name)->save();
        $this->expense->tag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->expense2->tag($this->tag3->name)->save();
        $this->assertEquals(1, Post::withAllTags([$this->tag1->name, $this->tag2->name])->count());
        $this->assertEquals(1, Expense::withAllTags([$this->tag1->name, $this->tag2->name])->count());
    }

    /**
     * @test
     */
    public function get_available_tags()
    {
        $this->assertEquals(0, \DB::table('tagging_taggables')->count());
        $this->post->tag([$this->tag1->name, $this->tag3->name])->save();
        $this->assertEquals(2, \DB::table('tagging_taggables')->count());
        $this->post2->tag($this->tag2->name)->save();
        $this->assertEquals(3, \DB::table('tagging_taggables')->count());
        $this->expense->tag([$this->tag1->name, $this->tag2->name])->save();
        $this->assertEquals(5, \DB::table('tagging_taggables')->count());
        $this->assertEquals([$this->tag3->name, $this->tag1->name, $this->tag2->name], $this->post->availableTags());
        $this->assertEquals([$this->tag1->name, $this->tag2->name], $this->expense->availableTags());
    }
}
