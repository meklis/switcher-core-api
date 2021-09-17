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

class Call extends Action
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
        $request = Request::init($this->getFormData());
        $error = null;
        $time_start = microtime(true);
        try {
            $data = $this->coreConnector->init(
                $request->getDevice()
            )->action(
                $request->getModule(),
                (array) $request->getArguments()
            );
        } catch (\Throwable $e) {
            $error = $e;
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
        if($error) {
            throw $error;
        }
        return  $this->respondWithData($data);
    }
}