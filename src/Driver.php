<?php


namespace Cxb\DingTalk;


use Cxb\DingTalk\Contract\DriverInterface;
use Cxb\DingTalk\Contract\ManagerInterface;
use Cxb\DingTalk\Driver\DepartmentDriver;
use Cxb\DingTalk\Driver\MessageDriver;
use Cxb\DingTalk\Driver\UserDriver;
use Cxb\DingTalk\Exception\BusinessException;
use function Hyperf\Support\make;

/**
 * Class Driver
 * @package Cxb\DingTalk
 */
class Driver implements DriverInterface, ManagerInterface
{
    /**
     * @var array|string[]
     */
    private array $alias = [
        'user' => UserDriver::class,
        'dept' => DepartmentDriver::class,
        'async' => MessageDriver::class
    ];

    private ?array $drivers;

    /**
     * Driver constructor.
     * @param array $config
     */
    public function __construct(protected array $config)
    {
        $this->drivers = [];
        $alias = $config['alias'] ?? null;
        if (!empty($alias) && is_array($alias))
            $this->alias = array_merge($this->alias, $alias);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return new Config($this->config);
    }


    /**
     * @param string $name
     * @return DriverInterface
     */
    public function get(string $name): DriverInterface
    {
        if (!class_exists($name))
            throw new \Exception(sprintf('%s is support', $name));
        return isset($this->drivers[$name]) ? $this->drivers[$name] : ($this->drivers[$name] = make($name, ['config' => $this->getConfig(), 'driver' => $this]));
    }

    /**
     * @param string $name
     * @return DriverInterface
     */
    public function __get(string $name): DriverInterface
    {
        if (!isset($this->alias[$name])) {
            throw new BusinessException("{$name} is invalid.");
        }
        return $this->get($this->alias[$name]);
    }
}