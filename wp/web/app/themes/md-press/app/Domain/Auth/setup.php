<?php

declare(strict_types=1);

namespace App\Domain\Auth;

use Illuminate\Support\Facades\Redirect;

/**
 * Gestiona el sistema de autenticación custom:
 * - Rutas personalizadas para /auth/login y /auth/logout.
 * - Bloqueo de wp-login.php y wp-admin no autenticado con respuesta 404.
 * - Procesamiento seguro del login.
 */
class CustomAuthHandler
{
    public function register(): void
    {
        add_action('init', [$this, 'handleCustomRoutes'], 5);
        add_filter('login_url', [$this, 'customLoginUrl'], 10, 2);
        add_filter('logout_url', [$this, 'customLogoutUrl'], 10, 2);
    }

    public function handleCustomRoutes(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = rtrim($path, '/');

        // 1. Bloquear acceso directo a wp-login.php
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        if (str_contains($scriptName, 'wp-login.php')) {
            $this->render404();
        }

        // 2. Bloquear wp-admin si no está logueado
        if (is_admin() && !defined('DOING_AJAX') && !is_user_logged_in()) {
            $this->render404();
        }

        // 3. Manejar ruta /auth/login
        if ($path === '/auth/login') {
            if (is_user_logged_in()) {
                wp_redirect(admin_url());
                exit;
            }
            $this->handleLoginRequest();
        }

        // 4. Manejar ruta /auth/logout
        if ($path === '/auth/logout') {
            wp_logout();
            wp_redirect(home_url('/'));
            exit;
        }
    }

    private function handleLoginRequest(): void
    {
        $error = null;
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar nonce de seguridad
            if (!isset($_POST['custom_login_nonce']) || !wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
                $error = 'Acción no autorizada o expirada.';
            } else {
                $username = sanitize_text_field($_POST['log'] ?? '');
                $password = $_POST['pwd'] ?? '';
                $remember = isset($_POST['rememberme']);

                $credentials = [
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => $remember,
                ];

                $user = wp_signon($credentials, is_ssl());

                if (is_wp_error($user)) {
                    $error = $this->getFriendlyErrorMessage($user->get_error_code());
                } else {
                    $redirectTo = sanitize_text_field($_GET['redirect_to'] ?? admin_url());
                    wp_redirect($redirectTo);
                    exit;
                }
            }
        }

        echo view('auth.login', [
            'error'     => $error,
            'username'  => $username,
            'actionUrl' => $_SERVER['REQUEST_URI'],
        ])->render();
        exit;
    }

    public function customLoginUrl(string $loginUrl, string $redirect): string
    {
        $url = home_url('/auth/login');
        if ($redirect) {
            $url = add_query_arg('redirect_to', urlencode($redirect), $url);
        }
        return $url;
    }

    public function customLogoutUrl(string $logoutUrl, string $redirect): string
    {
        return home_url('/auth/logout');
    }

    private function render404(): void
    {
        status_header(404);
        nocache_headers();
        echo view('404')->render();
        exit;
    }

    private function getFriendlyErrorMessage(string $code): string
    {
        return match ($code) {
            'invalid_username', 'invalid_email' => 'El usuario o correo electrónico no está registrado.',
            'incorrect_password' => 'La contraseña introducida es incorrecta.',
            'empty_username' => 'Por favor, introduce tu usuario.',
            'empty_password' => 'Por favor, introduce tu contraseña.',
            default => 'Error de autenticación. Por favor, inténtalo de nuevo.',
        };
    }
}

// Active handler
(new \App\Domain\Auth\CustomAuthHandler())->register();
