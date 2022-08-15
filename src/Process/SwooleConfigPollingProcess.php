<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Process;

use Imi\App;
use Imi\ConfigCenter\Event\Param\ConfigChangeEventParam;
use Imi\ConfigCenter\Event\Param\PipeConfigChangeEventParam;
use Imi\Event\Event;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Server\Server;
use Imi\Util\Imi;
use Imi\Util\ImiPriority;

if (Imi::checkAppType('swoole'))
{
    /**
     * @Process("ConfigPollingProcess")
     */
    class SwooleConfigPollingProcess extends BaseProcess
    {
        public function run(\Swoole\Process $process): void
        {
            /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
            $configCenter = App::getBean('ConfigCenter');
            Event::on('IMI.CONFIG_CENTER.CONFIG.CHANGE', [$this, 'onConfigChange']);
            $configCenter->startPolling();
            $channel = new \Swoole\Coroutine\Channel();
            Event::on('IMI.PROCESS.END', function () use ($channel) {
                $channel->push(1);
            }, ImiPriority::IMI_MIN);
            $channel->pop();
        }

        public function onConfigChange(ConfigChangeEventParam $e): void
        {
            $eventData = [
                'data' => new PipeConfigChangeEventParam([
                    'driverName'  => $e->getDriver()->getName(),
                    'key'         => $e->getKey(),
                    'configKey'   => $e->getConfigKey(),
                    'value'       => $e->getValue(),
                    'parsedValue' => $e->getParsedValue(),
                ]),
            ];
            Server::sendMessage('CONFIG_CENTER.CONFIG.CHANGE', $eventData);
            foreach (ProcessManager::getProcessSetWithManager() as $item)
            {
                $item['process']->sendUnixSocketMessage('CONFIG_CENTER.CONFIG.CHANGE', $eventData);
            }
        }
    }
}
