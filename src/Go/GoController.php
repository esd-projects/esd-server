<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/28
 * Time: 18:28
 */

namespace GoSwoole\Go;


use DI\Annotation\Inject;
use GoSwoole\BaseServer\Server\Beans\Request;
use GoSwoole\Plugins\EasyRoute\Controller\EasyController;
use GoSwoole\Plugins\Session\HttpSession;
use GoSwoole\Plugins\Whoops\WhoopsConfig;

abstract class GoController extends EasyController
{
    /**
     * @Inject()
     * @var HttpSession
     */
    protected $session;

    /**
     * @Inject()
     * @var WhoopsConfig
     */
    protected $whoopsConfig;

    /**
     * @throws NoSupportRequestMethodException
     */
    public function assertGet()
    {
        if (strtolower($this->request->getServer(Request::SERVER_REQUEST_METHOD)) != "get") {
            throw new NoSupportRequestMethodException();
        }
    }

    /**
     * @throws NoSupportRequestMethodException
     */
    public function assertPost()
    {
        if (strtolower($this->request->getServer(Request::SERVER_REQUEST_METHOD)) != "post") {
            throw new NoSupportRequestMethodException();
        }
    }

    public function onExceptionHandle(\Throwable $e)
    {
        if ($this->whoopsConfig->isEnable()) {
            throw $e;
        }
        return parent::onExceptionHandle($e);
    }
}