<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Daynnnnn\StatamicDatabase\Taxonomies\TaxonomyModel as Model;
use Statamic\Facades\Site;
use Statamic\Taxonomies\Taxonomy as FileTaxonomy;
use Statamic\Taxonomies\TermCollection;

class Taxonomy extends FileTaxonomy
{
    protected $model;

    public static function fromModel(Model $model)
    {
        $data = $model->data;

        $sites = array_get($data, 'sites', Site::hasMultiple() ? [] : [Site::default()->handle()]);

        return Taxonomy::make($model->handle)
            ->title(array_get($data, 'title'))
            ->cascade(array_get($data, 'inject', []))
            ->revisionsEnabled(array_get($data, 'revisions', false))
            ->searchIndex(array_get($data, 'search_index'))
            ->defaultPublishState(self::getDefaultPublishState($data))
            ->sites($sites);
    }

    public function toModel()
    {
        $model = Model::firstOrNew([
            'handle' => $this->id(),
        ]);

        $model->data = $this->fileData();

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
            throw new \Exception('Invalid taxonomy default_status value. Must be "published" or "draft".');
        }

        return $value === 'published';
    }
}