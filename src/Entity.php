<?php

namespace KusikusiCMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KusikusiCMS\Models\Traits\UsesShortId;
use KusikusiCMS\Models\Events\{
    EntityCreating,
    EntityCreated,
    EntityRetrieved,
    EntityUpdating,
    EntityUpdated,
    EntitySaving,
    EntitySaved,
    EntityDeleting,
    EntityDeleted,
    EntityTrashed,
    EntityForceDeleting,
    EntityForceDeleted,
    EntityRestoring,
    EntityRestored,
    EntityReplicating
};


class Entity extends Model//
{
    use UsesShortId, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'model',
        'view',
        'langs',
        'properties',
        'status',
        'parent_entity_id',
        'created_at',
        'published_at',
        'unpublished_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'published_at' => 'datetime',
        'unpublished_at' => 'datetime',
        'langs' => 'array'
    ];

    /**
     * The event map for the model. Some events are not used here, but they are defined so other packages can use them
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'retrieved' => EntityRetrieved::class,
        'creating' => EntityCreating::class,
        'created' => EntityCreated::class,
        'updating' => EntityUpdating::class,
        'updated' => EntityUpdated::class,
        'saving' => EntitySaving::class,
        'saved' => EntitySaved::class,
        'deleting' => EntityDeleting::class,
        'deleted' => EntityDeleted::class,
        'trashed' => EntityTrashed::class,
        'forceDeleting' => EntityForceDeleting::class,
        'forceDeleted' => EntityForceDeleted::class,
        'restoring' => EntityRestoring::class,
        'restored' => EntityRestored::class,
        'replicating' => EntityReplicating::class,
    ];
}
