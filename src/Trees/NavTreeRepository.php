<?php

namespace Daynnnnn\StatamicDatabase\Trees;

use Statamic\Facades\Blink;

class NavTreeRepository extends TreeRepository
{
    protected $type = 'navigation';

    public function find(string $handle, string $site): ?NavTree {
        return Blink::once("$this->type::$handle::$site", function () use ($handle, $site) {
            if (($model = TreeModel::where('handle', "$this->type::$handle::$site")->first()) == null) {
                return null;
            }
            
            return NavTree::fromModel($model);
        });
    }
}
