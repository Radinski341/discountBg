<?php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Check if .env.local.php exists for local overrides; otherwise, rely on injected environment variables
if (file_exists(dirname(__DIR__).'/.env')) {
    require dirname(__DIR__).'/.env';
} elseif (class_exists(Dotenv::class)) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env', 'APP_ENV', 'prod');
}
