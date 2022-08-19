<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Util\Imi;

/**
 * 应用运行时加载配置.
 *
 * @Listener(eventName="IMI.APP_RUN", priority=19940311)
 */
class ConfigCenterPull implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
        $configCenter = App::getBean('ConfigCenter');
        try {
            $configCenter->pull(null, null, Imi::checkAppType('fpm'));
        } catch(\Throwable $th) {
            Log::error($th);
        }
    }
}
