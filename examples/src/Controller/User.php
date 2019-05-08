<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/5/7
 * Time: 10:48
 */

namespace GoSwoole\Examples\Controller;

use DI\Annotation\Inject;
use GoSwoole\Examples\Service\UserService;
use GoSwoole\Go\GoController;

class User extends GoController
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    public function initialization(?string $controllerName, ?string $methodName)
    {
        parent::initialization($controllerName, $methodName);
        $this->response->addHeader("Content-type", "text/html;charset=UTF-8");
    }

    public function login()
    {
        if ($this->session->isAvailable()) {
            return "已登录" . $this->session->getId() . $this->session->getAttribute("test");
        } else {
            $this->session->refresh();
            $this->session->setAttribute("test", "hello");
            return "登录" . $this->session->getId() . $this->session->getAttribute("test");
        }
    }

    public function logout()
    {
        $this->session->invalidate();
        return "注销";
    }

    /**
     * @return \GoSwoole\Examples\Model\User
     * @throws \GoSwoole\Go\NoSupportRequestMethodException
     * @throws \GoSwoole\BaseServer\Exception
     */
    public function user()
    {
        $this->assertGet();
        $id = $this->request->getGetRequire("id");
        return $this->userService->getUser($id);
    }

    /**
     * 找不到方法时调用
     * @param $methodName
     * @return mixed
     */
    protected function defaultMethod(string $methodName)
    {
        return "Hello";
    }
}