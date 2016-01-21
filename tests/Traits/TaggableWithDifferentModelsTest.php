<?php namespace Waavi\Tagging\Test\Traits;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\Expense;
use Waavi\Tagging\Test\Post;
use Waavi\Tagging\Test\TestCase;

class TaggableWithDifferentModelsTest extends TestCase
{

    public function setUp()
    {
        parent::setUp(true);
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
        $this->tag1     = Tag::create(['name' => '8 Ball', 'taggable_type' => 'Waavi\Tagging\Test\Post']);
        $this->tag2     = Tag::create(['name' => '9 Ball', 'taggable_type' => 'Waavi\Tagging\Test\Post']);
        $this->tag3     = Tag::create(['name' => '10 Ball', 'taggable_type' => 'Waavi\Tagging\Test\Post']);
        $this->tag4     = Tag::create(['name' => '10 Ball', 'taggable_type' => 'Waavi\Tagging\Test\Expense']);
    }

    /**
     * @test
     */
    public function it_saves_a_tag()
    {
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTag($this->tag1->name)->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(1, $this->post->fresh()->tags->count());
    }

    /**
     * @test
     */
    public function it_saves_a_tag_which_exists_for_other_model()
    {
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTag($this->tag1->name)->save();
        $this->assertEquals(2, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
        $this->assertEquals(1, $this->expense->fresh()->tags->count());
    }

    /**
     * @test
     */
    public function it_saves_a_exists_tag_with_different_format()
    {
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTag('8 ball')->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(1, $this->post->fresh()->tags->count());
    }

    /**
     * @test
     */
    public function it_saves_multiples_tags_with_array()
    {
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        $this->assertEquals(6, Tag::count());
    }

    /**
     * @test
     */
    public function can_remove_a_tag_and_delete_tag_if_count_is_zero()
    {
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagRemoved::class);
        $this->post->removeTag($this->tag1->name)->save();
        foreach (Tag::all() as $tag) {
            $this->assertEquals(1, $tag->count);
        }
        $this->assertEquals(5, Tag::count());
        $this->assertEquals(2, $this->post->fresh()->tags->count());
        $this->assertEquals(2, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
    }

    /**
     * @test
     */
    public function can_remove_all_tags()
    {
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
        $this->assertEquals(3, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagRemoved::class);
        $this->post->removeAllTags()->save();
        $this->assertEquals(3, Tag::count());
        $this->assertEquals(0, $this->post->fresh()->tags->count());
        $this->assertEquals(0, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Post')->count());
        $this->assertEquals(3, Tag::where('taggable_type', 'like', 'Waavi\Tagging\Test\Expense')->count());
    }

    /**
     * @test
     */
    public function when_delete_post_delete_tags_relationship()
    {
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTags($this->tag1->name)->save();
        $this->assertEquals(1, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        \Event::shouldReceive('fire')->once(1)->with(\Waavi\Tagging\Events\TagRemoved::class);
        $this->post->delete();
        Post::withTrashed()->where('id', $this->post->id)->restore();
        $post = Post::find($this->post->id);
        $this->assertEquals(0, $post->tags->count());
    }

    /**
     * @test
     */
    public function when_delete_post_dont_delete_tags_relationship()
    {
        \Config::set('tagging.remove_tags_on_delete', false);
        \Event::shouldReceive('fire')->once()->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->post->addTags($this->tag1->name)->save();
        $this->assertEquals(1, $this->post->fresh()->tags->count());
        \Event::shouldReceive('fire')->times(3)->with(\Waavi\Tagging\Events\TagAdded::class);
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->post->delete();
        Post::withTrashed()->where('id', $this->post->id)->restore();
        $post = Post::find($this->post->id);
        $this->assertEquals(1, $post->tags->count());
    }

    /**
     * @test
     */
    public function all_models_with_any_tags()
    {
        $this->post->addTags($this->tag1->name)->save();
        $this->post2->addTags($this->tag2->name)->save();
        $this->expense->addTags($this->tag1->name)->save();
        $this->expense2->addTags($this->tag3->name)->save();
        $this->assertEquals(2, Post::withAnyTag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->count());
        $this->assertEquals(2, Expense::withAnyTag([$this->tag1->name, $this->tag2->name, $this->tag3->name])->count());
    }

    /**
     * @test
     */
    public function all_models_with_all_tags()
    {
        $this->post->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->post2->addTags($this->tag2->name)->save();
        $this->expense->addTags([$this->tag1->name, $this->tag2->name, $this->tag3->name])->save();
        $this->expense2->addTags($this->tag3->name)->save();
        $this->assertEquals(1, Post::withAllTags([$this->tag1->name, $this->tag2->name])->count());
        $this->assertEquals(1, Expense::withAllTags([$this->tag1->name, $this->tag2->name])->count());
    }
}
