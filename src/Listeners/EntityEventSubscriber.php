<?php
namespace KusikusiCMS\Models\Listeners;

use Carbon\Carbon;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Str;
use KusikusiCMS\Models\Entity;
use KusikusiCMS\Models\Events\EntityCreating;

class EntityEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function entityCreating(EntityCreating $event): void
    {
        // Check if the id is already in use
        if (Entity::find($event->entity[$event->entity->getKeyName()])) {
            abort(403, 'Duplicated Entity ID "'.$event->entity[$event->entity->getKeyName()]).'"';
        }
        // Setting default values
        if (!isset($event->entity->model))         { $event->entity->model = 'Entity'; }
        if (!isset($event->entity->published_at))  { $event->entity->published_at = Carbon::now(); }
        if (!isset($event->entity->view))          { $event->entity->view = Str::snake($event->entity['model']); }
        if (!isset($event->entity->properties))    { $event->entity->properties = new \ArrayObject(); }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            EntityCreating::class => 'entityCreating'
        ];
    }
}