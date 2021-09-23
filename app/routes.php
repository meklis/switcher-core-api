<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Switcher-Core API');
        return $response;
    });

    $app->get('/rr/workers', \App\Application\Actions\RR\Workers::class );
    $app->delete('/rr/restart-all', \App\Application\Actions\RR\RestartAll::class );
    $app->get('/model/{key}', \App\Application\Actions\SwitcherCore\ModelByKey::class);
    $app->get('/modules', \App\Application\Actions\SwitcherCore\Modules::class);
    $app->post('/detect', \App\Application\Actions\SwitcherCore\DetectModel::class);
    $app->post('/call', \App\Application\Actions\SwitcherCore\Call::class );
    $app->post('/call-batch', \App\Application\Actions\SwitcherCore\CallBatch::class);
};
