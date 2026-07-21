<?php

declare(strict_types=1);

namespace App\Domain\Auth\Http\Controllers;

use App\Domain\Auth\Services\LoginService;

/**
 * Gestiona la ruta GET|POST /auth/login.
 */
class LoginController
{
    public function __construct(
        private readonly LoginService $loginService
    ) {}

    /**
     * Despacha la petición: procesa el POST o renderiza el formulario.
     */
    public function handle(): void
    {
        if (is_user_logged_in()) {
            wp_redirect(admin_url());
            exit;
        }

        $error    = null;
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$error, $username] = $this->processPost();

            // Éxito → ya se ha redirigido dentro de processPost(); si llegamos
            // aquí es porque hubo un error.
        }

        echo view('auth.login', [
            'error'     => $error,
            'username'  => $username,
            'actionUrl' => $_SERVER['REQUEST_URI'],
        ])->render();
        exit;
    }

    /**
     * Procesa el formulario POST y redirige en caso de éxito.
     *
     * @return array{0: string|null, 1: string}  [$error, $username]
     */
    private function processPost(): array
    {
        if (
            !isset($_POST['custom_login_nonce'])
            || !wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')
        ) {
            return ['Acción no autorizada o expirada.', ''];
        }

        $username = sanitize_text_field($_POST['log'] ?? '');
        $password = $_POST['pwd'] ?? '';
        $remember = isset($_POST['rememberme']);

        $user = $this->loginService->attempt($username, $password, $remember);

        if (is_wp_error($user)) {
            $error = $this->loginService->getFriendlyErrorMessage($user->get_error_code());
            return [$error, $username];
        }

        $redirectTo = sanitize_text_field($_GET['redirect_to'] ?? admin_url());
        wp_redirect($redirectTo);
        exit;
    }

    /**
     * Filter: sobreescribe la URL de login de WordPress.
     */
    public function customLoginUrl(string $loginUrl, string $redirect): string
    {
        $url = home_url('/auth/login');
        if ($redirect) {
            $url = add_query_arg('redirect_to', urlencode($redirect), $url);
        }
        return $url;
    }
}
