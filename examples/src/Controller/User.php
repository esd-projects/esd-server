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

    public function login()
    {
        $session = $this->getSession();
        if($session->isAvailable()){
            return "已登录".$session->getId().$session->getAttribute("test");
        }else{
            $session->refresh();
            $session->setAttribute("test","hello");
            return "登录".$session->getId().$session->getAttribute("test");
        }
    }

    public function logout()
    {
        $session = $this->getSession();
        $session->invalidate();
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
        $id = $this->getRequest()->getGetRequire("id");
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