<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\SearchRules\NameHighlightRule;
use Rennokki\ElasticScout\Tests\SearchRules\NameRule;

class RuleTest extends TestCase
{
    public function test_name_highlight_rule()
    {
        $post = factory(Post::class)->make(['name' => 'How to breathe']);
        $post->getIndex()->sync();
        $post->save();
        $post->searchable();

        $searchResult =
            Post::search('How')
                ->addRule(new NameHighlightRule)
                ->first();

        $this->assertEquals(
            '<em>How</em> to breathe',
            $searchResult->elasticsearch_highlights->nameAsString
        );
    }

    public function test_name_rule()
    {
        $post = factory(Post::class)->make(['name' => 'How to breathe']);
        $post->getIndex()->sync();
        $post->save();
        $post->searchable();

        $searchResultWithoutName =
            Post::search('How to breathe')
                ->addRule(new NameRule)
                ->first();

        $searchResultWithName =
                Post::search('Not important')
                    ->addRule(new NameRule('How to breathe'))
                    ->first();

        $this->assertTrue(
            $searchResultWithoutName->is($searchResultWithName)
        );
    }
}
