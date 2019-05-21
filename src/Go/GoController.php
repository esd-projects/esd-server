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
use ESD\Plugins\EasyRoute\GetBoostSend;
use ESD\Plugins\Security\GetSecurity;
use ESD\Plugins\Session\HttpSession;
use ESD\Plugins\Uid\GetUid;
use ESD\Plugins\Whoops\WhoopsConfig;

class GoController extends EasyController
{
    use GetSecurity;
    use GetBoostSend;
    use GetUid;
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
     * @param string|null $has
     * @return bool
     */
    public function isGet(string $has = null): bool
    {
        if (strtolower($this->request->getServer(Request::SERVER_REQUEST_METHOD)) == "get") {
            if (!is_null($has)) {
                if (!is_null($this->request->getGet($has))) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * @param string|null $has
     * @return bool
     */
    public function isPost(string $has = null): bool
    {
        if (strtolower($this->request->getServer(Request::SERVER_REQUEST_METHOD)) == "post") {
            if (!is_null($has)) {
                if (!is_null($this->request->getPost($has))) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
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

    /**
     * 找不到方法时调用
     * @param $methodName
     * @return mixed
     */
    protected function defaultMethod(?string $methodName)
    {
        return "";
    }

    /**
     * 发送给uid
     * @param $uid
     * @param $data
     */
    protected function sendToUid($uid, $data)
    {
        $fd = $this->getUidFd($uid);
        if ($fd !== false) {
            $this->autoBoostSend($fd, $data);
        } else {
            $this->log->warn("通过uid寻找fd不存在");
        }
    }
}