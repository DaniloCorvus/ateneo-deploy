<?php

namespace App\Observers;

use App\Models\Person;

class PersonObserver
{
    /**
     * Handle the person "created" event.
     *
     * @param  \App\person  $person
     * @return void
     */
    public function created(Person $person)
    {
       $person->color = concat('#',SUBSTRING((lpad(hex(round(Rand() * 10000000)),6,0)),-6));
       $person->save();
    }

    /**
     * Handle the person "updated" event.
     *
     * @param  \App\person  $person
     * @return void
     */
    public function updated(person $person)
    {
        //
    }

    /**
     * Handle the person "deleted" event.
     *
     * @param  \App\person  $person
     * @return void
     */
    public function deleted(person $person)
    {
        //
    }

    /**
     * Handle the person "restored" event.
     *
     * @param  \App\person  $person
     * @return void
     */
    public function restored(person $person)
    {
        //
    }

    /**
     * Handle the person "force deleted" event.
     *
     * @param  \App\person  $person
     * @return void
     */
    public function forceDeleted(person $person)
    {
        //
    }
}
