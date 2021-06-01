<?php

namespace Daynnnnn\StatamicDatabase\Entries;

use Illuminate\Support\Collection as IlluminateCollection;
use Statamic\Contracts\Entries\Collection as CollectionContract;
use Statamic\Facades\Blink;
use Statamic\Stache\Repositories\CollectionRepository as StacheRepository;

class CollectionRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            CollectionContract::class => Collection::class,
        ];
    }

    public function all(): IlluminateCollection
    {
        return Blink::once('collections', function () {
            $keys = CollectionModel::get()->map(function ($model) {
                return Collection::fromModel($model);
            });

            return IlluminateCollection::make($keys);
        });
    }

    public function find($handle): ?Collection {
        return Blink::once('collections::'.$handle, function () use ($handle) {
            if (($model = CollectionModel::where('handle', $handle)->first()) == null) {
                return null;
            }

            $collection = Collection::fromModel($model);
            return $collection;
        });
    }

    public function findByHandle($handle): ?Collection
    {
        return $this->find($handle);
    }

    public function save($collection)
    {
        $model = $collection->toModel();

        $model->save();

        $collection->model($model->fresh());
    }

    public function delete($collection)
    {
        $collection->toModel()->delete();
    }

    public function updateEntryUris($collection, $ids = null)
    {
        $query = $collection->queryEntries();

        if ($ids) {
            $query->whereIn('id', $ids);
        }
        
        $query->get()->each(function ($entry) {
            EntryModel::where('id', $entry->id())->update(['uri' => $entry->uri()]);
        });
    }
}