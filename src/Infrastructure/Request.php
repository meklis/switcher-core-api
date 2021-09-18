<?php

namespace App\Infrastructure;

use SwitcherCore\Switcher\Device;

class Request
{
    /**
     * @var string
     */
    protected $module;

    /**
     * @var Device
     */
    protected $device;

    /**
     * @var array
     */
    protected $arguments = null;

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return Device
     */
    public function getDevice(): Device
    {
        return $this->device;
    }

    /**
     * @param Device $device
     */
    public function setDevice(Device $device): void
    {
        $this->device = $device;
    }


    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments): void
    {
        $this->arguments = $arguments;

    }


    public static function init($data)
    {
        $request = new self();
        if(!isset($data['device']['login'])) {
            $data['device']['login'] = '';
            $data['device']['password'] = '';
        }
        if(!isset($data['device']['password'])) {
            $data['device']['password'] = '';
        }
        if (!isset($data['module'])) {
            throw new \InvalidArgumentException("Module is required");
        } else {
            $request->setModule($data['module']);
        }
        if (!isset($data['device'])) {
            throw new \InvalidArgumentException("Device is required");
        } else {
            $device = Device::init(
                $data['device']['ip'],
                $data['device']['community'],
                $data['device']['login'],
                $data['device']['password']
            );
            if (isset($data['device']['meta'])) {
                $params = $data['device']['meta'];
                $device->telnetPort = $params['telnet_port'];
                $device->telnetTimeout = $params['telnet_timeout_sec'];
                $device->mikrotikApiPort = $params['mikrotik_api_port'];
                $device->snmpTimeoutSec = $params['snmp_timeout_sec'];
                $device->snmpRepeats = $params['snmp_repeats'];
            }
            $request->setDevice($device);
        }
        if (isset($data['arguments'])) {
            $request->setArguments($data['arguments']);
        } else {
            $request->arguments = new \stdClass();
        }
        return $request;
    }

    public function getAsArray()
    {
        return [
            'device' => [
                'ip' => $this->device->getIp(),
                'community' => $this->device->getCommunity(),
                'login' => $this->device->getLogin(),
                'password' => $this->device->getPassword(),
                'meta' => $this->device->getMeta(),
            ],
            'module' => $this->module,
            'arguments' => (array) $this->arguments,
        ];
    }
}