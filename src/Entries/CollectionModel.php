<?php

namespace Daynnnnn\StatamicDatabase\Entries;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CollectionModel extends Model
{
    protected $guarded = [];

    protected $table = 'collections';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAttribute($key)
    {
        return Arr::get($this->getAttributeValue('data'), $key, parent::getAttribute($key));
    }
}