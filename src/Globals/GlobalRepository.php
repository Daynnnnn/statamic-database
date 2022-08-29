<?php

namespace Daynnnnn\StatamicDatabase\Globals;

use Statamic\Stache\Repositories\GlobalRepository as StacheRepository;
use Statamic\Contracts\Globals\GlobalSet as GlobalContract;
use Statamic\Facades\Blink;
use Statamic\Globals\GlobalCollection;
use Statamic\Stache\Stache;

class GlobalRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            GlobalContract::class => GlobalSet::class,
        ];
    }

    public function all(): GlobalCollection
    {
        return Blink::once('globals', function () {
            $keys = GlobalModel::get()->map(function ($model) {
                return GlobalSet::fromModel($model);
            });

            return GlobalCollection::make($keys);
        });
    }

    public function find($set): ?GlobalSet {
        if (($model = GlobalModel::where('handle', $set)->first()) == null) {
            return null;
        }

        $global = GlobalSet::fromModel($model);
        return $global;

    }

    public function findByHandle($handle): ?GlobalSet
    {
        return $this->find($handle);
    }

    public function save($global)
    {
        $model = $global->toModel();

        $model->save();

        $global->model($model->fresh());
    }

    public function delete($global)
    {
        $global->toModel()->delete();
    }
}
