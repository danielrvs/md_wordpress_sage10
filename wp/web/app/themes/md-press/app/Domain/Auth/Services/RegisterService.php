<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use WP_Error;

/**
 * Encapsula las validaciones de negocio y la creación de nuevos usuarios.
 * No accede a superglobales; recibe datos ya saneados desde el Controller.
 */
class RegisterService
{
    /**
     * Valida los datos y registra un nuevo usuario.
     *
     * @return int|WP_Error  ID del nuevo usuario en éxito, WP_Error en fallo.
     */
    public function register(
        string $name,
        string $email,
        string $password,
        string $passwordConfirm
    ): int|WP_Error {
        if ($name === '') {
            return new WP_Error('empty_name', 'Por favor, introduce tu nombre completo.');
        }

        if (!is_email($email)) {
            return new WP_Error('invalid_email', 'El correo electrónico introducido no es válido.');
        }

        if (email_exists($email) || username_exists($email)) {
            return new WP_Error('email_exists', 'El correo electrónico ya está registrado. Por favor, inicia sesión.');
        }

        if (strlen($password) < 6) {
            return new WP_Error('password_too_short', 'La contraseña debe tener al menos 6 caracteres.');
        }

        if ($password !== $passwordConfirm) {
            return new WP_Error('password_mismatch', 'Las contraseñas no coinciden.');
        }

        $userId = wp_insert_user([
            'user_login'   => $email,
            'user_email'   => $email,
            'user_pass'    => $password,
            'display_name' => $name,
            'first_name'   => $name,
            'role'         => 'subscriber',
        ]);

        if (is_wp_error($userId)) {
            return $userId;
        }

        wp_set_current_user($userId);
        wp_set_auth_cookie($userId, true);

        return $userId;
    }
}
