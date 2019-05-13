<?php

namespace Test;
use DI\Annotation\Inject;
use ESD\Examples\Service\UserService;
use ESD\Plugins\PHPUnit\GoTestCase;

/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/9
 * Time: 16:18
 */

class UserServiceTest extends GoTestCase
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;
    public function testPhpUnitClassSay()
    {
        $user = $this->userService->getUser(10000);
        $this->assertEquals($user->id, 10000);
    }
}