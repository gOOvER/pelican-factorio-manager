<?php

namespace gOOvER\FactorioRcon\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Virtual Player model - not backed by database
 * Data comes from Factorio RCON responses
 */
class Player extends Model
{
    protected $fillable = [
        'id',
        'name',
        'online',
        'is_admin',
        'is_banned',
        'ban_reason',
    ];

    protected $casts = [
        'online' => 'boolean',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    public $exists = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set ID to name if not provided
        if (isset($attributes['name']) && !isset($attributes['id'])) {
            $this->attributes['id'] = $attributes['name'];
        }
    }

    public function getKey()
    {
        return $this->attributes['id'] ?? $this->attributes['name'] ?? null;
    }
}
