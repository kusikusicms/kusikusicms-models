<?php

namespace KusikusiCMS\Models\Observers;

use Carbon\Carbon;
use KusikusiCMS\Models\Entity;
use Illuminate\Support\Str;

class EntityObserver
{
    /**
     * Handle the Entity "created" event.
     */
    public function creating(Entity $entity): void
    {
        // Check if the id is already in use
        if (Entity::find($entity[$entity->getKeyName()])) {
            abort(403, 'Duplicated Entity ID "'.$entity[$entity->getKeyName()]).'"';
        }
        // Setting default values
        if (!isset($entity->model))         { $entity->model = 'Entity'; }
        if (!isset($entity->published_at))  { $entity->published_at = Carbon::now(); }
        if (!isset($entity->visibility))    { $entity->visibility = 'public'; }
        if (!isset($entity->view))          { $entity->view = Str::snake($entity['model']); }
        if (!isset($entity->properties))    { $entity->properties = new \ArrayObject(); }
    }

    /**
     * Handle the Entity "created" event.
     */
    public function created(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "updated" event.
     */
    public function updated(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "deleted" event.
     */
    public function deleted(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "restored" event.
     */
    public function restored(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "force deleted" event.
     */
    public function forceDeleted(Entity $entity): void
    {
        //
    }
}
