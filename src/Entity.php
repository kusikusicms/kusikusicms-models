<?php

namespace KusikusiCMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KusikusiCMS\Models\Traits\UsesShortId;
use KusikusicmsModels\Casts\Json;


class Entity extends Model
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
        'properties',
        'visibility',
        'parent_entity_id',
        'created_at',
        'published_at',
        'unpublished_at'
    ];

    protected $contentFields = [];

    protected $propertiesFields = [];

    protected $ancestorsRelations = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => Json::class,
        'published_at' => 'datetime',
        'unpublished_at' => 'datetime',
        'langs' => 'array'
    ];
}
