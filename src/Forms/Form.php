<?php

namespace Daynnnnn\StatamicDatabase\Forms;

use Statamic\Contracts\Data\Augmentable;
use Statamic\Contracts\Forms\Form as FormContract;
use Statamic\Events\FormDeleted;
use Statamic\Events\FormSaved;
use Statamic\Forms\Form as FileForm;
use Statamic\Support\Arr;

class Form extends FileForm implements FormContract, Augmentable
{
    protected $model;

    /**
     * Get Model ID
     */
    public function id()
    {
        return $this->model ?? ($this->model = FormModel::where('handle', $this->handle())->first()->id);
    }

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
        collect($data)->filter(function ($value, $property) {
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
        $formId = FormModel::where('handle', $this->handle())->first()->id;
        return SubmissionModel::where('form_id', $formId)->get()->map(function ($model) {
            return $this->makeSubmission()
                ->id($model->handle)
                ->data($model->data);
        });
    }
}
