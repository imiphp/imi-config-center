<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Event\Param;

use Imi\Util\Traits\TDataToProperty;
use JsonSerializable;

class PipeConfigChangeEventParam implements JsonSerializable
{
    use TDataToProperty;

    protected string $driverName = '';

    protected string $configKey = '';

    protected string $key = '';

    protected string $value = '';

    /**
     * @var mixed
     */
    protected $parsedValue = null;

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        // @phpstan-ignore-next-line
        foreach ($this as $k => $v)
        {
            $data[$k] = $v;
        }

        return $data;
    }

    public function getDriverName(): string
    {
        return $this->driverName;
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function getKey(): string
    {
        return $this->key;
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
