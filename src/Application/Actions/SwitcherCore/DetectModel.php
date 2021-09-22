<?php

namespace App\Application\Actions\SwitcherCore;

use App\Application\Actions\Action;
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
        if(!isset($dt['ip'])) {
            throw new HttpBadRequestException($this->request, "IP is required");
        }
        if(!isset($dt['community'])) {
            throw new HttpBadRequestException($this->request, "Community is required");
        }
        if(!isset($dt['login'])) {
            $dt['login'] = '';
        }
        if(!isset($dt['password'])) {
            $dt['password'] = '';
        }
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