<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/28
 * Time: 18:28
 */

namespace ESD\Go;


use DI\Annotation\Inject;
use ESD\BaseServer\Server\Beans\Request;
use ESD\Plugins\EasyRoute\Controller\EasyController;
use ESD\Plugins\Security\GetSecurity;
use ESD\Plugins\Session\HttpSession;
use ESD\Plugins\Whoops\WhoopsConfig;
use Inhere\Validate\ValidationTrait;

abstract class GoController extends EasyController
{
    use ValidationTrait;
    use GetSecurity;
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