<?php

namespace gOOvER\FactorioRcon\Helpers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;

class VisibilityHelper
{
    /**
     * Check if the plugin should be visible for the current server
     */
    public static function shouldShow(): bool
    {
        $server = Filament::getTenant();
        if (!$server) {
            return false;
        }

        // Make sure egg relationship is loaded
        if (!$server->relationLoaded('egg')) {
            $server->load('egg');
        }

        $egg = $server->egg;
        if (!$egg) {
            return false;
        }

        $hasTag = self::hasFactorioTag($egg);
        $hasFeature = self::hasFactorioRconFeature($egg);
        
        // Log for debugging
        if (config('app.debug')) {
            Log::debug('Factorio RCON Visibility Check', [
                'server_id' => $server->id ?? 'unknown',
                'server_uuid' => $server->uuid ?? 'unknown',
                'egg_id' => $egg->id ?? 'unknown',
                'egg_name' => $egg->name ?? 'unknown',
                'tags_raw' => $egg->tags,
                'features_raw' => $egg->features,
                'inherit_features_raw' => $egg->inherit_features ?? null,
                'hasTag' => $hasTag,
                'hasFeature' => $hasFeature,
                'result' => $hasTag && $hasFeature,
            ]);
        }

        return $hasTag && $hasFeature;
    }

    /**
     * Check if the egg has a 'factorio' tag
     */
    protected static function hasFactorioTag($egg): bool
    {
        // Get tags - could be array, Collection, or JSON string
        $tags = $egg->tags ?? [];
        
        // If tags is a string (JSON), decode it
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        }
        
        // If tags is a Collection, convert to array
        if ($tags instanceof \Illuminate\Support\Collection) {
            $tags = $tags->toArray();
        }
        
        if (!is_array($tags) || empty($tags)) {
            return false;
        }

        foreach ($tags as $tag) {
            // Handle both string tags and object/array tags
            $tagName = is_array($tag) ? ($tag['name'] ?? $tag[0] ?? '') : (string) $tag;
            
            if (stripos($tagName, 'factorio') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the egg has the 'factorio-rcon' feature
     */
    protected static function hasFactorioRconFeature($egg): bool
    {
        // First try inherit_features (this is what Pelican uses for inherited features)
        $features = null;
        
        // Try to access inherit_features attribute (Pelican Panel accessor)
        try {
            $features = $egg->inherit_features;
        } catch (\Exception $e) {
            // Fallback to direct features if inherit_features fails
        }
        
        // If inherit_features is empty, try direct features
        if (empty($features)) {
            $features = $egg->features ?? [];
        }
        
        // If features is a string (JSON), decode it
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }
        
        // If features is a Collection, convert to array
        if ($features instanceof \Illuminate\Support\Collection) {
            $features = $features->toArray();
        }
        
        if (!is_array($features) || empty($features)) {
            return false;
        }

        return in_array('factorio-rcon', $features, true);
    }
}
