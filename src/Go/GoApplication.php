<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:14
 */

namespace ESD\Go;


use ESD\Core\PlugIn\AbstractPlugin;
use ESD\Core\Plugins\Logger\GetLogger;
use ESD\Core\Server\Config\ServerConfig;
use ESD\Core\Server\Process\Process;
use ESD\Plugins\Actuator\ActuatorPlugin;
use ESD\Plugins\Aop\AopConfig;
use ESD\Plugins\Aop\AopPlugin;
use ESD\Plugins\AutoReload\AutoReloadPlugin;
use ESD\Plugins\Cache\CachePlugin;
use ESD\Plugins\Console\ConsolePlugin;
use ESD\Plugins\CsvReader\CsvReaderPlugin;
use ESD\Plugins\EasyRoute\EasyRoutePlugin;
use ESD\Plugins\Mysql\MysqlPlugin;
use ESD\Plugins\PHPUnit\PHPUnitPlugin;
use ESD\Plugins\ProcessRPC\ProcessRPCPlugin;
use ESD\Plugins\Redis\RedisPlugin;
use ESD\Plugins\Saber\SaberPlugin;
use ESD\Plugins\Scheduled\ScheduledPlugin;
use ESD\Plugins\Security\SecurityPlugin;
use ESD\Plugins\Session\SessionPlugin;
use ESD\Plugins\Topic\TopicPlugin;
use ESD\Plugins\Uid\UidPlugin;
use ESD\Plugins\Whoops\WhoopsPlugin;
use ESD\Server\Co\Server;

class GoApplication extends Server
{
    use GetLogger;

    /**
     * Application constructor.
     * @throws \DI\DependencyException
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct(new ServerConfig(), GoPort::class, GoProcess::class);
        $this->addPlug(new ConsolePlugin());
        $this->addPlug(new EasyRoutePlugin());
        $this->addPlug(new ScheduledPlugin());
        $this->addPlug(new RedisPlugin());
        $this->addPlug(new MysqlPlugin());
        $this->addPlug(new AutoreloadPlugin());
        $this->addPlug(new AopPlugin());
        $this->addPlug(new SaberPlugin());
        $this->addPlug(new ActuatorPlugin());
        $this->addPlug(new WhoopsPlugin());
        $this->addPlug(new SessionPlugin());
        $this->addPlug(new CachePlugin());
        $this->addPlug(new SecurityPlugin());
        $this->addPlug(new PHPUnitPlugin());
        $this->addPlug(new ProcessRPCPlugin());
        $this->addPlug(new UidPlugin());
        $this->addPlug(new TopicPlugin());
        $this->addPlug(new CsvReaderPlugin());
        //默认添加Go命名空间的aop
        $aopConfig = new AopConfig(__DIR__);
        $aopConfig->merge();
        $this->configure();
        $this->start();
    }

    /**
     * @param AbstractPlugin $plugin
     * @throws \ESD\Core\Exception
     */
    public function addPlug(AbstractPlugin $plugin)
    {
        $this->getPlugManager()->addPlug($plugin);
    }

    /**
     * 所有的配置插件已初始化好
     * @return mixed
     */
    public function configureReady()
    {
        $this->info("configureReady");
    }

    public function onStart()
    {
        $this->info("onStart");
    }

    public function onShutdown()
    {
        $this->info("onShutdown");
    }

    public function onWorkerError(Process $process, int $exit_code, int $signal)
    {
        return;
    }

    public function onManagerStart()
    {
        $this->info("onManagerStart");
    }

    public function onManagerStop()
    {
        $this->info("onManagerStop");
    }
}