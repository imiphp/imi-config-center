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
 * 配置监听进程.
 *
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 */
class ConfigCenterProcessPolling implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        if (Imi::checkAppType('fpm'))
        {
            return;
        }
        /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
        $configCenter = App::getBean('ConfigCenter');
        if (Mode::PROCESS === $configCenter->getMode())
        {
            /** @var \Imi\Process\AutoRunProcessManager $autoRunProcessManager */
            $autoRunProcessManager = App::getBean('AutoRunProcessManager');
            $autoRunProcessManager->add('ConfigPollingProcess', 'ConfigPollingProcess');
        }
    }
}
