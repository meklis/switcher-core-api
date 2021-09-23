<?php

namespace App\Application\Actions\SwitcherCore;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use SwitcherCore\Config\ModelCollector;
use SwitcherCore\Config\ModuleCollector;
use SwitcherCore\Config\Objects\Module;
use SwitcherCore\Switcher\CoreConnector;
use SwitcherCore\Switcher\Device;

class Modules extends Action
{
    /**
     * @var ModuleCollector
     */
    protected $moduleCollector;

    /**
     * @param ModelCollector $collector
     * @param LoggerInterface $logger
     */

    function __construct(ModuleCollector $moduleCollector, LoggerInterface $logger)
    {
        $this->moduleCollector = $moduleCollector;
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $modules = [];
        foreach ($this->moduleCollector->getAll() as $moduleName=>$module) {
            /**
             * @var $module Module
             */
            $modules[] = [
              'name' => $module->getName(),
              'arguments' => $module->getArguments(),
              'description' => $module->getDescr(),
            ];
        }
        return  $this->respondWithData($modules);
    }
}