<?php

namespace Daynnnnn\StatamicDatabase\Forms;

use Statamic\Contracts\Data\Augmentable;
use Statamic\Contracts\Forms\Submission as SubmissionContract;
use Statamic\Events\SubmissionDeleted;
use Statamic\Events\SubmissionSaved;
use Statamic\Forms\Submission as FileSubmission;

class Submission extends FileSubmission implements SubmissionContract, Augmentable
{
    /**
     * Save the submission.
     */
    public function save()
    {
        $model = FormSubmission::firstOrCreate([
            'form_id' => $this->form()->id(),
            'handle' => $this->id(),
        ]);
        $model->data = $this->data();
        $model->save();

        SubmissionSaved::dispatch($this);
    }

    /**
     * Delete this submission.
     */
    public function delete()
    {
        SubmissionModel::where('form_id', $this->form()->id())->where('handle', $this->id())->first()->delete();

        SubmissionDeleted::dispatch($this);
    }
}
