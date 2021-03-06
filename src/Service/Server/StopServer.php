<?php
/**
 * This file is part of Mini.
 * @auth lupeng
 */

namespace Mini\Service\Server;

use Mini\Application;
use Mini\Support\Command;

class StopServer
{
    public function __construct($server = '')
    {
        go(static function () use ($server) {
            $process = 'bin/mini start ' . $server;
            if ((!$server || isset(Application::$mapping[$server])) && Command::has($process)) {
                Command::line('stop ' . (!$server ? $server : $server . ' ') . 'server successful.');
                Command::kill($process);
            } else {
                Command::line('no ' . (!$server ? $server : $server . ' ') . 'server running.');
            }
        });
    }
}