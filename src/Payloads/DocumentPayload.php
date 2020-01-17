<?php

namespace Rennokki\ElasticScout\Payloads;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Rennokki\ElasticScout\Searchable;

class DocumentPayload extends TypePayload
{
    /**
     * DocumentPayload constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     * @throws \Exception
     */
    public function __construct(Model $model)
    {
        if (! in_array(Searchable::class, class_uses_recursive($model))) {
            throw new Exception(sprintf(
                'The %s model must use the %s trait.',
                get_class($model),
                Searchable::class
            ));
        }

        if ($model->getScoutKey() === null) {
            throw new Exception(sprintf(
                'The key value must be set to construct a payload for the %s instance.',
                get_class($model)
            ));
        }

        parent::__construct($model);

        $this->payload['id'] = $model->getScoutKey();
        $this->protectedKeys[] = 'id';
    }
}
