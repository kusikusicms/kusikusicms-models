<?php

namespace KusikusiCMS\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EntityRelation extends Pivot
{
    const RELATION_ANCESTOR = 'ancestor';
    const RELATION_UNDEFINED = 'relation';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entities_relations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable
        = [
            'caller_entity_id',
            'called_entity_id',
            'kind',
            'position',
            'depth',
            'tags'
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden
        = [
            'created_at',
            'updated_at',
            'caller_entity_id',
            'called_entity_id'
        ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts
        = [
            'tags' => 'array'
        ];

    protected $guarded = ['relation_id'];

    /**
     * To avoid "ambiguous" errors Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'relation_id';
    }

    /**
     * The relation to the Entity is calling
     */
    public function caller_entity()
    {
        return $this->belongsTo('KusikusiCMS\Models\Entity', 'caller_entity_id',
            'relation_id');
    }

    /**
     * The relation to the Entity is being called
     */
    public function called_entity()
    {
        return $this->belongsTo('KusikusiCMS\Models\Entity', 'called_entity_id',
            'relation_id');
    }
}
