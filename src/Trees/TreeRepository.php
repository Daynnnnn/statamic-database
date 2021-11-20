<?php

namespace Daynnnnn\StatamicDatabase\Trees;

class TreeRepository
{
    protected $type = 'default';

    public function __construct() {}

    public function save($tree)
    {
        $model = $this->toModel($tree);

        $model->save();
    }

    public function delete($tree)
    {
        $this->toModel($tree)->delete();
    }

    public static function bindings()
    {
        return [];
    }
}
