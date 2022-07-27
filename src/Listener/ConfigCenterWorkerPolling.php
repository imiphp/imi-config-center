<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\ConfigCenter\Enum\Mode;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\Imi;

/**
 * Swoole 每个进程、Worker 轮询监听.
 *
 * @Listener(eventName="IMI.PROCESS.BEGIN", priority=19940311)
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START", priority=19940311)
 */
class ConfigCenterWorkerPolling implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        if (!Imi::checkAppType('swoole'))
        {
            return;
        }
        /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
        $configCenter = App::getBean('ConfigCenter');
        if (Mode::WORKER === $configCenter->getMode())
        {
            $configCenter->startPolling();
        }
    }
}
