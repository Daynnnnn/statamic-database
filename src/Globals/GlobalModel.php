<?php

namespace Daynnnnn\StatamicDatabase\Globals;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class GlobalModel extends Model
{
    protected $guarded = [];

    protected $table = 'globals';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}