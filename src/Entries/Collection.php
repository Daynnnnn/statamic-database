<?php

namespace Daynnnnn\StatamicDatabase\Entries;

use Statamic\Facades\Site;
use Statamic\Entries\Collection as FileCollection;

class Collection extends FileCollection
{
    protected $model;

    public static function fromModel(CollectionModel $model)
    {
        $data = $model->data;
        $sites = array_get($data, 'sites', Site::hasMultiple() ? [] : [Site::default()->handle()]);
        $collection = Collection::make($model->handle)
            ->title(array_get($data, 'title'))
            ->routes(array_get($data, 'route'))
            ->mount(array_get($data, 'mount'))
            ->dated(array_get($data, 'date', false))
            ->ampable(array_get($data, 'amp', false))
            ->sites($sites)
            ->template(array_get($data, 'template'))
            ->layout(array_get($data, 'layout'))
            // TODO: Work out what this should do, and why it returns an empty Illuminate Collection
            // ->cascade(array_get($data, 'inject', []))
            ->searchIndex(array_get($data, 'search_index'))
            ->revisionsEnabled(array_get($data, 'revisions', false))
            ->defaultPublishState(self::getDefaultPublishState($data))
            ->structureContents(array_get($data, 'structure'))
            ->sortField(array_get($data, 'sort_by'))
            ->sortDirection(array_get($data, 'sort_dir'))
            ->taxonomies(array_get($data, 'taxonomies'));

        if ($dateBehavior = array_get($data, 'date_behavior')) {
            $collection
                ->futureDateBehavior($dateBehavior['future'] ?? null)
                ->pastDateBehavior($dateBehavior['past'] ?? null);
        }

        return $collection;
    }

    public function toModel()
    {
        $data = $this->fileData();

        $model = CollectionModel::firstOrNew([
            'handle' => $this->id(),
        ]);

        $model->data = $data;
        $model->save();

        return $model;
    }

    public function model($model = null)
    {
        if (func_num_args() === 0) {
            return $this->model;
        }

        $this->model = $model;

        $this->id($model->id);

        return $this;
    }

    public function lastModified()
    {
        return $this->model->updated_at;
    }

    protected static function getDefaultPublishState($data)
    {
        $value = array_get($data, 'default_status', 'published');

        if (! in_array($value, ['published', 'draft'])) {
            throw new \Exception('Invalid collection default_status value. Must be "published" or "draft".');
        }

        return $value === 'published';
    }
}