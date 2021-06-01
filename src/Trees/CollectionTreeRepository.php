<?php

namespace Daynnnnn\StatamicDatabase\Trees;

class CollectionTreeRepository extends TreeRepository
{
    protected $type = 'collection';

    public function find(string $handle, string $site): ?CollectionTree {
        if (($model = TreeModel::where('handle', "$this->type::$handle::$site")->first()) == null) {
            return null;
        }
        
        return CollectionTree::fromModel($model);
    }
}
