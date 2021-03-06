<?php
/**
 * This file is part of Mini.
 * @auth lupeng
 */
declare(strict_types=1);

namespace Mini\Service\Server;

use Mini\Service\Server\Protocol\MQTT;
use Swoole\Server;

class MqttServer extends AbstractServer
{
    protected string $type = 'Mqtt';

    public function initialize(): void
    {
        $this->config = config('servers.mqtt');
        $this->worker_num = $this->config['settings']['worker_num'] ?? 1;
        $this->server = new Server($this->config['ip'], $this->config['port'], $this->config['mode']);
        $this->server->on('Receive', [$this, 'onReceive']);
        \Mini\Server::getInstance()->set(self::class, $this->server);
    }

    public function onReceive(Server $server, $fd, $fromId, $data): void
    {
        try {
            $data = MQTT::decode($data);
            if (is_array($data) && isset($data['cmd'])) {
                switch ($data['cmd']) {
                    case MQTT::PINGREQ: // 心跳请求
                        [$class, $func] = $this->config['receiveCallbacks'][MQTT::PINGREQ];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $data)) {
                            // 返回心跳响应
                            $server->send($fd, MQTT::getAck(['cmd' => 13]));
                        }
                        break;
                    case MQTT::DISCONNECT: // 客户端断开连接
                        [$class, $func] = $this->config['receiveCallbacks'][MQTT::DISCONNECT];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $data)) {
                            if ($server->exist($fd)) {
                                $server->close($fd);
                            }
                        }
                        break;
                    case MQTT::CONNECT: // 连接
                    case MQTT::PUBLISH: // 发布消息
                    case MQTT::SUBSCRIBE: // 订阅
                    case MQTT::UNSUBSCRIBE: // 取消订阅
                        [$class, $func] = $this->config['receiveCallbacks'][$data['cmd']];
                        $obj = new $class();
                        $obj->{$func}($server, $fd, $fromId, $data);
                        break;
                }
            } else {
                $server->close($fd);
            }
        } catch (\Exception $e) {
            $server->close($fd);
        }
    }
}
