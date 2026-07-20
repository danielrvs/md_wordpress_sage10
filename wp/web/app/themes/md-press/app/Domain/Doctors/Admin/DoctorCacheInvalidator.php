<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Admin;

use Illuminate\Support\Facades\Cache;

/**
 * Gestiona la invalidación de la caché del directorio de médicos
 * cuando se guarda o elimina un post de tipo doctor.
 */
class DoctorCacheInvalidator
{
    public function register(): void
    {
        add_action('save_post_doctor', [$this, 'invalidate'], 10, 1);
        add_action('before_delete_post', [$this, 'invalidate'], 10, 1);
    }

    public function invalidate(int $postId): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        Cache::forget("doctors:id:{$postId}");

        try {
            $version = (int) Cache::get('doctors:cache_version', 1);
            Cache::put('doctors:cache_version', $version + 1, 86400 * 30);
        } catch (\Throwable $e) {
            error_log('Error al incrementar la versión de caché de médicos: ' . $e->getMessage());
        }
    }
}
