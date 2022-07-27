<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

class Mode extends BaseEnum
{
    /**
     * 每个进程自己监听.
     *
     * @EnumItem("工作进程模式")
     */
    public const WORKER = 1;

    /**
     * 由一个专门的进程监听，并通知到其它进程.
     *
     * @EnumItem("进程模式")
     */
    public const PROCESS = 2;

    private function __construct()
    {
    }
}
