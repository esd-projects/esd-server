<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:50
 */

namespace GoSwoole\Examples\Model;
use GoSwoole\Go\GoModel;

class User extends GoModel
{
    /**
     * 用户id
     */
    public $id;

    /**
     * 手机号码
     */
    public $mobile;

    /**
     * 登陆名称
     */
    public $loginName;

    /**
     * 用户姓名
     */
    public $userName;
}