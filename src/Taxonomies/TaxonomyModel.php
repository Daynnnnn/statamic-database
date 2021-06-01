<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TaxonomyModel extends Model
{
    protected $guarded = [];

    protected $table = 'taxonomies';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}