<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Statamic\Structures\NavTree as BaseTree;

class NavTree extends BaseTree
{
    protected $model;

    public static function fromModel(TreeModel $model)
    {
        [$type, $handle, $site] = explode('::', $model->handle);

        $tree = new self;
        $tree->handle($handle)
        ->locale($site)
        ->tree($model->data['tree'])
        ->syncOriginal();

        return $tree;
    }

    public function toModel()
    {
        $site = $this->locale();
        $handle = $this->handle();
        $data = $this->fileData();
        
        $model = TreeModel::firstOrNew([
            'handle' => "navigation::$handle::$site",
        ]);

        $model->data = $data;
        $model->save();

        return $model;
    }

    public function model($model = null)
    {
        if (func_num_args() === 0) {
            return $this->model;
        }

        $this->model = $model;

        return $this;
    }

    public function lastModified()
    {
        return $this->model->updated_at;
    }

}
