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