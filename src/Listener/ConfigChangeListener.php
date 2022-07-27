<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\ConfigCenter\Event\Param\ConfigChangeEventParam;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * 配置更改事件.
 *
 * @Listener("IMI.CONFIG_CENTER.CONFIG.CHANGE")
 */
class ConfigChangeListener implements IEventListener
{
    /**
     * @param ConfigChangeEventParam $e
     */
    public function handle(EventParam $e): void
    {
        /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
        $configCenter = App::getBean('ConfigCenter');
        $configCenter->setConfig($e->getConfigKey(), $e->getParsedValue());
    }
}
