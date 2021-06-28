<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Statamic\Facades;
use Statamic\Taxonomies\TermCollection;
use Statamic\Query\EloquentQueryBuilder;

class TermQueryBuilder extends EloquentQueryBuilder
{
    protected $taxonomies;
    protected $collections;

    protected $columns = [
        'id', 'taxonomy', 'slug', 'data', 'created_at', 'updated_at',
    ];

    protected function transform($items, $columns = [])
    {
        return TermCollection::make($items)->map(function ($model) {
            return Term::fromModel($model);
        });
    }

    protected function column($column)
    {
        if (! in_array($column, $this->columns)) {
            $column = 'data->'.$column;
        }

        return $column;
    }

    public function get($columns = ['*'])
    {
        $items = parent::get($columns);

        // If a single collection has been queried, we'll supply it to the terms so
        // things like URLs will be scoped to the collection. We can't do it when
        // multiple collections are queried because it would be ambiguous.
        if ($this->collections && count($this->collections) == 1) {
            $items->each->collection(Facades\Collection::findByHandle($this->collections[0]));
        }

        return $items;
    }

    public function where($column, $operator = null, $value = null)
    {
        if ($column === 'taxonomy') {
            $this->taxonomies[] = $operator;
        }

        if ($column === 'collection') {
            $this->collections[] = $operator;
        }

        return parent::where($column, $operator, $value);
    }

    public function whereIn($column, $values)
    {
        if (in_array($column, ['taxonomy', 'taxonomies'])) {
            $this->taxonomies = array_merge($this->taxonomies ?? [], $values);
        }

        if (in_array($column, ['collection', 'collections'])) {
            $this->collections = array_merge($this->collections ?? [], $values);
        }

        return parent::whereIn($column, $values);
    }

    public function paginate($perPage = null, $columns = ['*'])
    {
        return parent::paginate($perPage, $columns);
    }

    public function count()
    {
        return parent::count();
    }

    protected function collect($items = [])
    {
        return TermCollection::make($items);
    }

    protected function getFilteredKeys()
    {
        $taxonomies = empty($this->taxonomies)
            ? Facades\Taxonomy::handles()
            : $this->taxonomies;

        if ($this->collections) {
            $this->filterUsagesWithinCollections($taxonomies);
        }

        $keys = empty($this->wheres)
            ? $this->getKeysFromTaxonomies($taxonomies)
            : $this->getKeysFromTaxonomiesWithWheres($taxonomies, $this->wheres);

        return $keys->unique(function ($key) {
            return collect(explode('::', $key))->forget(1)->join('::');
        });
    }

    protected function getKeysFromTaxonomies($taxonomies)
    {
        return collect($taxonomies)->flatMap(function ($taxonomy) {
            $store = $this->store->store($taxonomy);

            return collect($store->index('title')->keys())->map(function ($key) use ($taxonomy) {
                return "{$taxonomy}::{$key}";
            });
        });
    }

    protected function getKeysFromTaxonomiesWithWheres($taxonomies, $wheres)
    {
        return collect($wheres)->reduce(function ($ids, $where) use ($taxonomies) {
            // Get a single array comprised of the items from the same index across all taxonomies.
            $items = collect($taxonomies)->flatMap(function ($taxonomy) use ($where) {
                return $this->store->store($taxonomy)
                    ->index($where['column'])->items()
                    ->mapWithKeys(function ($item, $key) use ($taxonomy) {
                        return ["{$taxonomy}::{$key}" => $item];
                    });
            });

            // Perform the filtering, and get the keys (the references, we don't care about the values).
            $method = 'filterWhere'.$where['type'];
            $keys = $this->{$method}($items, $where)->keys();

            // Continue intersecting the keys across the where clauses.
            // If a key exists in the reduced array but not in the current iteration, it should be removed.
            // On the first iteration, there's nothing to intersect, so just use the result as a starting point.
            return $ids ? $ids->intersect($keys)->values() : $keys;
        });
    }

    protected function getOrderKeyValuesByIndex()
    {
        $taxonomies = empty($this->taxonomies)
            ? Facades\Taxonomy::handles()
            : $this->taxonomies;

        // First, we'll get the values from each index grouped by taxonomy
        $keys = collect($taxonomies)->map(function ($taxonomy) {
            $store = $this->store->store($taxonomy);

            return collect($this->orderBys)->mapWithKeys(function ($orderBy) use ($taxonomy, $store) {
                $items = $store->index($orderBy->sort)
                    ->items()
                    ->mapWithKeys(function ($item, $key) use ($taxonomy) {
                        return ["{$taxonomy}::{$key}" => $item];
                    })->all();

                return [$orderBy->sort => $items];
            });
        });

        // Then, we'll merge all the corresponding index values together from each taxonomy.
        return $keys->reduce(function ($carry, $taxonomy) {
            foreach ($taxonomy as $sort => $values) {
                $carry[$sort] = array_merge($carry[$sort] ?? [], $values);
            }

            return $carry;
        }, collect());
    }
    
    private function filterUsagesWithinCollections($taxonomies)
    {
        $this->whereIn('id', collect($taxonomies)->flatMap(function ($taxonomy) {
            return $this->store->store($taxonomy)
                ->index('associations')
                ->items()->whereIn('collection', $this->collections)
                ->map(function ($item) use ($taxonomy) {
                    return $taxonomy.'::'.$item['slug'];
                });
        })->all());
    }
}
