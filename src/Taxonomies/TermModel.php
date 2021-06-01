<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TermModel extends Model
{
    protected $guarded = [];

    protected $table = 'taxonomy_terms';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}