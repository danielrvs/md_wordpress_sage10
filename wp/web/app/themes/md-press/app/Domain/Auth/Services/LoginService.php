<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use WP_Error;
use WP_User;

/**
 * Encapsula la lógica de autenticación delegando en wp_signon.
 * No accede a superglobales; recibe datos ya saneados desde el Controller.
 */
class LoginService
{
    /**
     * Intenta autenticar al usuario con las credenciales dadas.
     *
     * @return WP_User|WP_Error  WP_User en éxito, WP_Error en fallo.
     */
    public function attempt(string $username, string $password, bool $remember): WP_User|WP_Error
    {
        return wp_signon([
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        ], is_ssl());
    }

    /**
     * Traduce los códigos de error de WordPress a mensajes amigables para el usuario.
     */
    public function getFriendlyErrorMessage(string $code): string
    {
        return match ($code) {
            'invalid_username', 'invalid_email' => 'El usuario o correo electrónico no está registrado.',
            'incorrect_password'                => 'La contraseña introducida es incorrecta.',
            'empty_username'                    => 'Por favor, introduce tu usuario.',
            'empty_password'                    => 'Por favor, introduce tu contraseña.',
            default                             => 'Error de autenticación. Por favor, inténtalo de nuevo.',
        };
    }
}
