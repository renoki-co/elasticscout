<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Restaurant;

class ScoutTest extends TestCase
{
    public function test_scout_import()
    {
        $restaurants = factory(Restaurant::class, 10)->make();
        $restaurants->first()->getIndex()->sync();

        $restaurants->unsearchable();

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

    public function test_searchable()
    {
        $restaurants = factory(Restaurant::class, 10)->make();
        $restaurants->first()->getIndex()->sync();

        $restaurants->each(function ($restaurant) {
            $restaurant->save();
            $restaurant->searchable();
        });

        $restaurants->unsearchable();

        sleep(1);

        $this->assertEquals(0, Restaurant::elasticsearch()->count());

        $restaurants->searchable();

        $this->assertEquals(10, Restaurant::elasticsearch()->count());
    }

    public function test_unsearchable()
    {
        $restaurants = factory(Restaurant::class, 10)->make();
        $restaurants->first()->getIndex()->sync();

        $restaurants->each(function ($restaurant) {
            $restaurant->save();
            $restaurant->searchable();
        });

        $this->assertEquals(10, Restaurant::elasticsearch()->count());

        $restaurants->unsearchable();

        sleep(1);

        $this->assertEquals(0, Restaurant::elasticsearch()->count());
    }
}
