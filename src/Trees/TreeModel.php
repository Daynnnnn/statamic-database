<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Illuminate\Database\Eloquent\Model;
use Statamic\Support\Arr;

class TreeModel extends Model
{
    protected $guarded = [];

    protected $table = 'trees';

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