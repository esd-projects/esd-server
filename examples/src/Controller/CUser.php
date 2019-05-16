<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:48
 */

namespace ESD\Examples\Controller;

use DI\Annotation\Inject;
use ESD\Examples\Model\User;
use ESD\Examples\Service\UserService;
use ESD\Go\GoController;
use ESD\Plugins\EasyRoute\Annotation\GetMapping;
use ESD\Plugins\EasyRoute\Annotation\PostMapping;
use ESD\Plugins\EasyRoute\Annotation\RestController;
use ESD\Plugins\Security\Annotation\PreAuthorize;
use ESD\Plugins\Security\Beans\Principal;

/**
 * @RestController("user")
 * Class CUser
 * @package ESD\Examples\Controller
 */
class CUser extends GoController
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

    /**
     * @GetMapping("login")
     * @return string
     */
    public function login()
    {
        $principal = new Principal();
        $principal->addRole("user");
        $principal->setUsername("user");
        $this->setPrincipal($principal);
        if ($this->session->isAvailable()) {
            return "已登录" . $this->session->getId() . $this->session->getAttribute("test");
        } else {
            $this->session->refresh();
            $this->session->setAttribute("test", "hello");
            return "登录" . $this->session->getId() . $this->session->getAttribute("test");
        }

    }

    /**
     * @GetMapping("logout")
     * @return string
     */
    public function logout()
    {
        $this->session->invalidate();
        return "注销";
    }

    /**
     * @GetMapping("user")
     * @PreAuthorize(value="hasRole('user')")
     * @return User
     * @throws \ESD\BaseServer\Exception
     */
    public function user()
    {
        $id = $this->request->getGetRequire("id");
        return $this->userService->getUser($id);
    }

    /**
     * @PostMapping("updateUser")
     * @PreAuthorize(value="hasRole('user')")
     * @return User|null
     * @throws \ESD\BaseServer\Exception
     * @throws \ESD\Go\NoSupportRequestMethodException
     */
    public function updateUser()
    {
        $this->assertPost();
        return $this->userService->updateUser(new User($this->request->getJsonBody()));
    }

    /**
     * 找不到方法时调用
     * @param $methodName
     * @return mixed
     */
    protected function defaultMethod(?string $methodName)
    {
        return "Hello";
    }
}