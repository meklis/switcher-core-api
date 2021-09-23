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
use SwitcherCore\Switcher\CoreConnector;
use SwitcherCore\Switcher\Device;

class ModelByKey extends Action
{
    /**
     * @var ModelCollector
     */
    protected $modelCollector;

    /**
     * @var ModuleCollector
     */
    protected $moduleCollector;

    /**
     * @param ModelCollector $collector
     * @param LoggerInterface $logger
     */

    function __construct(ModuleCollector $moduleCollector, ModelCollector $modelCollector, LoggerInterface $logger)
    {
        $this->moduleCollector = $moduleCollector;
        $this->modelCollector = $modelCollector;
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $model = $this->modelCollector->getModelByKey($this->request->getAttribute('key'));
        $modules = $model->getModulesListAssoc();
        return  $this->respondWithData(
           [
               'name' => $model->getName(),
               'key' => $model->getKey(),
               'ports' => $model->getPorts(),
               'extra' => $model->getExtra(),
               'detect' => $model->getDetect(),
               'device_type' => $model->getDeviceType(),
               'modules' => $model->getModulesList(),
               'module_classes' => $modules,
           ]
        );
    }
}