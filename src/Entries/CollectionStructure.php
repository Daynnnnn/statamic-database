<?php

namespace Daynnnnn\StatamicDatabase\Entries;

use Daynnnnn\StatamicDatabase\Trees\CollectionTree;
use Statamic\Structures\CollectionStructure as BaseCollectionStructure;

class CollectionStructure extends BaseCollectionStructure
{
    public function newTreeInstance()
    {
        return new CollectionTree;
    }
}
