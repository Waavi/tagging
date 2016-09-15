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
        $tag->name = '  8 Ball  ';
        $tag->save();
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('8 ball', $tag->name);
        $this->assertEquals('8-ball', $tag->slug);
    }

    /**
     * @test
     */
    public function edit_tag()
    {
        $tag       = Tag::create(['name' => '7-ball']);
        $tag->name = '9 Ball';
        $tag->save();
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('9 ball', $tag->name);
        $this->assertEquals('7-ball', $tag->slug);
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
