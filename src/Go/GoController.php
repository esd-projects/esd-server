<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/4/28
 * Time: 18:28
 */

namespace GoSwoole\Go;


use GoSwoole\BaseServer\Server\Beans\Request;
use GoSwoole\Plugins\EasyRoute\Controller\EasyController;

abstract class GoController extends EasyController
{
    /**
     * @throws NoSupportRequestMethodException
     */
    public function assertGet()
    {
        if (strtolower($this->getRequest()->getServer(Request::SERVER_REQUEST_METHOD)) != "get") {
            throw new NoSupportRequestMethodException();
        }
    }

    /**
     * @throws NoSupportRequestMethodException
     */
    public function assertPost()
    {
        if (strtolower($this->getRequest()->getServer(Request::SERVER_REQUEST_METHOD)) != "post") {
            throw new NoSupportRequestMethodException();
        }
    }
}