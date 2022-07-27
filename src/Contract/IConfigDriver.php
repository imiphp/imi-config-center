<?php

declare(strict_types=1);

namespace Imi\ConfigCenter\Contract;

interface IConfigDriver
{
    public function __construct(string $name, array $config);

    public function getName(): string;

    /**
     * 向配置中心推送配置.
     */
    public function push(string $key, string $value, array $options = []): void;

    /**
     * 从配置中心拉取所监听的数据.
     */
    public function pull(bool $enableCache = true): void;

    /**
     * 从配置中心获取配置原始数据.
     */
    public function getRaw(string $key, bool $enableCache = true, array $options = []): ?string;

    /**
     * 从配置中心获取配置处理后的数据.
     *
     * @return mixed
     */
    public function get(string $key, bool $enableCache = true, array $options = []);

    /**
     * 删除配置.
     *
     * @param string|string[] $keys
     */
    public function delete($keys, array $options = []): void;

    /**
     * 监听配置.
     */
    public function listen(string $imiConfigKey, string $key, array $options = []): void;

    /**
     * 执行一次轮询配置.
     */
    public function polling(): void;

    /**
     * 开始监听配置.
     */
    public function startListner(): void;

    /**
     * 停止监听配置.
     */
    public function stopListner(): void;

    /**
     * 是否正在监听.
     */
    public function isListening(): bool;

    /**
     * 获取驱动原始客户端.
     *
     * @return mixed
     */
    public function getOriginClient();

    public function isSupportServerPush(): bool;
}
