<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:50
 */

namespace GoSwoole\Examples\Service;

use DI\Annotation\Inject;
use GoSwoole\Examples\Model\User;
use GoSwoole\Plugins\Cache\Annotation\Cacheable;
use GoSwoole\Plugins\Cache\Annotation\CacheEvict;
use Monolog\Logger;

class UserService
{
    /**
     * @Inject()
     * @var \MysqliDb
     */
    private $db;

    /**
     * @Inject()
     * @var Logger
     */
    private $log;

    /**
     * get操作创建缓存
     * @Cacheable(key="$p[0]",namespace="user")
     * @param $id
     * @return User|null
     */
    public function getUser($id)
    {
        $this->log->debug("获取User:$id");
        $result = $this->db->where("id", $id)->get("user", 1);
        if (count($result) > 0) {
            return new User($result[0]);
        } else {
            return null;
        }
    }

    /**
     * update操作修改缓存
     * @CacheEvict(key="$p[0]->id",namespace="user")
     * @param User $user
     */
    public function updateUser(User $user)
    {
        $this->db->update("user",$user->buildToArray());
    }
}