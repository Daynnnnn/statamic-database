<?php

namespace Daynnnnn\StatamicDatabase\Trees;

class NavTreeRepository extends TreeRepository
{
    protected $type = 'navigation';

    public function find(string $handle, string $site): ?NavTree {
        if (($model = TreeModel::where('handle', "$this->type::$handle::$site")->first()) == null) {
            return null;
        }
        
        return NavTree::fromModel($model);
    }
}
