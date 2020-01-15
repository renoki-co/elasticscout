<?php

namespace Rennokki\ElasticScout\Tests\Indexes;

use Rennokki\ElasticScout\Index;
use Rennokki\ElasticScout\Migratable;

class RestaurantIndex extends Index
{
    use Migratable;

    /**
     * The settings applied to this index.
     *
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'filter' => [
                'autocomplete_filter' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 1,
                    'max_gram' => 20,
                ],
            ],
            'analyzer' => [
                'autocomplete' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'filter' => ['lowercase', 'autocomplete_filter'],
                ],
            ],
        ],

    ];

    /**
     * The mapping for this index.
     *
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'standard',
            ],
            'location' => [
                'type' => 'geo_point',
            ],
        ],
    ];
}
