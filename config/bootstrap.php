<?php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// If APP_ENV is already set (injected by SymfonyCloud), skip loading the .env file
if (!isset($_SERVER['APP_ENV']) && class_exists(Dotenv::class)) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
