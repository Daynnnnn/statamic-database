<?php

namespace Daynnnnn\StatamicDatabase\Globals;

use Daynnnnn\StatamicDatabase\Globals\GlobalModel as Model;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet as FileGlobalSet;

class GlobalSet extends FileGlobalSet
{
    protected $model;

    public static function fromModel(Model $model)
    {
        $set = new GlobalSet;
        $set->model = $model->id;
        $set->handle = $model->handle;
        $set->title = $model->data['title'];

        $variables = $model->data['variables'] ?? [Site::selected()->handle() => []];

        foreach ($variables as $key => $variableData) {
            $variable = $set->makeLocalization($key);
            $variable->data($variableData ?? []);
            $set->addLocalization($variable);
        }
        
        return $set;
    }

    public function toModel()
    {

        $data = $this->fileData();

        if ($this->blueprint()) {
            $data['blueprint'] = $this->blueprint();
        }

        $model = GlobalModel::firstOrNew([
            'handle' => $this->id(),
        ]);

        foreach ($this->localizations() as $key => $localization) {
            $data['variables'][$localization->locale()] = $localization->fileData();
        }

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