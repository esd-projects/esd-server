<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:50
 */

namespace GoSwoole\Examples\Service;

use DI\Annotation\Inject;
use GoSwoole\BaseServer\Exception;
use GoSwoole\Examples\Model\User;
use GoSwoole\Plugins\Cache\Annotation\Cacheable;
use GoSwoole\Plugins\Cache\Annotation\CacheEvict;
use GoSwoole\Plugins\Mysql\Annotation\Transactional;
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
     * @Transactional()
     * @CacheEvict(key="$p[0]->id",namespace="user")
     * @param User $user
     * @return User|null
     * @throws Exception
     */
    public function updateUser(User $user)
    {
        if (empty($user->id)) {
            throw new Exception("User的id不能为空");
        }
        $this->db->where("id", $user->id)->update("user", $user->buildToArray());
        return $this->getUser($user->id);
    }
}