<?php

namespace Daynnnnn\StatamicDatabase;

use Statamic\Contracts\Assets\AssetContainerRepository as AssetContainerRepositoryContract;
use Daynnnnn\StatamicDatabase\Assets\AssetContainerRepository;
use Statamic\Contracts\Entries\CollectionRepository as CollectionRepositoryContract;
use Daynnnnn\StatamicDatabase\Entries\CollectionRepository;
use Statamic\Contracts\Structures\CollectionTreeRepository as CollectionTreeRepositoryContract;
use Daynnnnn\StatamicDatabase\Trees\CollectionTreeRepository;
use Statamic\Contracts\Entries\EntryRepository as EntryRepositoryContract;
use Daynnnnn\StatamicDatabase\Entries\EntryRepository;
use Statamic\Contracts\Globals\GlobalRepository as GlobalRepositoryContract;
use Daynnnnn\StatamicDatabase\Globals\GlobalRepository;
use Statamic\Contracts\Structures\NavigationRepository as NavigationRepositoryRepository;
use Daynnnnn\StatamicDatabase\Navigation\NavigationRepository;
use Statamic\Contracts\Structures\NavTreeRepository as NavTreeRepositoryContract;
use Daynnnnn\StatamicDatabase\Trees\NavTreeRepository;
use Statamic\Contracts\Taxonomies\TaxonomyRepository as TaxonomyRepositoryContract;
use Daynnnnn\StatamicDatabase\Taxonomies\TaxonomyRepository;
use Statamic\Contracts\Taxonomies\TermRepository as TermRepositoryContract;
use Daynnnnn\StatamicDatabase\Taxonomies\TermRepository;

use Illuminate\Support\ServiceProvider;
use Statamic\Statamic;

class StatamicDatabaseServiceProvider extends ServiceProvider
{
    protected $config = false;

    public function register()
    {
        Statamic::repository(AssetContainerRepositoryContract::class, AssetContainerRepository::class);
        Statamic::repository(CollectionRepositoryContract::class, CollectionRepository::class);
        Statamic::repository(CollectionTreeRepositoryContract::class, CollectionTreeRepository::class);
        Statamic::repository(EntryRepositoryContract::class, EntryRepository::class);
        Statamic::repository(GlobalRepositoryContract::class, GlobalRepository::class);
        Statamic::repository(NavigationRepositoryRepository::class, NavigationRepository::class);
        Statamic::repository(NavTreeRepositoryContract::class, NavTreeRepository::class);
        Statamic::repository(TaxonomyRepositoryContract::class, TaxonomyRepository::class);
        Statamic::repository(TermRepositoryContract::class, TermRepository::class);
    }
}
