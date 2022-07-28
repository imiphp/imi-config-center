<?php

declare(strict_types=1);

namespace Imi\ConfigCenter;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\ConfigCenter\Contract\IConfigDriver;
use Imi\ConfigCenter\Enum\Mode;
use Imi\Event\Event;
use Imi\Timer\Timer;
use Imi\Util\Imi;
use InvalidArgumentException;
use SimpleXMLElement;

/**
 * @Bean("ConfigCenter")
 */
class ConfigCenter
{
    /**
     * 配置列表.
     */
    protected array $configs = [];

    protected int $mode = Mode::WORKER;

    /**
     * @var IConfigDriver[]
     */
    private array $drivers = [];

    private bool $running = false;

    private bool $timerRunning = false;

    /**
     * @var array<string, float>
     */
    private array $lastPollingTimes = [];

    public function __init(): void
    {
        foreach ($this->configs as $name => $config)
        {
            if (!isset($config['driver']))
            {
                throw new InvalidArgumentException('@beans.ConfigCenter.configs.' . $name . '.driver not found');
            }
            /** @var IConfigDriver $driver */
            $driver = $this->drivers[$name] = new $config['driver']($name, $config);
            foreach ($config['configs'] ?? [] as $imiConfigKey => $item)
            {
                $driver->listen($imiConfigKey, $item['key'] ?? $imiConfigKey, $item);
            }
        }
    }

    /**
     * 拉取全部或指定配置.
     */
    public function pull(?string $name = null, ?string $key = null, bool $enableCache = true): void
    {
        $pullAll = null === $key && null === $name;
        foreach (null === $name ? $this->configs : [$this->configs[$name] ?? []] as $name => $config)
        {
            if (!isset($config['driver']))
            {
                throw new InvalidArgumentException('@beans.ConfigCenter.configs.' . $name . '.driver not found');
            }
            if (!isset($this->drivers[$name]))
            {
                throw new InvalidArgumentException(sprintf('Invalid driver name %s', $name));
            }
            $driver = $this->drivers[$name];
            if ($pullAll)
            {
                $driver->pull();
            }
            foreach ($config['configs'] ?? [] as $imiConfigKey => $item)
            {
                try
                {
                    $value = $driver->get($item['key'] ?? $imiConfigKey, $enableCache, $item);
                }
                catch (\Throwable $th)
                {
                    $value = null;
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($th);
                }
                $this->setConfig($imiConfigKey, $value);
            }
        }
    }

    public function startPolling(): void
    {
        if ($this->running)
        {
            return;
        }
        $this->running = true;
        Event::on('IMI.PROCESS.END', function () {
            $this->running = false;
        });
        if (Imi::checkAppType('swoole'))
        {
            foreach ($this->configs as $name => $config)
            {
                if (!isset($config['driver']))
                {
                    throw new InvalidArgumentException('@beans.ConfigCenter.configs.' . $name . '.driver not found');
                }
                if (!isset($this->drivers[$name]))
                {
                    throw new InvalidArgumentException(sprintf('Invalid driver name %s', $name));
                }
                imigo(fn () => $this->polling($this->drivers[$name], $config));
            }
        }
        else
        {
            Timer::tick(1000, function () {
                if ($this->timerRunning)
                {
                    return;
                }
                $this->timerRunning = true;
                try
                {
                    foreach ($this->drivers as $name => $driver)
                    {
                        $interval = $this->configs[$name]['pollingInterval'] ?? 10;
                        if (microtime(true) - ($this->lastPollingTimes[$name] ?? 0) >= $interval)
                        {
                            // 客户端轮询
                            $driver->polling();
                            $this->lastPollingTimes[$name] = microtime(true);
                        }
                    }
                }
                finally
                {
                    $this->timerRunning = false;
                }
            });
        }
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @return IConfigDriver[]
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    public function getDriver(string $name): IConfigDriver
    {
        if (!isset($this->drivers[$name]))
        {
            throw new InvalidArgumentException(sprintf('ConfigDriver %s not found', $name));
        }

        return $this->drivers[$name];
    }

    /**
     * @param mixed $parsedValue
     */
    public function setConfig(string $configKey, $parsedValue): void
    {
        if (!\is_array($parsedValue))
        {
            if ($parsedValue instanceof SimpleXMLElement)
            {
                $parsedValue = json_decode(json_encode($parsedValue), true);
            }
            else
            {
                $parsedValue = (array) $parsedValue;
            }
        }
        Config::setConfig($configKey, $parsedValue);
    }

    protected function polling(IConfigDriver $driver, array $config): void
    {
        try
        {
            if ($driver->isSupportServerPush())
            {
                if ($driver->isListening())
                {
                    $driver->stopListner();
                }
                else
                {
                    // 服务端推送
                    Event::on('IMI.PROCESS.END', function () use ($driver) {
                        $driver->stopListner();
                    });
                }
                $driver->startListner();
            }
            else
            {
                $interval = $config['pollingInterval'] ?? 10;
                while ($this->running)
                {
                    // 客户端轮询
                    $driver->polling();
                    sleep($interval);
                }
            }
        }
        catch (\Throwable $th)
        {
            // @phpstan-ignore-next-line
            App::getBean('ErrorLog')->onException($th);
            if (Imi::checkAppType('swoole'))
            {
                imigo(fn () => $this->polling($driver, $config));
            }
            else
            {
                $this->polling($driver, $config);
            }
        }
    }

    public function getMode(): int
    {
        return $this->mode;
    }
}
