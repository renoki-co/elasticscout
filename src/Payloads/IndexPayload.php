<?php

namespace Rennokki\ElasticScout\Payloads;

use Exception;
use Rennokki\ElasticScout\Index;
use Rennokki\ElasticScout\Payloads\Features\HasProtectedKeys;

class IndexPayload extends RawPayload
{
    use HasProtectedKeys;

    /**
     * The protected keys.
     *
     * @var array
     */
    protected $protectedKeys = [
        'index',
    ];

    /**
     * The index.
     *
     * @var \Rennokki\ElasticScout\Index
     */
    protected $index;

    /**
     * IndexPayload constructor.
     *
     * @param \Rennokki\ElasticScout\Index $index
     * @return void
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
        $this->payload['index'] = $index->getName();
    }

    /**
     * Use an alias on the payload.
     *
     * @param  string  $alias
     * @return $this
     * @throws \Exception
     */
    public function withAlias(string $alias)
    {
        $this->payload['index'] = $this->index->getMigratableAlias($alias);

        return $this;
    }
}
