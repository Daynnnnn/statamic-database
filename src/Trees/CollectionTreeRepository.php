<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Statamic\Facades\Blink;

class CollectionTreeRepository extends TreeRepository
{
    protected $type = 'collection';

    public function find(string $handle, string $site): ?CollectionTree {
        return Blink::once("$this->type::$handle::$site", function () use ($handle, $site) {
            if (($model = TreeModel::where('handle', "$this->type::$handle::$site")->first()) == null) {
                return null;
            }
            
            return CollectionTree::fromModel($model);
        });
    }
}
