<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class IndexTest extends TestCase
{
    public function test_create_index()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());

        $this->assertTrue($index->create());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $this->assertEquals([], $index->getRawMapping());
        $this->assertEquals($index->getSettings()['analysis'], $index->getRawSettings()['analysis']);
    }

    public function test_create_alias_on_inexistent_index_should_sync_the_index_and_put_alias()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());

        $this->assertTrue($index->createAlias());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $rawMapping = $index->getRawMapping();
        $rawSettings = $index->getRawSettings();

        $this->assertEquals($index->getMapping()['properties'], $rawMapping['properties']);
        $this->assertEquals($index->getMapping()['_meta'], $rawMapping['_meta']);

        $this->assertEquals($index->getSettings()['analysis'], $rawSettings['analysis']);
    }

    public function test_delete_index()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertTrue($index->create());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $this->assertTrue($index->delete());

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());
    }

    public function test_sync_should_create_index_and_put_mapping_and_settings_if_not_existent()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());

        $this->assertTrue($index->sync());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $rawMapping = $index->getRawMapping();
        $rawSettings = $index->getRawSettings();

        $this->assertEquals($index->getMapping()['properties'], $rawMapping['properties']);
        $this->assertEquals($index->getMapping()['_meta'], $rawMapping['_meta']);

        $this->assertEquals($index->getSettings()['analysis'], $rawSettings['analysis']);
    }

    public function test_sync_mapping()
    {
        $post = factory(Post::class)->make();
        $index = $post->getIndex();

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());

        $this->assertTrue($index->create());
        $this->assertTrue($index->syncMapping());

        $rawMapping = $index->getRawMapping();
        $rawSettings = $index->getRawSettings();

        $this->assertEquals($index->getMapping()['_meta'], $rawMapping['_meta']);

        $this->assertEquals([], $index->getSettings()['analysis'] ?? []);

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());
    }

    public function test_raw_index()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $index->sync();

        $rawIndex = $index->getRaw();

        $this->assertEquals($index->getSettings()['analysis'], $rawIndex['settings']['index']['analysis']);
        $this->assertEquals($index->getMapping(), $rawIndex['mappings']);
    }
}
