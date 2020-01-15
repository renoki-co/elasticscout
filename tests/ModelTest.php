<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class ModelTest extends TestCase
{
    public function test_model_gets_indexed_with_refresh_on_save_on()
    {
        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        sleep(2);

        $this->assertEquals(Restaurant::search('*')->count(), 1);
        $this->assertEquals(Restaurant::elasticsearch()->count(), 1);
    }

    public function test_model_does_not_get_indexed_with_refresh_on_save_on()
    {
        config(['elasticscout.refresh_document_on_save' => false]);

        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        sleep(2);

        $this->assertEquals(Restaurant::search('*')->count(), 0);
        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);
    }

    public function test_model_does_not_get_indexed_with_refresh_on_set_to_wait_for()
    {
        config(['elasticscout.refresh_document_on_save' => 'wait_for']);

        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        sleep(2);

        $this->assertEquals(Restaurant::search('*')->count(), 0);
        $this->assertEquals(Restaurant::elasticsearch()->count(), 0);
    }
}
