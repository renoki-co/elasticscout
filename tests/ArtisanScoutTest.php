<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class ArtisanScoutTest extends TestCase
{
    public function test_scout_import()
    {
        factory(Restaurant::class, 10)->create();

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

    public function test_scout_flush()
    {
        factory(Restaurant::class, 10)->create();

        $this->assertEquals(10, Restaurant::elasticsearch()->count());

        $this->artisan('scout:flush', [
            'model' => Restaurant::class,
        ]);

        sleep(1);

        $this->assertEquals(
            0,
            Restaurant::elasticsearch()->count()
        );
    }
}
