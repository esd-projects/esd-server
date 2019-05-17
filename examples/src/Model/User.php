<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:50
 */

namespace ESD\Examples\Model;

use ESD\Go\GoModel;
use ESD\Plugins\Validate\Annotation\Validated;

class User extends GoModel
{
    /**
     * @Validated(required=true,integer=true)
     * 用户id
     */
    public $id;

    /**
     * @Validated(regexp="/^((0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?)$|^(0?[1][3578][0-9]{9})$|^(400[0-9]{7})$/")
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

    /**
     * 获取数据库表名
     * @return string
     */
    public static function getTableName(): string
    {
        return "user";
    }

    /**
     * 获取主键名
     * @return string
     */
    public static function getPrimaryKey(): string
    {
        return "id";
    }

    public function rules()
    {
        return[
            ['id', 'int'],
        ];
    }

    /**
     * 获取数据源名
     * @return string
     */
    public static function getSelectDb(): string
    {
        return "default";
    }
}