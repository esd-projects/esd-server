<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/4/28
 * Time: 17:46
 */

namespace GoSwoole\Go;


use GoSwoole\BaseServer\Server\Message\Message;
use GoSwoole\BaseServer\Server\Process;
use GoSwoole\BaseServer\Server\Server;

class GoProcess extends Process
{

    /**
     * 在onProcessStart之前，用于初始化成员变量
     * @return mixed
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