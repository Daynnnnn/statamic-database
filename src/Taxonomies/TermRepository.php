<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Statamic\Stache\Repositories\TermRepository as StacheRepository;
use Statamic\Contracts\Taxonomies\Term as TermContract;
use Statamic\Support\Str;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Taxonomies\TermCollection;

class TermRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            TermContract::class => Term::class,
        ];
    }

    public function all(): TermCollection
    {
        $keys = TermModel::get()->map(function ($model) {
            return Term::fromModel($model);
        });

        return TermCollection::make($keys);
    }

    public function find($id): TermContract {
        // TODO: Less hacky way of converting refference to id, possibly
        //       able to replace refference function altogether?
        if (($model = TermModel::where('slug', Str::afterLast($id, '::'))->first()) == null) {
            return null;
        }

        $term = Term::fromModel($model);
        return $term;
    }

    public function findByHandle($handle): ?Term
    {
        return $this->find($handle);
    }

    public function findByUri(string $uri, string $site = null): ?TermContract
    {
        $collection = Collection::all()
            ->first(function ($collection) use ($uri, $site) {
                if (Str::startsWith($uri, $collection->uri($site))) {
                    return true;
                }

                return Str::startsWith($uri, '/'.$collection->handle());
            });

        if ($collection) {
            $uri = Str::after($uri, $collection->uri($site) ?? $collection->handle());
        }

        $uri = Str::removeLeft($uri, '/');

        [$taxonomy, $slug] = array_pad(explode('/', $uri), 2, null);

        if (! $slug) {
            return null;
        }

        if (! $taxonomy = $this->findTaxonomyHandleByUri($taxonomy)) {
            return null;
        }
        
        $term = $this->query()
            ->where('slug', $slug)
            ->where('taxonomy', $taxonomy)
            ->where('site', $site)
            ->first();

        if (! $term) {
            return null;
        }
        
        // TODO: Why does query not return term localized
        $term = $term->in(Site::selected()->handle());

        return $term->collection($collection);
    }

    public function save($term)
    {
        $model = $term->toModel();

        $model->save();

        $term->model($model->fresh());
    }

    public function delete($term)
    {
        $term->toModel()->delete();
    }

    public function query()
    {
        $this->ensureAssociations();

        return new TermQueryBuilder(TermModel::query());
    }

    private function findTaxonomyHandleByUri($uri)
    {
        return Taxonomy::find($uri)->handle();
    }
}
