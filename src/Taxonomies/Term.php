<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Statamic\Facades\Term as TermFacade;
use Statamic\Taxonomies\Term as FileTerm;

class Term extends FileTerm
{
    protected $model;

    public static function fromModel(TermModel $model)
    {
        $data = $model->data;

        $term = TermFacade::make()
            ->taxonomy($model->taxonomy)
            ->slug($model->slug)
            ->blueprint($data['blueprint'] ?? null);

        foreach (Arr::pull($data, 'localizations', []) as $locale => $localeData) {
            $term->dataForLocale($locale, $localeData);
        }
        
        $term->dataForLocale($term->defaultLocale(), $data);

        return $term;
    }

    public function toModel()
    {
        $model = TermModel::firstOrNew([
            'slug' => $this->slug(),
        ]);
        
        $model->taxonomy = $this->taxonomy;
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
}