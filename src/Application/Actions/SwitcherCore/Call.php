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
//
//        $this->metrics->add('calling_devices', 1, [
//            'ip' => (string)$request->getDevice()->getIp(),
//            'module' =>  (string)$request->getModule(),
//        ]);
        if($error) {
            throw $error;
        }
        return  $this->respondWithData($data);
    }
}