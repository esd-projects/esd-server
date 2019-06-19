<?php

namespace ESD\Examples\Controller;

use ESD\Go\GoController;
use ESD\Plugins\Cache\GetCache;
use ESD\Plugins\EasyRoute\GetHttp;
use ESD\Plugins\Mysql\GetMysql;
use ESD\Plugins\Redis\GetRedis;
use ESD\Plugins\Session\GetSession;


/**
 * Class Base
 * @package app\Controller
 */
class Base extends GoController
{
    use GetSession;
    use GetRedis;
    use GetHttp;
    use GetMysql;
    use GetCache;
}