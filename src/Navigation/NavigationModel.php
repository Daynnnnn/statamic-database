<?php

namespace Daynnnnn\StatamicDatabase\Navigation;

use Illuminate\Database\Eloquent\Model;
use Statamic\Support\Arr;

class NavigationModel extends Model
{
    protected $guarded = [];

    protected $table = 'navigation';

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