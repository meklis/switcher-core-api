<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'displayTrace' => true,
                'logError'            => env('PRINT_ERRORS', false),
                'logErrorDetails'     => env('PRINT_ERRORS', false),
                'logger' => [
                    'name' => 'sw-core-api',
                    'path' => __DIR__ . '/../logs/sw-core-api.log',
                    'level' => Logger::DEBUG,
                ],
                'metrics' => env('ENABLE_METRICS', true),
            ]);
        }
    ]);
};
