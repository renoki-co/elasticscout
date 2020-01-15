<?php

namespace Rennokki\ElasticScout\Tests\SearchRules;

use Rennokki\ElasticScout\Builders\SearchQueryBuilder;
use Rennokki\ElasticScout\SearchRule;

class NameHighlightRule extends SearchRule
{
    /**
     * Initialize the rule.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the highlight payload.
     *
     * @param  SearchQueryBuilder  $builder
     * @return array
     */
    public function buildHighlightPayload(SearchQueryBuilder $builder)
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'plain',
                ],
            ],
        ];
    }

    /**
     * Build the query payload.
     *
     * @param  SearchQueryBuilder  $builder
     * @return array
     */
    public function buildQueryPayload(SearchQueryBuilder $builder)
    {
        return [
            'should' => [
                'match' => [
                    'name' => $builder->query,
                ],
            ],
        ];
    }
}
