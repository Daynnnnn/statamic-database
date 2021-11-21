<?php

namespace Daynnnnn\StatamicDatabase\Navigation;

use Statamic\Facades\Nav as NavFacade;
use Statamic\Structures\Nav as FileNav;

class Nav extends FileNav
{
    protected $model;

    public static function fromModel(NavigationModel $model)
    {
        $data = $model->data;

        return NavFacade::make()
            ->handle($model->handle)
            ->title($data['title'] ?? null)
            ->maxDepth($data['max_depth'] ?? null)
            ->collections($data['collections'] ?? null)
            ->expectsRoot($data['root'] ?? false);
    }

    public function toModel()
    {
        $data = $this->fileData();

        $model = NavigationModel::firstOrNew([
            'handle' => $this->id(),
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

        $this->id($model->id);

        return $this;
    }

    public function lastModified()
    {
        return $this->model->updated_at;
    }
}