<?php

namespace App\Application\Actions\SwitcherCore;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Request;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Spiral\RoadRunner\Metrics\Metrics;
use SwitcherCore\Switcher\CoreConnector;
use SwitcherCore\Switcher\Device;

class CallBatch extends Action
{
    /**
     * @var CoreConnector
     */
    protected $coreConnector;

    /**
     * @var Metrics
     */
    protected $metrics;

    function __construct(Metrics $metrics, CoreConnector $core, LoggerInterface $logger)
    {
        $this->metrics = $metrics;
        $this->coreConnector = $core;
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $data = $this->getFormData();
        $responses = [];
        foreach ($data as $d) {
            $request = Request::init($d);
            $response = null;
            $error = null;
            $time_start = microtime(true);
            try {
                $response = $this->coreConnector->getOrInit(
                    $request->getDevice()
                )->action(
                    $request->getModule(),
                    (array) $request->getArguments()
                );
            } catch (\Throwable $e) {
                $error = [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString(),
                ];
            }
            $this->metrics->add('calling_devices_counter', 1, [
                (string)$request->getDevice()->getIp(),
                (string)$request->getModule(),
                (string)($error === null ? 'success' : 'failed')
            ]);
            $this->metrics->add('calling_devices_duration', (microtime(true) - $time_start), [
                (string)$request->getDevice()->getIp(),
                (string)$request->getModule(),
                (string)($error === null ? 'success' : 'failed')
            ]);
            $responses[] = [
              'error' => $error,
              'request' => $request->getAsArray(),
              'data'  => $response,
            ];
        }
        return $this->respondWithData($responses);
    }
}