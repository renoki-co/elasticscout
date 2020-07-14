<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;

class MigrationTest extends TestCase
{
    public function test_migrate_model_index()
    {
        $this->artisan('elasticscout:migrate', [
            '--drop' => true,
            'models' => [Post::class],
        ]);

        $this->assertTrue(
            (new Post)->getIndex()->exists()
        );
    }

    public function test_migrate_model_with_reimport()
    {
        $post = factory(Post::class)->make();
        $post->getIndex()->sync();
        $post->save();
        $post->searchable();

        $this->assertEquals(
            1, Post::search('*')->count()
        );

        $post->unsearchable();

        sleep(2);

        $this->assertEquals(
            0, Post::search('*')->count()
        );

        $this->artisan('elasticscout:migrate', [
            '--drop' => true,
            '--import' => true,
            'models' => [Post::class],
        ]);

        $this->assertEquals(
            1, Post::search('*')->count()
        );

        $this->assertTrue(
            (new Post)->getIndex()->exists()
        );
    }
}
