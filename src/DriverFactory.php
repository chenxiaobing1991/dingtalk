<?php


namespace Cxb\DingTalk;

use Cxb\DingTalk\Contract\DriverInterface;
use Cxb\DingTalk\Contract\ManagerInterface;
use Cxb\DingTalk\Exception\BusinessException;
use Hyperf\Contract\ConfigInterface;

/**
 * Class DriverFactory
 * @package Cxb\DingTalk
 */
final class DriverFactory
{
    private ?array $config;

    private ?array $drivers;

    /**
     * DriverFactory constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config->get('dingtalk');
        $this->drivers = [];
        foreach ($this->config['drivers'] as $name => $info) {
            $this->set($name, new Driver($info));
        }
    }

    /**
     * @param string|null $name
     */
    public function get(?string $name = null): ManagerInterface
    {
        $name = $name === null ? $this->defaultDriver() : $name;
        if (!$this->has($name))
            throw new BusinessException(sprintf('[%s] driver is support', $name));
        return $this->drivers[$name];
    }

    /**
     * @param string $name
     * @param ManagerInterface $manager
     */
    public function set(string $name, ManagerInterface $manager): void
    {
        $this->drivers[$name] = $manager;
    }

    /**
     * @return string|null
     */
    private function defaultDriver(): ?string
    {
        return $this->config['default']['driver'] ?? null;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->drivers);
    }

    /**
     * @param string $name
     * @return DriverInterface
     */
    public function __get(string $name): DriverInterface
    {
        $driver = $this->get();
        return $driver->{$name};
    }
}