<?php

namespace gOOvER\FactorioManager\Models;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Virtual Player DTO - not backed by database
 * Data comes from Factorio RCON responses
 * 
 * This is a simple data object, NOT an Eloquent model,
 * to avoid any database queries.
 */
class Player implements Arrayable, JsonSerializable
{
    public string $id;
    public string $name;
    public bool $online;
    public bool $is_admin;
    public bool $is_banned;
    public string $ban_reason;

    public function __construct(array $attributes = [])
    {
        $this->name = $attributes['name'] ?? '';
        $this->id = $attributes['id'] ?? $this->name;
        $this->online = (bool) ($attributes['online'] ?? false);
        $this->is_admin = (bool) ($attributes['is_admin'] ?? false);
        $this->is_banned = (bool) ($attributes['is_banned'] ?? false);
        $this->ban_reason = $attributes['ban_reason'] ?? '';
    }

    public function getKey(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'online' => $this->online,
            'is_admin' => $this->is_admin,
            'is_banned' => $this->is_banned,
            'ban_reason' => $this->ban_reason,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create a Player instance from an array
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Prevent Eloquent query() calls - this is not a database model
     * 
     * @throws \BadMethodCallException
     */
    public static function query(): never
    {
        throw new \BadMethodCallException(
            'Player is not an Eloquent model. Use FactorioRconProvider methods instead. ' .
            'If you see this error, please delete any old PlayerResource files from the plugin.'
        );
    }

    /**
     * Handle any static method calls that might be Eloquent-related
     * 
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, array $args): never
    {
        throw new \BadMethodCallException(
            "Player::{$method}() is not available. Player is not an Eloquent model. " .
            'Use FactorioRconProvider methods instead.'
        );
    }
}
