<?php

namespace App\Application\Actions\SwitcherCore;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Request;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use SwitcherCore\Switcher\CoreConnector;
use SwitcherCore\Switcher\Device;

class DetectModel extends Action
{
    /**
     * @var CoreConnector
     */
    protected $coreConnector;

    function __construct(CoreConnector $core, LoggerInterface $logger)
    {
        $this->coreConnector = $core;
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $dt = $this->getFormData();
        $device = Device::init(
            $dt['ip'],
            $dt['community'],
            $dt['login'],
            $dt['password'],
        );

        return  $this->respondWithData(
            $this->coreConnector->init(
                $device
            )->getDeviceMetaData()
        );
    }
}