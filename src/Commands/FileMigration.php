<?php

namespace App\Console\Commands;

use Daynnnnn\StatamicDatabase;
use Illuminate\Console\Command;
use Statamic\Contracts;
use Statamic\Facades\YAML;
use Statamic\Stache\Repositories as FileRepositories;
use Statamic\Statamic;
use Statamic\Support\Arr;

class FileMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statamic-database:file-migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->migrateAssetContainers();
        $this->migrateCollections();
        $this->migrateEntries();
        $this->migrateForms();
        $this->migrateGlobals();
        $this->migrateNavigation();
        $this->migrateTaxonomies();
        return 0;
    }

    private function migrateAssetContainers()
    {
        Statamic::repository(Contracts\Assets\AssetContainerRepository::class, FileRepositories\AssetContainerRepository::class);

        $assetContainers = \Statamic\Facades\AssetContainer::all();

        foreach($assetContainers as $assetContainer) {
            $model = StatamicDatabase\Assets\AssetContainerModel::firstOrNew([
                'handle' => $assetContainer->handle()
            ]);
            $model->data = $assetContainer->fileData();
            $model->save();

            $assets = $assetContainer->assets();

            foreach ($assets as $key => $asset) {
                $model = StatamicDatabase\Assets\AssetModel::firstOrNew([
                    'handle' => $assetContainer->handle() . '::' . $asset->metaPath()
                ]);
                $model->data = $asset->meta();
                $model->save();
            }
        }
    }

    private function migrateCollections()
    {
        Statamic::repository(Contracts\Entries\CollectionRepository::class, FileRepositories\CollectionRepository::class);
        
        $collections = \Statamic\Facades\Collection::all();

        foreach ($collections as $collection) {
            $model = StatamicDatabase\Entries\CollectionModel::firstOrNew([
                'handle' => $collection->handle()
            ]);

            $model->data = $collection->fileData();

            $model->save();
            foreach (config('statamic.sites.sites') as $key => $site) {
                if (is_file(base_path('content/trees/collections/'.$key.'/'.$collection->handle().'.yaml'))) {
                    $model = StatamicDatabase\Trees\TreeModel::firstOrNew([
                        'handle' => 'collection::' . $collection->handle() . '::'.$key
                    ]);
                    $fileData = YAML::file(base_path('content/trees/collections/'.$key.'/'.$collection->handle().'.yaml'))->parse();
                    $model->data = $fileData;
                    $model->save();
                }
            }
        }
    }

    private function migrateEntries()
    {
        Statamic::repository(Contracts\Entries\EntryRepository::class, FileRepositories\EntryRepository::class);
        $entries = \Statamic\Facades\Entry::all();

        $entries->each(function ($entry) {
            $model = new StatamicDatabase\Entries\EntryModel([
                'id' => $entry->id(),
                'origin_id' => optional($entry->origin())->id(),
                'site' => $entry->locale(),
                'slug' => $entry->slug(),
                'uri' => $entry->uri(),
                'date' => $entry->hasDate() ? $entry->date() : null,
                'collection' => $entry->collectionHandle(),
                'data' => $entry->data(),
                'published' => $entry->published(),
                'status' => $entry->status(),
            ]);

            $model->save();
        });
    
    }

    private function migrateForms()
    {
        Statamic::repository(Contracts\Forms\FormRepository::class, \Statamic\Forms\FormRepository::class);
        $forms = \Statamic\Facades\Form::all();

        foreach ($forms as $form) {
            $formModel = StatamicDatabase\Forms\FormModel::firstOrNew([
                'handle' => $form->handle()
            ]);

            $data = collect([
                'title' => $form->title(),
                'honeypot' => $form->honeypot(),
                'email' => collect($form->email())->map(function ($email) {
                    $email['markdown'] = $email['markdown'] ?: null;
    
                    return Arr::removeNullValues($email);
                })->all(),
                'metrics' => $form->metrics(),
            ])->filter()->all();
    
            if ($form->store() === false) {
                $data['store'] = false;
            }

            $formModel->data = $data;
            $formModel->save();

            $submissions = $form->submissions();

            foreach ($submissions as $key => $submission) {
                $model = StatamicDatabase\Forms\SubmissionModel::firstOrNew([
                    'handle' => $submission->id(),
                    'form_id' => $formModel->id
                ]);

                $model->data = $submission->data();
                $model->save();
            }
        }
    }

    private function migrateGlobals()
    {
        Statamic::repository(Contracts\Globals\GlobalRepository::class, FileRepositories\GlobalRepository::class);
        $globals = \Statamic\Facades\GlobalSet::all();

        foreach ($globals as $global) {
            $model = StatamicDatabase\Globals\GlobalModel::firstOrNew([
                'handle' => $global->handle()
            ]);

            $data = $global->fileData();

            if ($global->blueprint()) {
                $data['blueprint'] = $global->blueprint();
            }

            foreach ($global->localizations() as $key => $localization) {
                $data['variables'][$localization->locale()] = $localization->fileData();
            }

            $model->data = $data;
            $model->save();
        }
    }

    private function migrateNavigation()
    {
        Statamic::repository(Contracts\Structures\NavigationRepository::class, FileRepositories\NavigationRepository::class);
        $navigations = \Statamic\Facades\Nav::all();
        
        foreach ($navigations as $key => $navigation) {
            $model = StatamicDatabase\Navigation\NavigationModel::firstOrNew([
                'handle' => $navigation->id(),
            ]);
            $model->data = $navigation->fileData();
            $model->save();

            foreach (config('statamic.sites.sites') as $key => $site) {
                if (is_file(base_path('content/trees/navigation/'.$key.'/'.$navigation->id().'.yaml'))) {
                    $model = StatamicDatabase\Trees\TreeModel::firstOrNew([
                        'handle' => 'navigation::' . $navigation->id() . '::'.$key
                    ]);
                    $fileData = YAML::file(base_path('content/trees/navigation/'.$key.'/'.$navigation->id().'.yaml'))->parse();
                    $model->data = $fileData;
                    $model->save();
                }
            }
        }
    }

    private function migrateTaxonomies()
    {
        Statamic::repository(Contracts\Taxonomies\TaxonomyRepository::class, FileRepositories\TaxonomyRepository::class);
        $taxonomies = \Statamic\Facades\Taxonomy::all();
        
        foreach ($taxonomies as $taxonomy) {
            $taxonomyModel = StatamicDatabase\Taxonomies\TaxonomyModel::firstOrNew([
                'handle' => $taxonomy->id(),
            ]);
    
            $taxonomyModel->data = $taxonomy->fileData();
    
            $taxonomyModel->save();

            foreach ($taxonomy->queryTerms()->get() as $key => $term) {
                $fileData = YAML::file($term->path())->parse();

                $model = StatamicDatabase\Taxonomies\TermModel::firstOrNew([
                    'taxonomy' => $term->taxonomyHandle(),
                    'slug' => $term->slug(),
                ]);

                $model->data = $fileData;
                $model->save();
            }
        }
    }
}
