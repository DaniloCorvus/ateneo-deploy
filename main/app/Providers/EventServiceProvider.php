<?php

namespace App\Providers;

use App\CustomModels\Person as CustomModelsPerson;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Appointment;
use App\Models\Cup;
use App\Models\CustomModels\Person as ModelsCustomModelsPerson;
use App\Models\Person;
use App\Models\User;
use App\Observers\ApointmentObserver;
use App\Observers\CupObserver;
use App\Observers\ProfessionalObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'App\Events\AppointmentModify' => [
            'App\Listeners\SendAppointmentModifyNotification',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Appointment::observe(ApointmentObserver::class);
        Person::observe(ProfessionalObserver::class);
        ModelsCustomModelsPerson::observe(ProfessionalObserver::class);
        Cup::observe(CupObserver::class);
    }
}
