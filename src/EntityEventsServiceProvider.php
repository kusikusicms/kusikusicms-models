<?php

namespace KusikusiCMS\Models;

use KusikusiCMS\Models\Listeners\EntityEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class EntityEventsServiceProvider extends EventServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        EntityEventSubscriber::class,
    ];
}