<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\ConfigCenter\Event\Param\PipeConfigChangeEventParam;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * 进程通信的配置更改事件.
 *
 * @Listener("IMI.PIPE_MESSAGE.CONFIG_CENTER.CONFIG.CHANGE")
 * @Listener("IMI.PROCESS.PIPE_MESSAGE")
 */
class PipeConfigChangeListener implements IEventListener
{
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        if ('IMI.PROCESS.PIPE_MESSAGE' === $e->getEventName() && 'CONFIG_CENTER.CONFIG.CHANGE' !== ($data['action'] ?? ''))
        {
            return;
        }
        if (!isset($data['data']['data']))
        {
            return;
        }
        /** @var \Imi\ConfigCenter\Event\Param\PipeConfigChangeEventParam|array $data */
        $data = $data['data']['data'];
        if (\is_array($data))
        {
            $data = new PipeConfigChangeEventParam($data);
        }
        /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
        $configCenter = App::getBean('ConfigCenter');
        $configCenter->setConfig($data->getConfigKey(), $data->getParsedValue());
    }
}
