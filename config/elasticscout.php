<?php

return [
    /*
     * Define your Elasticsearch connection here.
     */
    'connection' => [
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

        /**
         * SSL.
         *
         * If your Elasticsearch instance uses an out-dated or self-signed SSL
         * certificate, you will need to pass in the certificate bundle.  This can
         * either be the path to the certificate file (for self-signed certs), or a
         * package like https://github.com/Kdyby/CurlCaBundle.  See the documentation
         * below for all the details.
         *
         * If you are using SSL instances, and the certificates are up-to-date and
         * signed by a public certificate authority, then you can leave this null and
         * just use "https" in the host path(s) above and you should be fine.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_security.html#_ssl_encryption_2
         */
        'sslVerification' => null,

        /**
         * Retries.
         *
         * By default, the client will retry n times, where n = number of nodes in
         * your cluster. If you would like to disable retries, or change the number,
         * you can do so here.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_set_retries
         */
        'retries' => null,

        /**
         * The remainder of the configuration options can almost always be left
         * as-is unless you have specific reasons to change them.  Refer to the
         * appropriate sections in the Elasticsearch documentation for what each option
         * does and what values it expects.
         */

        /**
         * Sniff On Start.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html
         */
        'sniffOnStart' => false,

        /**
         * HTTP Handler.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_configure_the_http_handler
         * @see http://ringphp.readthedocs.org/en/latest/client_handlers.html
         */
        'httpHandler' => null,

        /**
         * Connection Pool.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_pool
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_connection_pool.html
         */
        'connectionPool' => null,

        /**
         * Connection Selector.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_selector
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_selectors.html
         */
        'connectionSelector' => null,

        /**
         * Serializer.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_serializer
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_serializers.html
         */
        'serializer' => null,

        /**
         * Connection Factory.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_a_custom_connectionfactory
         */
        'connectionFactory' => null,

        /**
         * Endpoint.
         *
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/6.0/_configuration.html#_set_the_endpoint_closure
         */
        'endpoint' => null,

        /**
         * Register additional namespaces.
         *
         * An array of additional namespaces to register.
         *
         * @example 'namespaces' => [XPack::Security(), XPack::Watcher()]
         * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/ElasticsearchPHP_Endpoints.html#Elasticsearch_ClientBuilderregisterNamespace_registerNamespace
         */
        'namespaces' => [],
    ],

    /*
     * Choose the method of indexing.
     *
     * SimpleIndexer indexes the data document by document.
     * \Rennokki\ElasticScout\Indexers\SimpleIndexer::class
     *
     * MultipleIndexer indexes the data in bulks.
     * \Rennokki\ElasticScout\Indexers\MultipleIndexer::class
     */
    'indexer' => \Rennokki\ElasticScout\Indexers\SimpleIndexer::class,

    /*
     * Each time a document is created, updated or deleted, update the mapping
     * attached to the index of the model.
     */
    'sync_mapping_on_save' => env('SCOUT_ELASTICSEARCH_SYNC_MAPPING_ON_SAVE', true),

    /*
     * Elasticsearch "caches" some of the documents and reveals them
     * when it thinks it's the best to. Enabling it will lead to a lot of
     * refreshes that can slow the performance on production.
     *
     * Recommended for production: false
     * Recommended for local/testing: true
     */
    'refresh_document_on_save' => env('SCOUT_ELASTICSEARCH_REFRESH_DOCUMENT_ON_SAVE', true),
];
