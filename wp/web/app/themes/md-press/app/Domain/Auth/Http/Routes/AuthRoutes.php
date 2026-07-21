<?php

declare(strict_types=1);

namespace App\Domain\Auth\Http\Routes;

use App\Domain\Auth\Http\Controllers\LoginController;
use App\Domain\Auth\Http\Controllers\LogoutController;
use App\Domain\Auth\Http\Controllers\RegisterController;
use App\Domain\Auth\Services\LoginService;
use App\Domain\Auth\Services\RegisterService;

/**
 * Registra las rutas y filtros del sistema de autenticación custom:
 * - /auth/login    → LoginController
 * - /auth/register → RegisterController
 * - /auth/logout   → LogoutController
 * - Bloqueo de wp-login.php y wp-admin no autenticado (404).
 * - Filtros login_url / logout_url de WordPress.
 */
class AuthRoutes
{
    private LoginController    $loginController;
    private LogoutController   $logoutController;
    private RegisterController $registerController;

    public function __construct()
    {
        $this->loginController    = new LoginController(new LoginService());
        $this->logoutController   = new LogoutController();
        $this->registerController = new RegisterController(new RegisterService());
    }

    public function register(): void
    {
        add_action('init', [$this, 'dispatch'], 5);
        add_filter('login_url',  [$this->loginController,  'customLoginUrl'],  10, 2);
        add_filter('logout_url', [$this->logoutController, 'customLogoutUrl'], 10, 2);
    }

    /**
     * Despacha la petición entrante a su controlador correspondiente.
     * Se ejecuta en el hook `init` con prioridad 5 (antes del routing de WordPress).
     */
    public function dispatch(): void
    {
        $path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

        // 1. Bloquear acceso directo a wp-login.php
        if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', 'wp-login.php')) {
            $this->render404();
        }

        // 2. Bloquear wp-admin si no hay sesión activa
        if (is_admin() && !defined('DOING_AJAX') && !is_user_logged_in()) {
            $this->render404();
        }

        // 3. Despachar rutas /auth/*
        match ($path) {
            '/auth/login'    => $this->loginController->handle(),
            '/auth/register' => $this->registerController->handle(),
            '/auth/logout'   => $this->logoutController->handle(),
            default          => null,
        };
    }

    private function render404(): void
    {
        status_header(404);
        nocache_headers();
        echo view('404')->render();
        exit;
    }
}
