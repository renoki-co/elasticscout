<?php

namespace Rennokki\ElasticScout;

use Exception;
use Laravel\Scout\Searchable as SourceSearchable;
use Rennokki\ElasticScout\Builders\ElasticsearchBuilder;
use Rennokki\ElasticScout\Builders\SearchQueryBuilder;
use Rennokki\ElasticScout\Contracts\HasElasticScoutIndex;

trait Searchable
{
    use SourceSearchable {
        SourceSearchable::bootSearchable as sourceBootSearchable;
        SourceSearchable::getScoutKeyName as sourceGetScoutKeyName;
        SourceSearchable::getScoutKey as sourceGetScoutKey;
    }

    /**
     * The highligths.
     *
     * @var \Rennokki\ElasticScout\Highlight|null
     */
    private $elasticsearchHighlights = null;

    /**
     * Defines if the model is searchable.
     *
     * @var bool
     */
    protected static $isSearchableTraitBooted = false;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootSearchable()
    {
        if (static::$isSearchableTraitBooted) {
            return;
        }

        self::sourceBootSearchable();

        static::$isSearchableTraitBooted = true;
    }

    /**
     * Get the index.
     *
     * @return \Rennokki\ElasticScout\Index
     * @throws \Exception
     */
    public function getIndex()
    {
        if (! $this instanceof HasElasticScoutIndex) {
            throw new Exception(sprintf(
                'The model %s does not implement the $s interface.',
                __CLASS__,
                HasElasticScoutIndex::class
            ));
        }

        if (! $this->getElasticScoutIndex()) {
            throw new Exception(sprintf(
                'The model %s has no index set.',
                __CLASS__
            ));
        }

        return $this->getElasticScoutIndex();
    }

    /**
     * Get the search rules. If no rules are set,
     * use the default one that attach the query phrase.
     *
     * @return array
     */
    public function getElasticScoutSearchRules(): array
    {
        return [
            new SearchRule,
        ];
    }

    /**
     * Execute the search.
     *
     * @param  string  $query
     * @param  callable|null  $callback
     * @return \Rennokki\ElasticScout\Builders\SearchQueryBuilder
     */
    public static function search(string $query, $callback = null)
    {
        $softDelete = static::usesSoftDelete() && config('scout.soft_delete', false);

        return new SearchQueryBuilder(new static, $query, $callback, $softDelete);
    }

    /**
     * Enable the communication with the ES Client.
     *
     * @param  callable|null  $callback
     * @return \Rennokki\ElasticScout\Builders\ElasticsearchBuilder
     */
    public static function elasticsearch($callback = null)
    {
        $softDelete = static::usesSoftDelete() && config('scout.soft_delete', false);

        return new ElasticsearchBuilder(new static, $callback, $softDelete);
    }

    /**
     * Set the highlight attribute.
     *
     * @param  \Rennokki\ElasticScout\Highlight  $highlight
     * @return void
     */
    public function setElasticsearchHighlightsAttribute(Highlight $highlight)
    {
        $this->elasticsearchHighlights = $highlight;
    }

    /**
     * Get the highlight attribute.
     *
     * @return \Rennokki\ElasticScout\Highlight|null
     */
    public function getElasticsearchHighlightsAttribute()
    {
        return $this->elasticsearchHighlights;
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getScoutKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->getKey();
    }
}
