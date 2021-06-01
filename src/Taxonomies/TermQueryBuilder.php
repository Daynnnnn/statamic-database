<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Statamic\Taxonomies\TermCollection;
use Statamic\Query\EloquentQueryBuilder;

class TermQueryBuilder extends EloquentQueryBuilder
{

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
        return parent::get($columns);
    }

    public function paginate($perPage = null, $columns = ['*'])
    {
        return parent::paginate($perPage, $columns);
    }

    public function count()
    {
        return parent::count();
    }
}