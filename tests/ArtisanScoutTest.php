<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class ArtisanScoutTest extends TestCase
{
    public function test_scout_import()
    {
        $restaurants = factory(Restaurant::class, 10)->make();
        $restaurants->first()->getIndex()->sync();

        Restaurant::removeAllFromSearch();

        sleep(1);

        $this->assertEquals(0, Restaurant::elasticsearch()->count());

        $this->artisan('scout:import', [
            'model' => Restaurant::class,
        ]);

        $this->assertEquals(
            Restaurant::count(),
            Restaurant::elasticsearch()->count()
        );
    }

    /* public function test_scout_flush()
    {
        factory(Restaurant::class, 10)->make()->each(function (Restaurant $restaurant) {
            $restaurant->getIndex()->sync();
            $restaurant->save();
            $restaurant->searchable();
        });

        $this->assertEquals(10, Restaurant::elasticsearch()->count());

        $this->artisan('scout:flush', [
            'model' => Restaurant::class,
        ]);

        sleep(1);

        $this->assertEquals(
            0,
            Restaurant::elasticsearch()->count()
        );
    } */
}
