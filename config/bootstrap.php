<?php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

file_put_contents('/tmp/bootstrap_debug.log', "Starting bootstrap...\n", FILE_APPEND);

if (file_exists(dirname(__DIR__).'/.env.local.php')) {
    file_put_contents('/tmp/bootstrap_debug.log', "Using .env.local.php\n", FILE_APPEND);
    require dirname(__DIR__).'/.env.local.php';
} elseif (file_exists(dirname(__DIR__).'/.env') && class_exists(Dotenv::class)) {
    file_put_contents('/tmp/bootstrap_debug.log', "Loading .env file\n", FILE_APPEND);
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
} else {
    file_put_contents('/tmp/bootstrap_debug.log', "Using environment variables\n", FILE_APPEND);
    $_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? 'prod';
    $_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? false;
}
