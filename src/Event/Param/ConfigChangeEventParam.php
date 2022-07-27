<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Event\Param;

use Imi\ConfigCenter\Contract\IConfigDriver;
use Imi\Event\EventParam;

class ConfigChangeEventParam extends EventParam
{
    protected ?IConfigDriver $driver = null;

    protected string $configKey = '';

    protected string $key = '';

    protected string $value = '';

    /**
     * @var mixed
     */
    protected $parsedValue = null;

    protected array $options = [];

    public function getDriver(): ?IConfigDriver
    {
        return $this->driver;
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getParsedValue()
    {
        return $this->parsedValue;
    }
}
