<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/28
 * Time: 17:46
 */

namespace ESD\Go;


use ESD\Core\Message\Message;
use ESD\Core\Server\Process\Process;
use ESD\Server\Co\Server;

class GoProcess extends Process
{

    /**
     * 在onProcessStart之前，用于初始化成员变量
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function init()
    {
        $this->log = Server::$instance->getLog();
    }

    public function onProcessStart()
    {
        $this->log->info("onProcessStart");
    }

    public function onProcessStop()
    {
        $this->log->info("onProcessStop");
    }

    public function onPipeMessage(Message $message, Process $fromProcess)
    {
        return;
    }
}