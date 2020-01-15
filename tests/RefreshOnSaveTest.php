<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class RefreshOnSaveTest extends TestCase
{
    public function test_model_gets_indexed_with_refresh_on_save_on()
    {
        config(['elasticscout.refresh_document_on_save' => true]);

        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);

        // The documents wont become available until searchable() hits.
        $restaurant->searchable();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 1);
    }

    public function test_model_does_not_get_indexed_with_refresh_on_save_off()
    {
        config(['elasticscout.refresh_document_on_save' => false]);

        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);

        // The documents will stay hidden even if searchable() hits.
        $restaurant->searchable();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);
    }

    public function test_model_does_not_get_indexed_with_refresh_on_set_to_wait_for()
    {
        config(['elasticscout.refresh_document_on_save' => 'wait_for']);

        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);

        // Hitting searchable will make them available for search.
        $restaurant->searchable();

        $this->assertEquals(Restaurant::elasticsearch()->count(), 1);
    }
}
