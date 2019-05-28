<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 11:08
 */

namespace ESD\Go;


use ESD\Core\Exception;

class NoSupportRequestMethodException extends Exception
{
    public function __construct()
    {
        parent::__construct("不支持的请求方法", 0, null);
    }
}