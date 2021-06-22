<?php

namespace Statamic\Forms;

use Statamic\Contracts\Data\Augmentable;
use Statamic\Contracts\Data\Augmented;
use Statamic\Contracts\Forms\Form as FormContract;
use Statamic\Contracts\Forms\Submission;
use Statamic\Data\HasAugmentedInstance;
use Statamic\Events\FormBlueprintFound;
use Statamic\Events\FormDeleted;
use Statamic\Events\FormSaved;
use Statamic\Facades\Blueprint;
use Statamic\Facades\File;
use Statamic\Facades\Folder;
use Statamic\Facades\YAML;
use Statamic\Forms\Exceptions\BlueprintUndefinedException;
use Statamic\Statamic;
use Statamic\Support\Arr;
use Statamic\Support\Traits\FluentlyGetsAndSets;

class Form implements FormContract, Augmentable
{
    /**
     * Save form.
     */
    public function save()
    {
        $data = collect([
            'title' => $this->title,
            'honeypot' => $this->honeypot,
            'email' => collect($this->email)->map(function ($email) {
                $email['markdown'] = $email['markdown'] ?: null;

                return Arr::removeNullValues($email);
            })->all(),
            'metrics' => $this->metrics,
        ])->filter()->all();

        if ($this->store === false) {
            $data['store'] = false;
        }

        $model = FormModel::firstOrNew([
            'handle' => $this->handle(),
        ]);

        $model->data = $data;

        $model->save();

        FormSaved::dispatch($this);
    }

    /**
     * Delete form and associated submissions.
     */
    public function delete()
    {
        $this->submissions()->each->delete();

        FormModel::delete($this->handle());

        FormDeleted::dispatch($this);
    }

    /**
     * Hydrate form from file data.
     *
     * @return $this
     */
    public function hydrate()
    {
        $data = FormModel::where('handle', $this->handle())->first()->data;
        $data->filter(function ($value, $property) {
                return in_array($property, [
                    'title',
                    'honeypot',
                    'store',
                    'email',
                ]);
            })
            ->each(function ($value, $property) {
                $this->{$property}($value);
            });

        return $this;
    }

    /**
     * Get the submissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function submissions()
    {
        $path = config('statamic.forms.submissions').'/'.$this->handle();

        return collect(Folder::getFilesByType($path, 'yaml'))->map(function ($file) {
            return $this->makeSubmission()
                ->id(pathinfo($file)['filename'])
                ->data(YAML::parse(File::get($file)));
        });
    }
}
