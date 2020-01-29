<?php

namespace Rennokki\ElasticScout\Tests;

use Illuminate\Support\Facades\Cache;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class CacheTest extends TestCase
{
    public function test_get_cache()
    {
        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        $query = Restaurant::elasticsearch()->cacheFor(3600);
        $hash = $query->generateCacheKey();

        $query->get();

        $this->assertNotNull(
            Cache::get("leqc:{$hash}")
        );
    }

    public function test_first_cache()
    {
        $restaurant = factory(Restaurant::class)->make();

        $restaurant->getIndex()->sync();
        $restaurant->save();

        $query = Restaurant::elasticsearch()->cacheFor(3600);
        $hash = $query->generateCacheKey();

        $query->first();

        $this->assertNotNull(
            Cache::get("leqc:{$hash}")
        );
    }
}
