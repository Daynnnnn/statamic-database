<?php

namespace Daynnnnn\StatamicDatabase\Forms;

use Illuminate\Database\Eloquent\Model;

class FormModel extends Model
{
    protected $guarded = [];

    protected $table = 'forms';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}