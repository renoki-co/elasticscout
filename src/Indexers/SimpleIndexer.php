<?php

namespace Rennokki\ElasticScout\Indexers;

use Illuminate\Database\Eloquent\Collection;
use Rennokki\ElasticScout\Contracts\Indexer;
use Rennokki\ElasticScout\Facades\ElasticClient;
use Rennokki\ElasticScout\Payload;

class SimpleIndexer implements Indexer
{
    /**
     * {@inheritdoc}
     */
    public function update(Collection $models)
    {
        $models->each(function ($model) {
            if ($model::usesSoftDelete() && config('scout.soft_delete', false)) {
                $model->pushSoftDeleteMetadata();
            }

            $modelData = array_merge(
                $model->toSearchableArray(),
                $model->scoutMetadata()
            );

            if (empty($modelData)) {
                return true;
            }

            $index = $model->getIndex();

            $payload = Payload::document($model)
                ->set('body', $modelData);

            if ($index->isMigratable()) {
                $payload->withAlias('write');
            }

            if ($documentRefresh = config('elasticscout.refresh_document_on_save')) {
                $payload->set('refresh', $documentRefresh);
            }

            ElasticClient::index($payload->get());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Collection $models)
    {
        $models->each(function ($model) {
            $payload = Payload::document($model)
                ->set('client.ignore', 404)
                ->get();

            ElasticClient::delete($payload);
        });
    }
}
