<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Statamic\Facades\Blink;
use Statamic\Structures\NavTree;

class NavTreeRepository extends TreeRepository
{
    protected $type = 'navigation';

    public function find(string $handle, string $site): ?NavTree {
        return Blink::once("$this->type::$handle::$site", function () use ($handle, $site) {
            if (($model = TreeModel::where('handle', "$this->type::$handle::$site")->first()) == null) {
                return null;
            }
            
            return self::fromModel($model);
        });
    }

    protected static function fromModel(TreeModel $model)
    {
        [$type, $handle, $site] = explode('::', $model->handle);

        return (new NavTree)
            ->handle($handle)
            ->locale($site)
            ->tree($model->data['tree'])
            ->syncOriginal();
    }

    protected function toModel($tree)
    {
        $site = $tree->locale();
        $handle = $tree->handle();
        
        $model = TreeModel::firstOrNew([
            'handle' => "navigation::$handle::$site",
        ]);

        $model->data = $tree->fileData();
        $model->save();

        return $model;
    }
}
