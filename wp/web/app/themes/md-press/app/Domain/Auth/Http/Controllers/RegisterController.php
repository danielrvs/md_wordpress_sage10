<?php

declare(strict_types=1);

namespace App\Domain\Auth\Http\Controllers;

use App\Domain\Auth\Services\RegisterService;

/**
 * Gestiona la ruta GET|POST /auth/register.
 */
class RegisterController
{
    public function __construct(
        private readonly RegisterService $registerService
    ) {}

    /**
     * Despacha la petición: procesa el POST o renderiza el formulario.
     */
    public function handle(): void
    {
        if (is_user_logged_in()) {
            wp_redirect(home_url('/'));
            exit;
        }

        $error = null;
        $name  = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$error, $name, $email] = $this->processPost();

            // Éxito → ya se ha redirigido dentro de processPost(); si llegamos
            // aquí es porque hubo un error.
        }

        echo view('auth.register', [
            'error'     => $error,
            'name'      => $name,
            'email'     => $email,
            'actionUrl' => $_SERVER['REQUEST_URI'],
        ])->render();
        exit;
    }

    /**
     * Procesa el formulario POST y redirige en caso de éxito.
     *
     * @return array{0: string|null, 1: string, 2: string}  [$error, $name, $email]
     */
    private function processPost(): array
    {
        if (
            !isset($_POST['custom_register_nonce'])
            || !wp_verify_nonce($_POST['custom_register_nonce'], 'custom_register_action')
        ) {
            return ['Acción no autorizada o expirada.', '', ''];
        }

        $name            = sanitize_text_field($_POST['name'] ?? '');
        $email           = sanitize_email($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $result = $this->registerService->register($name, $email, $password, $passwordConfirm);

        if (is_wp_error($result)) {
            return [$result->get_error_message(), $name, $email];
        }

        $redirectTo = sanitize_text_field($_GET['redirect_to'] ?? home_url('/'));
        wp_redirect($redirectTo);
        exit;
    }
}
