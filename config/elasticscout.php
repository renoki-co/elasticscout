<?php

return [

    'connection' => [

        /*
        |--------------------------------------------------------------------------
        | Elasticscout Connections
        |--------------------------------------------------------------------------
        |
        | Define your Elasticsearch connection here.
        |
        */

        'hosts' => [
            [
                'host' => env('SCOUT_ELASTICSEARCH_HOST', '127.0.0.1'),
                'port' => env('SCOUT_ELASTICSEARCH_PORT', 9200),
                'scheme' => env('SCOUT_ELASTICSEARCH_SCHEME', null),
                'user' => env('SCOUT_ELASTICSEARCH_USER', null),
                'pass' => env('SCOUT_ELASTICSEARCH_PASSWORD', null),

                'aws_enable' => env('SCOUT_ELASTICSCOUT_AWS_ENABLED', false),
                'aws_region' => env('SCOUT_ELASTICSCOUT_AWS_REGION', 'us-east-1'),
                'aws_key' => env('AWS_ACCESS_KEY_ID', ''),
                'aws_secret' => env('AWS_SECRET_ACCESS_KEY', ''),
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | SSL Verification
        |--------------------------------------------------------------------------
        |
        | If your Elasticsearch instance uses an out-dated or self-signed SSL
        | certificate, you will need to pass in the certificate bundle.  This can
        | either be the path to the certificate file (for self-signed certs), or a
        | package like https://github.com/Kdyby/CurlCaBundle.  See the documentation
        | below for all the details.
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_security.html#_ssl_encryption_2
        |
        | If you are using SSL instances, and the certificates are up-to-date and
        | signed by a public certificate authority, then you can leave this null and
        | just use "https" in the host path(s) above and you should be fine.
        |
        */

        'sslVerification' => null,

        /*
        |--------------------------------------------------------------------------
        | HTTP Retries
        |--------------------------------------------------------------------------
        |
        | By default, the client will retry n times, where n = number of nodes in
        | your cluster. If you would like to disable retries, or change the number,
        | you can do so here.
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_set_retries
        |
        */

        'retries' => null,

        /*
        |--------------------------------------------------------------------------
        | Start Sniffing
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html
        |
        */

        'sniffOnStart' => false,

        /*
        |--------------------------------------------------------------------------
        | HTTP Handler
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_configure_the_http_handler
        | See http://ringphp.readthedocs.org/en/latest/client_handlers.html
        |
        */

        'httpHandler' => null,

        /*
        |--------------------------------------------------------------------------
        | Connection Pooling
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_pool
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_connection_pool.html
        |
        */

        'connectionPool' => null,

        /*
        |--------------------------------------------------------------------------
        | Connection Selection
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_selector
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_selectors.html
        */

        'connectionSelector' => null,

        /*
        |--------------------------------------------------------------------------
        | Serialization
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_serializer
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_serializers.html
        |
        */

        'serializer' => null,

        /*
        |--------------------------------------------------------------------------
        | Connection Factory
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_a_custom_connectionfactory
        |
        */

        'connectionFactory' => null,

        /*
        |--------------------------------------------------------------------------
        | HTTP Endpoint
        |--------------------------------------------------------------------------
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/6.0/_configuration.html#_set_the_endpoint_closure
        |
        */

        'endpoint' => null,

        /*
        |--------------------------------------------------------------------------
        | Namespace Registration
        |--------------------------------------------------------------------------
        |
        | An array of additional namespaces to register.
        | For example: [XPack::Security(), XPack::Watcher()]
        |
        | See https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/ElasticsearchPHP_Endpoints.html#Elasticsearch_ClientBuilderregisterNamespace_registerNamespace
        |
        */

        'namespaces' => [],

    ],

    /*
    |--------------------------------------------------------------------------
    | Indexing Method
    |--------------------------------------------------------------------------
    |
    | To index, you can either index one-by-one, or bulk-index multiple
    | models at once, by chunking.
    |
    | Available classes:
    | "\Rennokki\ElasticScout\Indexers\SimpleIndexer::class" - single index
    | "\Rennokki\ElasticScout\Indexers\MultipleIndexer::class" - bulk index
    |
    */

    'indexer' => \Rennokki\ElasticScout\Indexers\SimpleIndexer::class,

    /*
    |--------------------------------------------------------------------------
    | Sync Mapping on Save
    |--------------------------------------------------------------------------
    |
    | Each time a document is created, updated or deleted, update the mapping
    | attached to the index of the model. This can be used as alternative
    | to the sync index command.
    |
    */

    'sync_mapping_on_save' => env('SCOUT_ELASTICSEARCH_SYNC_MAPPING_ON_SAVE', true),

    /*
    |--------------------------------------------------------------------------
    | Refresh Documents on Save
    |--------------------------------------------------------------------------
    |
    | Elasticsearch "caches" some of the documents and reveals them
    | when it thinks it's the best to. Enabling it will lead to a lot of
    | refreshes that can slow the performance. Recommended to be set "false"
    | if the documents are not needed to be available as soon
    | as they get updated.
    |
    */

    'refresh_document_on_save' => env('SCOUT_ELASTICSEARCH_REFRESH_DOCUMENT_ON_SAVE', true),

];
