ElasticScout - Elasticsearch Driver for Laravel Scout
================

![](images/elasticscout.png)

![CI](https://github.com/renoki-co/elasticscout/workflows/CI/badge.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/rennokki/elasticscout/v/stable)](https://packagist.org/packages/rennokki/elasticscout)
[![Total Downloads](https://poser.pugx.org/rennokki/elasticscout/downloads)](https://packagist.org/packages/rennokki/elasticscout)
[![Monthly Downloads](https://poser.pugx.org/rennokki/elasticscout/d/monthly)](https://packagist.org/packages/rennokki/elasticscout)
[![codecov](https://codecov.io/gh/renoki-co/elasticscout/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/elasticscout/branch/master)
[![StyleCI](https://github.styleci.io/repos/233681522/shield?branch=master)](https://github.styleci.io/repos/233681522)
[![License](https://poser.pugx.org/rennokki/elasticscout/license)](https://packagist.org/packages/rennokki/elasticscout)

ElasticScout is a Laravel Scout driver that interacts with any Elasticsearch server to bring the full-power of Full-Text Search in your models.

This package was shaped from [Babenko Ivan's Elasticscout Driver repo](https://github.com/babenkoivan/scout-elasticsearch-driver).

## 🤝 Supporting

Renoki Co. on GitHub aims on bringing a lot of open source projects and helpful projects to the world. Developing and maintaining projects everyday is a harsh work and tho, we love it.

If you are using your application in your day-to-day job, on presentation demos, hobby projects or even school projects, spread some kind words about our work or sponsor our work. Kind words will touch our chakras and vibe, while the sponsorships will keep the open source projects alive.

## 🚀 Installation

Install the package using Composer CLI:

```bash
$ composer require rennokki/elasticscout
```

If your Laravel package does not support auto-discovery, add this to your `config/app.php` file:

```php
'providers' => [
    ...
    Rennokki\ElasticScout\ElasticScoutServiceProvider::class,
    ...
];
```

Publish the config files:

```bash
$ php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
$ php artisan vendor:publish --provider="Rennokki\ElasticScout\ElasticScoutServiceProvider"
```

If you wish to access directly the Elasticsearch Client, already set with the configuration of your own, you can do so by adding the facade to `config/app.php`:

```php
'ElasticScout' => Rennokki\ElasticScout\Facades\ElasticClient::class,
```

Then you can access it like you normally would:

```php
// Get all indexes
ElasticScout::indices()->get(['index' => '*']);
```

### Configuring Scout

In your `.env` file, set yout `SCOUT_DRIVER` to `elasticscout`, alongside with Elasticsearch configuration:

```env
SCOUT_DRIVER=elasticscout

SCOUT_ELASTICSEARCH_HOST=localhost
SCOUT_ELASTICSEARCH_PORT=9200
```

### AWS Elasticsearch Service

Amazon Elasticsearch Service works perfectly fine without any additional setup for VPC Clusters. However, it is a bit freaky about Public clusters because it requires IAM authentication.

You will first make sure that you have the right version of `elasticsearch/elasticsearch` package installed.
For instance, for a `7.4` cluster, you should install `elasticsearch/elasticsearch:"7.4.*"`, otherwise you will receive errors in your application.

```bash
$ composer require elasticsearch/elasticsearch:"7.4.*"
```

To find the right package size, check the [Elasticsearch's Version Matrix](https://github.com/elastic/elasticsearch-php#version-matrix).

When requesting data from an AWS Elasticsearch cluster, ElasticScout makes sure your authentication will be passed to the further requests by attaching a handler. All you have to do is to enable the setup, by setting the  `SCOUT_ELASTICSCOUT_AWS_ENABLED` env variable:

```env
AWS_ACCESS_KEY_ID=my_key
AWS_SECRET_ACCESS_KEY=my_secret

SCOUT_ELASTICSCOUT_AWS_ENABLED=true
SCOUT_ELASTICSCOUT_AWS_REGION=us-east-1

SCOUT_ELASTICSEARCH_HOST=search-xxxxxx.es.amazonaws.com
SCOUT_ELASTICSEARCH_PORT=443
SCOUT_ELASTICSEARCH_SCHEME=https
```

Please keep in mind: you do not need user & password for AWS Elasticsearch Service clusters.

## 📄 Indexes

### Creating an index

In Elasticsearch, the Index is the equivalent of a table in MySQL, or a collection in MongoDB. You can create an index class using artisan:

```bash
$ php artisan make:elasticscout:index PostIndex
```

You will have something like this in `app/Indexes/PostIndex.php`:

```php
namespace App\Indexes;

use Rennokki\ElasticScout\Index;
use Rennokki\ElasticScout\Migratable;

class PostIndex extends Index
{
    use Migratable;

    /**
     * The settings applied to this index.
     *
     * @var array
     */
    protected $settings = [
        //
    ];

    /**
     * The mapping for this index.
     *
     * @var array
     */
    protected $mapping = [
        //
    ];
}
```

The key here is that you can set settings and a mapping for each index.
You can find more on Elasticsearch's documentation website about [mappings](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html#_explicit_mappings) and [settings](https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-update-settings.html).

Here's an example on creating a mapping for a field that is [a geo-point datatype](https://www.elastic.co/guide/en/elasticsearch/reference/current/geo-point.html):

```php
class RestaurantIndex extends Index
{
    ...
    protected $mapping = [
        'properties' => [
            'location' => [
                'type' => 'geo_point',
            ],
        ],
    ];
}
```

Here is an example on creating a new analyzer in the `$settings` variable for [a whitespace tokenizer](https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-update-settings.html#update-settings-analysis):

```php
class PostIndex extends Index
{
    ...
    protected settings = [
        'analysis' => [
            'analyzer' => [
                'content' => [
                    'type' => 'custom',
                    'tokenizer' => 'whitespace',
                ],
            ],
        ],
    ];
}
```

If you wish to change the name of the index, you can do so by overriding the `$name` variable:

```php
class PostIndex extends Index
{
    protected $name = 'posts_index_2';
}
```

### Attach the index to a model

All the models that can be searched into should use the `Rennokki\ElasticScout\Searchable` trait and implement the `Rennokki\ElasticScout\Index\HasElasticScoutIndex` interface:

```php
use Rennokki\ElasticScout\Contracts\HasElasticScoutIndex;
use Rennokki\ElasticScout\Searchable;

class Post extends Model implements HasElasticScoutIndex
{
    use Searchable;
}
```

Additionally, the model should also specify the index class:

```php
use App\Indexes\PostIndex;
use Rennokki\ElasticScout\Contracts\HasElasticScoutIndex;
use Rennokki\ElasticScout\Index;
use Rennokki\ElasticScout\Searchable;

class Post extends Model implements HasElasticScoutIndex
{
    use Searchable;

    /**
     * Get the index instance class for Elasticsearch.
     *
     * @return \Rennokki\ElasticScout\Index
     */
    public function getElasticScoutIndex(): Index
    {
        return new PostIndex($this);
    }
}
```

### Publish the index to Elasticsearch

To publish the index to Elasticsearch, you should sync the index:

```bash
$ php artisan elasticscout:index:sync App\\Post
```

Now, each time your model creates,updates or deletes new records, they will be automatically synced to Elasticsearch.

**In case you want to import already-existing data, please use the [scout:import command](https://laravel.com/docs/5.8/scout#batch-import) that is described in the Scout documentation.**

Syncing the index can also be done within your code:

```php
$restaurant = Restaurant::first();

$restaurant->getIndex()->sync(); // returns true/false
```

### Sync indexes automatically with Elasticsearch

A good practice is to keep in sync all your indexes with your models. For example, later on, updating a mapping will require another sync.

In CI/CD pipelines and deployments, this can take more commands to sync them out:

```bash
$ php artisan elasticscout:index:sync App\\Post
$ php artisan elasticscout:index:sync App\\Restaurant
$ php artisan elasticscout:index:sync App\\Book
...
```

For this, ElasticScout comes with a handy `elasticscout:migrate` command that can drop and/or reimport the indexes when needed.

```php
# recreate index and reimport the models from database

$ php artisan elasticscout:migrate App\\Post App\\Restaurant App\\Book --drop --import
```

## 🔍 Search Query

To query data into Elasticsearch, you may use the `search()` method:

```php
Post::search('Laravel')
    ->take(30)
    ->from(10)
    ->get();
```

In case you want just the number of the documents, you can do so:

```php
$posts = Post::search('Lumen')->count();
```

## 🔺 Filter Query

ElasticScout allows you to create a custom query using built-in methods by going through the `elasticsearch()` method.

### Must, Must not, Should, Filter
You can use Elasticsearch's must, must_not, should and filter keys directly in the builder.
Keep in mind that you can chain as many as you want.

```php
Post::elasticsearch()
    ->must(['term' => ['tag' => 'wow']])
    ->should(['term' => ['tag' => 'yay']])
    ->shouldNot(['term' => ['tag' => 'nah']])
    ->filter(['term' => ['tag' => 'wow']])
    ->get();
```

## ⚗️ Query Customizations

You can append data to body or query keys.

```php
// apend to the body payload
Post::elasticsearch()
    ->appendToBody('minimum_should_match', 1)
    ->appendToBody('some_field', ['array' => 'yes'])
    ->get();
```

```php
// append to the query payload
Post::elasticsearch()
    ->appendToQuery('some_field', 'value')
    ->appendToQuery('some_other_field', ['array' => 'yes'])
    ->get();
```

## Wheres

```php
Post::elasticsearch()
    ->where('title.keyword', 'Elasticsearch')
    ->first();
```

```php
Book::elasticsearch()
    ->whereBetween('price', [100, 200])
    ->first();
```

```php
Book::elasticsearch()
    ->whereNotBetween('price', [100, 200])
    ->first();
```

## Whens, Unless, Dynamic Wheres

```php
Book::elasticsearch()
    ->when(true, function ($query) {
        return $query->where('price', 100);
    })->get();
```

```php
Book::elasticsearch()
    ->unless(false, function ($query) {
        return $query->where('price', 100);
    })->get();
```

```php
Book::elasticsearch()
    ->wherePrice(100)
    ->get();

// This is the equivalent.
Book::elasticsearch()
    ->where('price', 100)
    ->get();
```

If the dynamic where contains multiple words, they are split by `snake_case`:

```php
Book::elasticsearch()
    ->whereFanVotes(10)
    ->get();

// This is the same.
Book::elasticsearch()
    ->where('fan_votes', 10)
    ->get();
```

## Regex filters

```php
Post::elasticsearch()
    ->whereRegexp('title.raw', 'A.+')
    ->get();
```

## Existence check

Since Elasticsearch has a NoSQL structure, you should be able to check if a field exists.

```php
Post::elasticsearch()
    ->whereExists('meta')
    ->whereNotExists('new_meta')
    ->get();
```

## Geo-type searches

```php
Restaurant::whereGeoDistance('location', [-70, 40], '1000m')
    ->get();
```

```php
Restaurant::whereGeoBoundingBox(
    'location',
    [
        'top_left' => [-74.1, 40.73],
        'bottom_right' => [-71.12, 40.01],
    ]
)->get();
```

```php
Restaurant::whereGeoPolygon(
    'location',
    [
        [-70, 40], [-80, 30], [-90, 20],
    ]
)->get();
```

```php
Restaurant::whereGeoShape(
    'shape',
    [
        'type' => 'circle',
        'radius' => '1km',
        'coordinates' => [4, 52],
    ],
    'WITHIN'
)->get();
```

## Working with Scopes

Elasticscout also works with scopes that are defined in the main model.

```php
class Restaurant extends Model
{
    public function scopeNearby($query, $lat, $lon, $meters)
    {
        return $query->whereGeoDistance('location', [$lat, $lon], $meters.'m');
    }
}

$nearbyRestaurants =
    Restaurant::search('Dominos')->nearby(45, 35, 1000)->get();
```

## Query Caching

Query-by-query caching is available using [rennokki/laravel-eloquent-query-cache](https://github.com/renoki-co/laravel-eloquent-query-cache). All you have to do is to check the repository on how to use it.

Basically, you can cache requests like so:

```php
$booksByJohnGreen =
    Book::elasticsearch()
        ->cacheFor(now()->addMinutes(60))
        ->where('author', 'John Green')
        ->get();
```

## Elasticsearch Rules

A search rule is a class that can be used on multiple queries, helping you to define custom payload only once. This works only for the Search Query builder.

To create a rule, use the artisan command:

```bash
$ php artisan make:elasticscout:rule NameRule
```

You will get something like this:

```php
namespace App\SearchRules;

use Rennokki\ElasticScout\Builders\SearchQueryBuilder;
use Rennokki\ElasticScout\SearchRule;

class NameRule extends SearchRule
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
            //
        ];
    }
}
```

### Query Payload

Within the `buildQueryPayload()`, you should define the query payload that will take place during the query.

For example, you can get started with some bool query. Details about the bool query you can find [in the Elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html).

```php
class NameRule extends SearchRule
{
    public function buildQueryPayload(SearchQueryBuilder $builder)
    {
        return [
            'must' => [
                'match' => [
                    // access the search phrase from the $builder
                    'name' => $builder->query,
                ],
            ],
        ];
    }
}
```

To apply by default on all search queries, define a `getElasticScoutSearchRules()` method in your model:

```php
use App\SearchRules\NameRule;

class Restaurant extends Model
{
    /**
     * Get the search rules for Elasticsearch.
     *
     * @return array
     */
    public function getElasticScoutSearchRules(): array
    {
        return [
            new NameRule,
        ];
    }
}
```

To apply the rule at the query level, you can call the `->addRule()` method:

```php
use App\SearchRules\NameRule;

Restaurant::search('Dominos')
    ->addRule(new NameRule)
    ->get();
```

You can add multiple rules or set the rules to a specific value:

```php
use App\SearchRules\NameRule;
use App\SearchRules\LocationRule;

Restaurant::search('Dominos')
    ->addRules([
        new NameRule,
        new LocationRule($lat, $lon),
    ])->get();

// The rule that will be aplied will be only LocationRule
Restaurant::search('Dominos')
    ->addRule(new NameRule)
    ->setRules([
        new LocationRule($lat, $lon),
    ])->get();
```

### Highlight Payload
When building the highlight payload, you can pass the array to the `buildHighlightPayload()` method.
More details on highlighting can be found [in the Elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-body.html#request-body-search-highlighting).

```php
class NameRule extends SearchRule
{
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
```

To access the payload, you can use the `$highlight` attribute from the model (or from each model of the final collection).

```php
use App\SearchRules\NameRule;

$restaurant = Restaurant::search('Dominos')->addRule(new NameRule)->first();

$name = $restaurant->elasticsearch_highlights->name;
$nameAsString = $restaurant->elasticsearch_highlights->nameAsString;
```

**In case you need to pass arguments to the rules, you can do so by adding your construct method.**

```php
class NameRule extends SearchRule
{
    protected $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function buildQueryPayload(SearchQueryBuilder $builder)
    {
        // Override the name from the rule construct.
        $name = $this->name ?: $builder->query;

        return [
            'must' => [
                'match' => [
                    'name' => $name,
                ],
            ],
        ];
    }
}

Restaurant::search('Dominos')
    ->addRule(new NameRule('Pizza Hut'))
    ->get();
```

## 🐛 Debugging queries

You can debug by explaining the query.

```php
Restaurant::search('Dominos')->explain();
```

You can see how the payload looks like by calling `getPayload()`.

```php
Restaurant::search('Dominos')->getPayload();
```

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [Ivan Babenko](https://github.com/babenkoivan)
- [All Contributors](../../contributors)
