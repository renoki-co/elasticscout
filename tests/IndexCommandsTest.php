<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Restaurant;

class IndexCommandsTest extends TestCase
{
    public function test_sync_index_command()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());

        $this->artisan('elasticscout:index:sync', [
            'model' => get_class($restaurant),
        ]);

        $rawMapping = $index->getRawMapping();
        $rawSettings = $index->getRawSettings();

        $this->assertEquals($index->getMapping()['properties'], $rawMapping['properties']);
        $this->assertEquals($index->getMapping()['_meta'], $rawMapping['_meta']);

        $this->assertEquals($index->getSettings()['analysis'], $rawSettings['analysis']);

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());
    }

    public function test_delete_index_command()
    {
        $restaurant = factory(Restaurant::class)->make();
        $index = $restaurant->getIndex();

        $this->assertTrue($index->create());

        $this->assertTrue($index->exists());
        $this->assertTrue($index->hasAlias());

        $this->artisan('elasticscout:index:delete', [
            'model' => get_class($restaurant),
        ]);

        $this->assertFalse($index->exists());
        $this->assertFalse($index->hasAlias());
    }
}
