<?php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Check for .env.local.php for local development, otherwise rely on environment variables
if (file_exists(dirname(__DIR__).'/.env.local.php')) {
    require dirname(__DIR__).'/.env.local.php';
} elseif (file_exists(dirname(__DIR__).'/.env') && class_exists(Dotenv::class)) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
} else {
    // Skip loading .env file if it doesn't exist
    putenv('APP_ENV=prod'); // Default to production environment
}
