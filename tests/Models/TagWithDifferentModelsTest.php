<?php namespace Waavi\Tagging\Test\Models;

use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Test\TestCase;

class TagWithDifferentModelsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp(true);
    }

    /**
     * @test
     */
    public function create_tag_with_differents_models()
    {
        $tag                = new Tag;
        $tag->name          = '8  Ball';
        $tag->taggable_type = 'posts';
        $saved              = $tag->save() ? true : false;
        $this->assertTrue($saved);
        $this->assertEquals(1, Tag::count());
        $this->assertEquals(1, Tag::where('taggable_type', 'like', 'posts')->count());
        $this->assertEquals('8  Ball', $tag->name);
        $this->assertEquals('8-ball', $tag->slug);
        $this->assertEquals('posts', $tag->taggable_type);
        $this->assertEquals(0, $tag->count);
    }
}
