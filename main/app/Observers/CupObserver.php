<?php

namespace App\Observers;

use App\Models\Cup;

class CupObserver
{
    /**
     * Handle the cup "created" event.
     *
     * @param  \App\Cup  $cup
     * @return void
     */
    public function created(Cup $cup)
    {
        //$cup->specialities()->sync(request()->get('specialities'));
    }

    /**
     * Handle the cup "updated" event.
     *
     * @param  \App\Cup  $cup
     * @return void
     */
    public function updated(Cup $cup)
    {
       
    }

    /**
     * Handle the cup "deleted" event.
     *
     * @param  \App\Cup  $cup
     * @return void
     */
    public function deleted(Cup $cup)
    {
        //
    }

    /**
     * Handle the cup "restored" event.
     *
     * @param  \App\Cup  $cup
     * @return void
     */
    public function restored(Cup $cup)
    {
        //
    }

    /**
     * Handle the cup "force deleted" event.
     *
     * @param  \App\Cup  $cup
     * @return void
     */
    public function forceDeleted(Cup $cup)
    {
        //
    }
}
