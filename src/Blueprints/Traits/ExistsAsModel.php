<?php

namespace Daynnnnn\StatamicDatabase\Blueprints\Traits;

use Daynnnnn\StatamicDatabase\Blueprints\BlueprintModel;

trait ExistsAsModel
{
    public function updateModel($blueprint) {
        $model = BlueprintModel::firstOrNew([
            'handle' => $blueprint->handle(),
            'namespace' => $blueprint->namespace() ?? '',
        ]);
        $model->data = $blueprint->contents();
        $model->save();
    }

    public function deleteModel($blueprint) {
        $model = BlueprintModel::where('namespace', $blueprint->namespace() ?? '')->where('handle', $blueprint->handle())->first();
        $model->delete();
    }
}
