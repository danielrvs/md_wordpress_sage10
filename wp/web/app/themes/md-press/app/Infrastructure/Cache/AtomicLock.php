<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use DomainException;

/**
 * Bloqueo atómico reutilizable sobre Redis mediante wp_cache_add (SETNX).
 *
 * Garantiza que un recurso identificado por clave solo pueda ser procesado
 * por una única petición a la vez, previniendo race conditions.
 *
 * Uso básico:
 *   $lock = new AtomicLock('appointment_slot', '42_2026-07-23_10:00');
 *   $result = $lock->run(fn () => $service->procesarReserva());
 *
 * Uso manual:
 *   $lock = new AtomicLock('schedule_update', 'doctor_42');
 *   $lock->acquire();
 *   try { ... } finally { $lock->release(); }
 */
class AtomicLock
{
    private const DEFAULT_TTL_SECONDS = 5;
    private const CACHE_GROUP = 'atomic_locks';

    private string $lockKey;
    private bool $acquired = false;

    /**
     * @param string $context  Dominio o contexto del bloqueo (ej: 'appointment_slot', 'schedule_update')
     * @param string $resource Identificador único del recurso a bloquear (ej: '42_2026-07-23_10:00')
     * @param int    $ttl      Segundos máximos del bloqueo (safety net contra deadlocks)
     */
    public function __construct(
        private readonly string $context,
        private readonly string $resource,
        private readonly int $ttl = self::DEFAULT_TTL_SECONDS,
    ) {
        $this->lockKey = sprintf('lock_%s_%s', $this->context, $this->resource);
    }

    /**
     * Intenta adquirir el bloqueo atómico.
     *
     * @throws DomainException si el recurso ya está bloqueado por otra petición.
     */
    public function acquire(string $failMessage = ''): void
    {
        $acquired = wp_cache_add($this->lockKey, 1, self::CACHE_GROUP, $this->ttl);

        if (!$acquired) {
            throw new DomainException(
                $failMessage ?: 'El recurso solicitado está siendo procesado por otra petición. Por favor, reintenta en unos segundos.'
            );
        }

        $this->acquired = true;
    }

    /**
     * Libera el bloqueo atómico si fue adquirido.
     */
    public function release(): void
    {
        if ($this->acquired) {
            wp_cache_delete($this->lockKey, self::CACHE_GROUP);
            $this->acquired = false;
        }
    }

    /**
     * Ejecuta un callable dentro del bloqueo atómico y lo libera al finalizar.
     *
     * @template T
     * @param callable(): T $callback
     * @param string        $failMessage Mensaje de error si no se puede adquirir el bloqueo.
     * @return T
     * @throws DomainException si el recurso ya está bloqueado.
     */
    public function run(callable $callback, string $failMessage = ''): mixed
    {
        $this->acquire($failMessage);

        try {
            return $callback();
        } finally {
            $this->release();
        }
    }

    /**
     * Devuelve la clave de bloqueo generada (útil para tests y debugging).
     */
    public function getKey(): string
    {
        return $this->lockKey;
    }
}
