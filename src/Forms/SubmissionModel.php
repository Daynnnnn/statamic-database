<?php

namespace Daynnnnn\StatamicDatabase\Forms;

use Illuminate\Database\Eloquent\Model;

class SubmissionModel extends Model
{
    protected $guarded = [];

    protected $table = 'form_submissions';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}