<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:48
 */

namespace ESD\Examples\Controller;

use DI\Annotation\Inject;
use ESD\Core\Exception;
use ESD\Examples\Model\User;
use ESD\Examples\Service\UserService;
use ESD\Plugins\Cache\Annotation\CacheEvict;
use ESD\Plugins\EasyRoute\Annotation\GetMapping;
use ESD\Plugins\EasyRoute\Annotation\ModelAttribute;
use ESD\Plugins\EasyRoute\Annotation\PostMapping;
use ESD\Plugins\EasyRoute\Annotation\RequestBody;
use ESD\Plugins\EasyRoute\Annotation\RequestRawJson;
use ESD\Plugins\EasyRoute\Annotation\RestController;
use ESD\Plugins\Mysql\MysqlException;
use ESD\Plugins\Security\Annotation\PreAuthorize;
use ESD\Plugins\Security\Beans\Principal;
use ESD\Plugins\Validate\ValidationException;

/**
 * @RestController("user")
 * Class CUser
 * @package ESD\Examples\Controller
 */
class CUser extends Base
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    /**
     * @GetMapping("login")
     * @return string
     */
    public function login()
    {
        $this->response->withAddedHeader('content-type', 'text/html; charset=utf-8');
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
        $this->response->withAddedHeader('content-type', 'text/html; charset=utf-8');
        $this->session->invalidate();
        return "注销";
    }


    /**
     * @GetMapping("user")
     * @PreAuthorize("hasRole('user')")
     * @return User|null
     * @throws Exception
     * @throws MysqlException
     * @throws ValidationException
     */
    public function user()
    {
        $id = $this->queryRequire("id");
        return $this->userService->getUser($id);
    }

    /**
     * @GetMapping()
     * @CacheEvict(namespace="user",allEntries=true)
     */
    public function clearCache()
    {
        return "clear";
    }

    /**
     * 该模式仅接受 raw 格式的json字符串
     * @PostMapping("updateUser")
     * @PreAuthorize("hasRole('user')")
     * @return User|null
     * @throws Exception
     * @throws MysqlException
     * @throws ValidationException
     */
    public function updateUser()
    {
        $data = $this->postRawJson();
        return $this->userService->updateUser(new User($data));
    }


    /**
     * 该模式仅接受 raw 格式的json字符串
     * @PostMapping()
     * @PreAuthorize("hasRole('user')")
     * @RequestRawJson("data")
     * @param $data
     * @throws Exception
     * @throws MysqlException
     * @throws ValidationException]
     * @return User|null
     */
    public function updateUser2($data)
    {
        return $this->userService->updateUser(new User($data));
    }


    /**
     * 该模式仅支持 form-data 或 x-www-form-urlencoded
     * @PostMapping()
     * @PreAuthorize("hasRole('user')")
     * @ModelAttribute("user")
     * @param User $user
     * @return User|null
     * @throws MysqlException
     * @throws ValidationException
     */
    public function updateUser3(User $user)
    {
        $user->updateSelective();
        return $user::select($user->id);
    }

    /**
     * @PostMapping("insertUser")
     * @RequestBody("user")
     * @param User $user
     * @return User
     * @throws MysqlException
     * @throws ValidationException
     */
    public function insertUser(User $user)
    {
        $user->insert();
        return $user;
    }
}
