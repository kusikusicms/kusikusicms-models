<?php

namespace KusikusicmsModels\Models;

use Illuminate\Database\Eloquent\Model;

class EntityRoute extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entities_routes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_id',
        'path',
        'entity_model',
        'lang',
        'kind'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'route_id',
        'entity_id',
        'entity_model'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default' => 'boolean'
    ];

    /**
     * To avoid "ambiguous" SQL errors Change the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'route_id';
    }
}
