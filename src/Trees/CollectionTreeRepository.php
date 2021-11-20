<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Statamic\Facades\Blink;
use Statamic\Structures\CollectionTree;

class CollectionTreeRepository extends TreeRepository
{
    protected $type = 'collection';

    public function find(string $handle, string $site): ?CollectionTree {
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

        return (new CollectionTree)
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
            'handle' => "collection::$handle::$site",
        ]);

        $model->data = $tree->fileData();
        $model->save();

        return $model;
    }

}
