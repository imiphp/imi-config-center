# imi-config-center

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-config-center.svg)](https://packagist.org/packages/imiphp/imi-config-center)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.8.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-config-center.svg)](https://github.com/imiphp/imi-config-center/blob/master/LICENSE)

## 介绍

此项目是 imi 框架的配置中心组件，仅包含抽象定义，无实现代码。请结合具体实现的配置组件使用。

> 正在开发中，随时可能修改，请勿用于生产环境！

**支持的配置中心：**

* [x] Nacos ([imi-nacos](https://github.com/imiphp/imi-nacos))

* [ ] Apollo

* [x] etcd ([imi-etcd](https://github.com/imiphp/imi-etcd))

* [ ] Consul

* [ ] Zookeeper

……

## 设计

### 监听模式

#### 工作进程模式

每个进程自己监听，适用于 Swoole 环境。

#### 进程模式

由一个专门的进程监听，并通知到其它进程。适用于 Swoole、Workerman 环境。

对配置中心压力更小。

---

> php-fpm 模式比较特殊，是走的文件缓存逻辑。超过一定时间才去请求配置中心，获取数据，实时性有一定影响。

### 监听方式

#### 客户端轮询

客户端定时请求配置中心，对配置中心服务端压力较大，但是最为通用。

#### 服务端推送（长轮询）

如果配置中心支持服务端推送（长轮询），建议用这个方式，对配置中心服务端压力较小。

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.8.0
- [imi](https://www.imiphp.com/) >= 2.1

## 版权信息

`imi-config-center` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.1/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
