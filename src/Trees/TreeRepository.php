<?php

namespace Daynnnnn\StatamicDatabase\Trees;

class TreeRepository
{
    protected $type = 'default';

    public function __construct() {}

    public function save($tree)
    {
        $model = $tree->toModel();

        $model->save();

        $tree->model($model->fresh());
    }

    public function delete($tree)
    {
        $tree->toModel()->delete();
    }


    public static function bindings()
    {
        return [];
    }
}
