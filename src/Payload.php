<?php

namespace Rennokki\ElasticScout;

use Illuminate\Database\Eloquent\Model;
use Rennokki\ElasticScout\Payloads\DocumentPayload;
use Rennokki\ElasticScout\Payloads\IndexPayload;
use Rennokki\ElasticScout\Payloads\RawPayload;
use Rennokki\ElasticScout\Payloads\TypePayload;

class Payload
{
    /**
     * Initialize a RawPayload instance.
     *
     * @return \Rennokki\ElasticScout\Payloads\RawPayload
     */
    public static function raw(): RawPayload
    {
        return new RawPayload;
    }

    /**
     * Initialize an IndexPayload instance.
     *
     * @param  \Rennokki\ElasticScout\Index  $index
     * @return \Rennokki\ElasticScout\Payloads\IndexPayload
     */
    public static function index(Index $index): IndexPayload
    {
        return new IndexPayload($index);
    }

    /**
     * Initialize a TypePayload instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Rennokki\ElasticScout\Payloads\TypePayload
     */
    public static function type(Model $model): TypePayload
    {
        return new TypePayload($model);
    }

    /**
     * Initialize a DocumentPayload instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Rennokki\ElasticScout\Payloads\DocumentPayload
     */
    public static function document(Model $model): DocumentPayload
    {
        return new DocumentPayload($model);
    }
}
