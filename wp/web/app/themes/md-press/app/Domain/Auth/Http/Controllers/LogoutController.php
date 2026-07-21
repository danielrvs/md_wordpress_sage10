<?php

declare(strict_types=1);

namespace App\Domain\Auth\Http\Controllers;

/**
 * Gestiona la ruta GET /auth/logout y sobreescribe la URL de logout de WordPress.
 */
class LogoutController
{
    /**
     * Cierra la sesión y redirige al inicio.
     */
    public function handle(): void
    {
        wp_logout();
        wp_redirect(home_url('/'));
        exit;
    }

    /**
     * Filter: sobreescribe la URL de logout de WordPress.
     */
    public function customLogoutUrl(string $logoutUrl, string $redirect): string
    {
        return home_url('/auth/logout');
    }
}
