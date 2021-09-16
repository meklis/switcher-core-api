<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Spiral\Goridge;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        SwitcherCore\Config\ModelCollector::class => function(ContainerInterface $c) {
            return \SwitcherCore\Config\ModelCollector::init(
                new \SwitcherCore\Config\Reader(
                    \SwitcherCore\Modules\Helper::getBuildInConfig()
                )
            );
        },
        Goridge\RPC\RPC::class => function(ContainerInterface $c) {
            return new Goridge\RPC\RPC(
                Goridge\Relay::create(\Spiral\RoadRunner\Environment::fromGlobals()->getRPCAddress())
            );
        },
        Spiral\RoadRunner\Metrics\Metrics::class => function (ContainerInterface $c) {
            return  new Spiral\RoadRunner\Metrics\Metrics($c->get(Spiral\Goridge\RPC\RPC::class));
        },
        SwitcherCore\Switcher\CoreConnector::class => function (ContainerInterface $c) {
            $coreConnector = new \SwitcherCore\Switcher\CoreConnector(
                \SwitcherCore\Modules\Helper::getBuildInConfig()
            );
            $coreConnector->setLogger($c->get(LoggerInterface::class));
            return $coreConnector;
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);
};
