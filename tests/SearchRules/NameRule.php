<?php

namespace Rennokki\ElasticScout\Tests\SearchRules;

use Rennokki\ElasticScout\Builders\SearchQueryBuilder;
use Rennokki\ElasticScout\SearchRule;

class NameRule extends SearchRule
{
    /**
     * The name that will be used to search within the payload.
     *
     * @var string
     */
    protected $name;

    /**
     * Initialize the rule.
     *
     * @param  string|null  $name
     * @return void
     */
    public function __construct($name = null)
    {
        $this->name = $name;
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
            //
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
            'must' => [
                'match' => [
                    'name' => $this->name ?: $builder->query,
                ],
            ],
        ];
    }
}
