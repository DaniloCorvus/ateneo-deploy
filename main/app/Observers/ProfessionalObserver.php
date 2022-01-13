<?php

namespace App\Observers;

use App\Models\Person;
use Illuminate\Support\Facades\File;

class ProfessionalObserver
{
    /**
     * Handle the person "created" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function updated(Person $person)
    {
        if ($person->wasChanged('image_blob')) {
            if (File::exists($person->getOriginal('image_blob'))) {
                File::delete($person->getOriginal('image_blob'));
            }
        }

        if ($person->wasChanged('signature_blob')) {
            if (File::exists($person->getOriginal('signature_blob'))) {
                File::delete($person->getOriginal('signature_blob'));
            }
        }
    }

    /**
     * Handle the person "deleted" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function deleted(Person $person)
    {
        //
    }

    /**
     * Handle the person "restored" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function restored(Person $person)
    {
        //
    }

    /**
     * Handle the person "force deleted" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function forceDeleted(Person $person)
    {
        //
    }
}
