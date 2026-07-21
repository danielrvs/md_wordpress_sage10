<?php

declare(strict_types=1);

namespace App\Services;

class LanguageManager
{
    private static ?string $currentLocale = null;
    private static array $translations = [];

    public static function boot(): void
    {
        if (self::$currentLocale !== null) {
            return;
        }

        // 1. Detectar si se recibe ?lang=es o ?lang=en por URL
        if (isset($_GET['lang'])) {
            $requestedLang = strtolower(trim((string) $_GET['lang']));
            if (in_array($requestedLang, ['es', 'en'], true)) {
                self::$currentLocale = $requestedLang;
                if (!headers_sent()) {
                    setcookie('app_locale', $requestedLang, time() + (86400 * 30), '/');
                }
            }
        }

        // 2. Si no viene por URL, buscar en Cookie
        if (self::$currentLocale === null && isset($_COOKIE['app_locale'])) {
            $cookieLang = strtolower(trim((string) $_COOKIE['app_locale']));
            if (in_array($cookieLang, ['es', 'en'], true)) {
                self::$currentLocale = $cookieLang;
            }
        }

        // 3. Idioma por defecto: español ('es')
        if (self::$currentLocale === null) {
            self::$currentLocale = 'es';
        }

        // Cargar diccionario de traducciones
        self::loadTranslations();
    }

    public static function getLocale(): string
    {
        self::boot();
        return self::$currentLocale ?? 'es';
    }

    public static function getTranslations(): array
    {
        self::boot();
        return self::$translations;
    }

    public static function translate(string $key, array $replace = []): string
    {
        self::boot();

        $text = self::$translations[$key] ?? $key;

        foreach ($replace as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, (string) $value, $text);
        }

        return $text;
    }

    private static function loadTranslations(): void
    {
        $langDir = dirname(__DIR__, 2) . '/resources/lang';
        $filePath = "{$langDir}/" . self::$currentLocale . '.php';

        if (file_exists($filePath)) {
            self::$translations = require $filePath;
        } else {
            self::$translations = require "{$langDir}/es.php";
        }
    }
}
