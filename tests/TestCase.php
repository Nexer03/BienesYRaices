<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        $shouldFallbackToDatabase = ! extension_loaded('pdo_sqlite');
        $connection = null;
        $database = null;

        if ($shouldFallbackToDatabase) {
            $configuredConnection = env('DB_CONNECTION', 'mysql');
            $connection = env(
                'TEST_DB_CONNECTION',
                $configuredConnection === 'sqlite' ? 'mysql' : $configuredConnection
            );
            $database = env('TEST_DB_DATABASE', env('DB_DATABASE', 'laravel'));

            putenv("DB_CONNECTION={$connection}");
            $_ENV['DB_CONNECTION'] = $connection;
            $_SERVER['DB_CONNECTION'] = $connection;

            putenv("DB_DATABASE={$database}");
            $_ENV['DB_DATABASE'] = $database;
            $_SERVER['DB_DATABASE'] = $database;
        }

        parent::setUp();

        if ($shouldFallbackToDatabase && $connection) {
            Config::set('database.default', $connection);

            if (Config::has("database.connections.{$connection}")) {
                Config::set("database.connections.{$connection}.database", $database);
            }
        }
    }
}
