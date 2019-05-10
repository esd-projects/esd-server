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
     * @throws Exception
     * @throws \GoSwoole\Plugins\Mysql\MysqlException
     */
    public function getUser($id)
    {
        return User::select($id);
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
        $user->updateSelective();
        return $this->getUser($user->id);
    }
}