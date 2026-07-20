<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Clase utilitaría para acceder a la cache de forma versionada
 * sin utilizar los tags. Ahorra RAM en Redis frente a los tags
 * y limpia el código en los servicios de dominio
 */

class VersionedCache
{
    public static function get(string $context, string $entityId, string $subKey): mixed
    {
        $version = self::getCurrentVersion($context, $entityId);
        $cacheKey = sprintf('%s:id_%s:v_%d:%s', $context, $entityId, $version, $subKey);

        return Cache::get($cacheKey);
    }

    public static function put(string $context, string $entityId, string $subKey, mixed $value, int $ttl): void
    {
        $version = self::getCurrentVersion($context, $entityId);
        $cacheKey = sprintf('%s:id_%s:v_%d:%s', $context, $entityId, $version, $subKey);

        Cache::put($cacheKey, $value, $ttl);
    }

    /**
     * Elimina un único registro sin alterar el resto.
     */
    public static function forget(string $context, string $entityId, string $subKey): bool
    {
        $version = self::getCurrentVersion($context, $entityId);
        $cacheKey = sprintf('%s:id_%s:v_%d:%s', $context, $entityId, $version, $subKey);

        return Cache::forget($cacheKey);
    }

    /**
     * Invalida de forma masiva/lógica toda la caché del contexto incrementando la versión.
     */
    public static function invalidate(string $context, string $entityId): void
    {
        $versionKey = sprintf('%s:v:%s', $context, $entityId);
        $currentVersion = (int) Cache::get($versionKey, 1);

        Cache::put($versionKey, $currentVersion + 1, 86400 * 30);
    }

    private static function getCurrentVersion(string $context, string $entityId): int
    {
        $versionKey = sprintf('%s:v:%s', $context, $entityId);
        return (int) Cache::get($versionKey, 1);
    }
}