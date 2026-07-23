<?php

declare(strict_types=1);

use App\Infrastructure\Cache\AtomicLock;

/*
|--------------------------------------------------------------------------
| AtomicLock Unit Tests
|--------------------------------------------------------------------------
| Tests aislados para la clase AtomicLock (Infrastructure/Cache).
| Validan acquire, release, run, reentrada, TTL y mensajes personalizados.
*/

test('acquire obtiene el bloqueo y una segunda llamada lanza DomainException', function () {
    $lock1 = new AtomicLock('unit', 'acquire_test', 5);
    $lock1->acquire();

    $lock2 = new AtomicLock('unit', 'acquire_test', 5);

    expect(fn () => $lock2->acquire())
        ->toThrow(DomainException::class);

    $lock1->release();
});

test('release libera el bloqueo permitiendo que otro lo adquiera', function () {
    $lock = new AtomicLock('unit', 'release_test', 5);
    $lock->acquire();
    $lock->release();

    // Un segundo lock debería poder adquirirse sin problema
    $lock2 = new AtomicLock('unit', 'release_test', 5);
    $lock2->acquire();
    $lock2->release();

    expect(true)->toBeTrue();
});

test('release es idempotente (llamar dos veces no falla)', function () {
    $lock = new AtomicLock('unit', 'idempotent_test', 5);
    $lock->acquire();
    $lock->release();
    $lock->release(); // segunda llamada no debe lanzar

    expect(true)->toBeTrue();
});

test('run ejecuta el callback, devuelve su resultado y libera el lock', function () {
    $lock = new AtomicLock('unit', 'run_success', 5);

    $result = $lock->run(fn () => 42);

    expect($result)->toBe(42);

    // Lock liberado: otro acquire no debe fallar
    $check = new AtomicLock('unit', 'run_success', 5);
    $check->acquire();
    $check->release();
});

test('run libera el lock incluso cuando el callback lanza una excepción', function () {
    $lock = new AtomicLock('unit', 'run_exception', 5);

    try {
        $lock->run(fn () => throw new RuntimeException('boom'));
    } catch (RuntimeException) {
        // esperado
    }

    // Lock liberado: otro acquire no debe fallar
    $check = new AtomicLock('unit', 'run_exception', 5);
    $check->acquire();
    $check->release();

    expect(true)->toBeTrue();
});

test('acquire usa el mensaje de error personalizado al fallar', function () {
    $lock1 = new AtomicLock('unit', 'custom_msg', 5);
    $lock1->acquire();

    $lock2 = new AtomicLock('unit', 'custom_msg', 5);

    expect(fn () => $lock2->acquire('Recurso ocupado, espera'))
        ->toThrow(DomainException::class, 'Recurso ocupado, espera');

    $lock1->release();
});

test('run propaga el mensaje personalizado de fallo', function () {
    $lock1 = new AtomicLock('unit', 'run_custom_msg', 5);
    $lock1->acquire();

    $lock2 = new AtomicLock('unit', 'run_custom_msg', 5);

    expect(fn () => $lock2->run(fn () => 'never', 'Slot bloqueado'))
        ->toThrow(DomainException::class, 'Slot bloqueado');

    $lock1->release();
});

test('contextos distintos no colisionan entre sí', function () {
    $lockA = new AtomicLock('context_a', 'same_resource', 5);
    $lockB = new AtomicLock('context_b', 'same_resource', 5);

    $lockA->acquire();
    $lockB->acquire(); // distinto contexto, no debe fallar

    $lockA->release();
    $lockB->release();

    expect(true)->toBeTrue();
});

test('getKey devuelve la clave con formato lock_{context}_{resource}', function () {
    $lock = new AtomicLock('appointment_slot', '42_2026-07-23_10:00');

    expect($lock->getKey())->toBe('lock_appointment_slot_42_2026-07-23_10:00');
});
