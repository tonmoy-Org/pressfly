<?php

if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    exit(
        'Your PHP version must be equal or higher than 8.1.0 to use the script. ' .
        'Please ask your hosting company to update it.'
    );
}

$extensions = [
    'pdo_mysql',
    'curl',
    'intl',
    'gd',
    'json',
    'openssl',
    'mbstring',
    'tokenizer',
    'ctype',
    'xml',
    'fileinfo',
    'bcmath',
];
foreach ($extensions as $extension) {
    if (!extension_loaded($extension)) {
        exit(
            "You must enable the <b>{$extension}</b> extension to use our script. " .
            "Please ask your hosting company to enable it."
        );
    }
}

/*
$functions = ['putenv', 'getenv']; // proc_open, proc_close, proc_get_status
foreach ($functions as $function) {
    if (!function_exists($function)) {
        exit(
            "You must enable the <b>{$function}</b> function to use the script. " .
            "Please ask your hosting company to enable it."
        );
    }
}
*/

// Check if tmp directory and its subdirectories are writable
$root = dirname(__DIR__);

$dirs = [
    $root . '/uploads/',
    $root . '/uploads/2019/',
    $root . '/private/',
    $root . '/private/bootstrap/cache/',
    $root . '/private/resources/lang/',
    $root . '/private/storage/',
    $root . '/private/storage/app/',
    $root . '/private/storage/app/public/',
    $root . '/private/storage/framework/',
    $root . '/private/storage/framework/cache/',
    $root . '/private/storage/framework/cache/data/',
    $root . '/private/storage/framework/sessions/',
    $root . '/private/storage/framework/testing/',
    $root . '/private/storage/framework/views/',
    $root . '/private/storage/logs/',
];
foreach ($dirs as $dir) {
    if (file_exists($dir) && !is_writable($dir)) {
        exit("<b>{$dir}</b> directory must be writable.");
    }
}
