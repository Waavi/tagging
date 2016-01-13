<?php namespace Waavi\Tagging\Test\Models;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\TestCase;

class TagTest extends TestCase
{

    /**
     * @test
     */
    public function create_tag()
    {
        $tag       = new Tag;
        $tag->name = '8  Ball';
        $saved     = $tag->save() ? true : false;
        $this->assertTrue($saved);
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('8  Ball', $tag->name);
        $this->assertEquals('8-ball', $tag->slug);
        $this->assertEquals(0, $tag->count);
    }

    /**
     * @test
     */
    public function edit_tag()
    {
        $tag       = Tag::create(['name' => '7-ball']);
        $tag->name = '9 Ball';
        $saved     = $tag->save() ? true : false;
        $this->assertTrue($saved);
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('9 Ball', $tag->name);
        $this->assertEquals('7-ball', $tag->slug);
    }

    /**
     * @test
     */
    public function increment_and_decrement_tag()
    {
        $tag = Tag::create(['name' => '9 Ball']);
        $this->assertEquals(0, $tag->count);
        $tag->increment('count', 1);
        $this->assertEquals(1, Tag::first()->count);
        $tag->decrement('count', 1);
        $this->assertEquals(0, Tag::first()->count);
    }

    /**
     * @test
     */
    public function delete_tag()
    {
        $tag = Tag::create(['name' => '9 Ball']);
        $this->assertEquals(1, Tag::count());
        $tag->delete();
        $this->assertEquals(0, Tag::count());
    }
}
