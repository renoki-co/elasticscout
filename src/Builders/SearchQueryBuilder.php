<?php

namespace Rennokki\ElasticScout\Builders;

use Illuminate\Database\Eloquent\Model;
use Rennokki\ElasticScout\SearchRule;

class SearchQueryBuilder extends ElasticsearchBuilder
{
    /**
     * The rules array.
     *
     * @var array
     */
    public $searchRules = [];

    /**
     * SearchQueryBuilder constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $query
     * @param  callable|null  $callback
     * @param  bool  $softDelete
     * @return void
     */
    public function __construct(Model $model, $query, $callback = null, $softDelete = false)
    {
        parent::__construct($model, $callback, $softDelete);

        $this->query = $query;
    }

    /**
     * Add a new rule to the builder.
     *
     * @param  \Rennokki\ElasticScout\SearchRule  $searchRule
     * @return $this
     */
    public function addRule(SearchRule $searchRule)
    {
        $this->searchRules[] = $searchRule;

        return $this;
    }

    /**
     * Add an array of new rules to the builder.
     *
     * @param  array  $searchRules
     * @return $this
     */
    public function addRules(array $searchRules = [])
    {
        foreach ($searchRules as $searchRule) {
            $this->addRule($searchRule);
        }

        return $this;
    }

    /**
     * Set the rules.
     *
     * @param  array  $searchRules
     * @return $this
     */
    public function setRules(array $searchRules = [])
    {
        $this->searchRules = [];

        foreach ($searchRules as $searchRule) {
            $this->addRule($searchRule);
        }

        return $this;
    }
}
