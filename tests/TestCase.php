<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        $this->configureTestDatabaseEnvironment();

        parent::setUp();
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $this->configureTestDatabaseConfig($app);
    }

    /**
     * Ensure the testing environment points to a database connection
     * that is available when the SQLite driver is not present.
     */
    protected function configureTestDatabaseEnvironment(): void
    {
        $configuration = $this->determineTestDatabaseConfiguration();

        if (! $configuration) {
            return;
        }

        foreach ($configuration as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Configure the framework database connection before the RefreshDatabase
     * trait decides which driver should be used for the tests.
     */
    protected function configureTestDatabaseConfig($app): void
    {
        $configuration = $this->determineTestDatabaseConfiguration();

        if (! $configuration) {
            return;
        }

        $app['config']->set('database.default', $configuration['DB_CONNECTION']);

        if ($app['config']->has('database.connections.' . $configuration['DB_CONNECTION'])) {
            $app['config']->set(
                'database.connections.' . $configuration['DB_CONNECTION'] . '.database',
                $configuration['DB_DATABASE']
            );
        }
    }

    /**
     * Determine the fallback database connection that should be used when the
     * SQLite PDO driver is not installed.
     */
    protected function determineTestDatabaseConfiguration(): ?array
    {
        if (extension_loaded('pdo_sqlite')) {
            return null;
        }

        $configuredConnection = env('DB_CONNECTION', 'mysql');

        $connection = env(
            'TEST_DB_CONNECTION',
            $configuredConnection === 'sqlite' ? 'mysql' : $configuredConnection
        );

        $database = env('TEST_DB_DATABASE', env('DB_DATABASE', 'laravel'));

        return [
            'DB_CONNECTION' => $connection,
            'DB_DATABASE' => $database,
        ];
    }
}
