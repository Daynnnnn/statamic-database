<?php

namespace Daynnnnn\StatamicDatabase\Forms;

use Statamic\Forms\FormRepository as FileFormRepository;
use Statamic\Contracts\Forms\Form as FormContract;
use Statamic\Contracts\Forms\FormRepository as Contract;
use Statamic\Contracts\Forms\Submission as SubmissionContract;

class FormRepository extends FileFormRepository implements Contract
{
    /**
     * Find a form.
     *
     * @param string $handle
     * @return FormContract
     */
    public function find($handle)
    {
        $form = $this->make($handle);

        if (($model = FormModel::where('handle', $handle)->first())) {
            return;
        }

        return $form->hydrate();
    }

    /**
     * Get all forms.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return FormModel::all()->map(function ($model) {
            return self::find($model->handle);
        });
    }

    /**
     * Get the number of forms.
     *
     * @return int
     */
    public function count()
    {
        return FormModel::count();
    }

    public static function bindings(): array
    {
        return [
            FormContract::class => Form::class,
            SubmissionContract::class => Submission::class,
        ];
    }
}
