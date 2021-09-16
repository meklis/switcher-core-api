<?php

namespace App\Application\Actions\SwitcherCore;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Request;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use SwitcherCore\Config\ModelCollector;
use SwitcherCore\Switcher\CoreConnector;
use SwitcherCore\Switcher\Device;

class ModelByKey extends Action
{
    /**
     * @var ModelCollector
     */
    protected $collector;

    function __construct(ModelCollector $collector, LoggerInterface $logger)
    {
        $this->collector = $collector;
        parent::__construct($logger);
    }

    protected function action(): Response
    {

        return  $this->respondWithData(
           $this->collector->getModelByKey($this->request->getAttribute('key'))
        );
    }
}