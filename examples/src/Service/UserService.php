<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/5/7
 * Time: 10:50
 */

namespace GoSwoole\Examples\Service;

use DI\Annotation\Inject;
use GoSwoole\Examples\Model\User;
use GoSwoole\Plugins\Mysql\MysqlPool;

class UserService
{
    /**
     * @Inject()
     * @var MysqlPool
     */
    private $mysql;

    public function getUser($id)
    {
        $result = $this->mysql->db()->where("id", $id)->get("user", 1);
        if (count($result) > 0) {
            return new User($result[0]);
        } else {
            return null;
        }
    }
}