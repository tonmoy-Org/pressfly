<?php

const APP_VERSION = "3.5.3";

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

if (!defined('DS')) {
    /**
     * Define DS as short form of DIRECTORY_SEPARATOR.
     */
    define('DS', DIRECTORY_SEPARATOR);
}

require __DIR__ . '/requirements.php';

$envs = [];

if (file_exists(__DIR__ . '/env.php')) {
    $envs = require_once __DIR__ . '/env.php';
}

if (file_exists(__DIR__ . '/env_local.php')) {
    $envs = require_once __DIR__ . '/env_local.php';
}

if (!count($envs)) {
    $envs = [
        'APP_INSTALLED' => 0,
        'APP_KEY' => 'yuZAcObRqtiWdGCNhYQHlIAnemMhL4z1',
    ];
}

if (is_array($envs) && count($envs)) {
    foreach ($envs as $key => $value) {
        /*
        if (function_exists('putenv')) {
            putenv("$key=$value");
        }
        */
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

/**
 * Gets the value of an environment variable.
 *
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 *
 * @see private/vendor/laravel/framework/src/Illuminate/Support/helpers.php:131
 */
function env($key, $default = null)
{
    switch (true) {
        case array_key_exists($key, $_ENV):
            return $_ENV[$key];
        case array_key_exists($key, $_SERVER):
            return $_SERVER[$key];
        default:
            /*
            $value = getenv($key);
            return $value === false ? $default : $value; // switch getenv default to null
            */
            return $default;
    }
}
