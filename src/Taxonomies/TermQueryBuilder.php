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

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column === 'taxonomy') {
            $this->taxonomies[] = $operator;
        }

        if ($column === 'collection') {
            $this->collections[] = $operator;
        }

        return parent::where($column, $operator, $value);
    }

    public function whereIn($column, $values, $boolean = 'and')
    {
        if (in_array($column, ['taxonomy', 'taxonomies'])) {
            $this->taxonomies = array_merge($this->taxonomies ?? [], $values);
        }

        if (in_array($column, ['collection', 'collections'])) {
            $this->collections = array_merge($this->collections ?? [], $values);
        }

        return parent::whereIn($column, $values);
    }

    public function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
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
}
