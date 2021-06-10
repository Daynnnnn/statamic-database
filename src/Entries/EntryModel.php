<?php

namespace Daynnnnn\StatamicDatabase\Entries;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EntryModel extends Eloquent
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $table = 'entries';

    protected $casts = [
        'date' => 'datetime',
        'data' => 'json',
        'published' => 'bool',
    ];

    public function origin()
    {
        return $this->belongsTo(self::class);
    }

    public function getAttribute($key)
    {
        return Arr::get($this->getAttributeValue('data'), $key, parent::getAttribute($key));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            $entry->{$entry->getKeyName()} = (string) Str::uuid();
        });
    }
}