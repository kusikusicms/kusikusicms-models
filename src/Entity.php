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
    protected $fillable
        = [
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
    protected $casts
        = [
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
    protected $dispatchesEvents
        = [
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

    /**
     * Static function to refresh the relations of ANCESTOR kind for the given Entity ID.
     * It also recreates children ANCESTOR relations recursively.
     */
    public static function refreshAncestorsRelationsById(string $entity_id)
    {
        $entity = Entity::find($entity_id);
        if ($entity) {
            self::refreshAncestorsRelationsOfEntity($entity);
        }
    }

    /**
     * Static function to refresh the relations of ANCESTOR kind for the given Entity.
     * It also recreates children ANCESTOR relations recursively.
     */
    public static function refreshAncestorsRelationsOfEntity(Entity $entity)
    {
        // First clear all ancestors relations of the entity
        EntityRelation::query()
            ->where('caller_entity_id', $entity->id)
            ->where('kind', EntityRelation::RELATION_ANCESTOR)
            ->delete();
        // Now recreate all ancestors relations
        $currentAncestor = Entity::find($entity->parent_entity_id);
        $currentDepth = 1;
        while ($currentAncestor) {
            EntityRelation::create([
                "caller_entity_id" => $entity->id,
                "called_entity_id" => $currentAncestor->id,
                "kind" => EntityRelation::RELATION_ANCESTOR,
                "depth" => $currentDepth
            ]);
            $currentAncestor = Entity::find($currentAncestor->parent_entity_id);
            $currentDepth ++;
        }
        // Descendants should be updated as well
        $children = Entity::where("parent_entity_id", $entity->id)->get();
        foreach ($children as $child) {
            self::refreshAncestorsRelationsOfEntity($child);
        }
    }

    /** Refresh the relations of ANCESTOR kind of the Entity
     */
    public function refreshAncestorsRelations(): void
    {
        self::refreshAncestorsRelationsOfEntity($this);
    }
}
