<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Statamic\Stache\Repositories\TaxonomyRepository as StacheRepository;
use Statamic\Contracts\Taxonomies\Taxonomy as TaxonomyContract;
use Illuminate\Support\Collection;
use Statamic\Facades\Blink;

class TaxonomyRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            TaxonomyContract::class => Taxonomy::class,
        ];
    }

    public function all(): Collection
    {
        return Blink::once('taxonomies', function () {
            $keys = TaxonomyModel::get()->map(function ($model) {
                return Taxonomy::fromModel($model);
            });

            return Collection::make($keys);
        });
    }

    public function find($id): Taxonomy {
        return Blink::once('taxonomy:'.$id, function () use ($id) {
            if (($model = TaxonomyModel::where('handle', $id)->first()) == null) {
                return null;
            }

            $taxonomy = Taxonomy::fromModel($model);
            return $taxonomy;
        });
    }

    public function findByHandle($handle): ?Taxonomy
    {
        return $this->find($handle);
    }

    public function save($taxonomy)
    {
        $model = $taxonomy->toModel();

        $model->save();

        $taxonomy->model($model->fresh());
    }

    public function delete($taxonomy)
    {
        $taxonomy->toModel()->delete();
    }

    private function findTaxonomyHandleByUri($uri)
    {
        return Taxonomy::find($uri)->handle();
    }
}
