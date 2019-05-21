<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:48
 */

namespace ESD\Examples\Controller;

use ESD\Go\GoController;
use ESD\Plugins\EasyRoute\Annotation\RequestMapping;
use ESD\Plugins\EasyRoute\Annotation\WsController;

/**
 * @WsController()
 * Class CUser
 * @package ESD\Examples\Controller
 */
class WebSocket extends GoController
{
    /**
     * @RequestMapping()
     */
    public function wsBindUid()
    {
        $this->bindUid($this->clientData->getFd(), "test1");
        return "test";
    }

    /**
     * @RequestMapping()
     * @return mixed|null
     */
    public function wsGetUid()
    {
        return $this->getFdUid($this->clientData->getFd());
    }

    /**
     * @RequestMapping()
     * @return mixed|null
     */
    public function send()
    {
        $this->sendToUid("test1","hello");
    }
}