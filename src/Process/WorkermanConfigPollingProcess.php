<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Process;

use Imi\App;
use Imi\ConfigCenter\Event\Param\ConfigChangeEventParam;
use Imi\ConfigCenter\Event\Param\PipeConfigChangeEventParam;
use Imi\Event\Event;
use Imi\Util\Imi;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\BaseProcess;
use Imi\Workerman\Server\Server;
use Workerman\Worker;

if (Imi::checkAppType('workerman'))
{
    /**
     * @Process("ConfigPollingProcess")
     */
    class WorkermanConfigPollingProcess extends BaseProcess
    {
        public function run(Worker $worker): void
        {
            /** @var \Imi\ConfigCenter\ConfigCenter $configCenter */
            $configCenter = App::getBean('ConfigCenter');
            Event::on('IMI.CONFIG_CENTER.CONFIG.CHANGE', [$this, 'onConfigChange']);
            $configCenter->startPolling();
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
        }
    }
}
