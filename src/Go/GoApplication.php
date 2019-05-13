<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:14
 */

namespace ESD\Go;


use ESD\BaseServer\Server\Config\ServerConfig;
use ESD\BaseServer\Server\Plugin\PluginInterface;
use ESD\BaseServer\Server\Process;
use ESD\BaseServer\Server\Server;
use ESD\Plugins\Actuator\ActuatorPlugin;
use ESD\Plugins\Aop\AopConfig;
use ESD\Plugins\Aop\AopPlugin;
use ESD\Plugins\AutoReload\AutoReloadPlugin;
use ESD\Plugins\Cache\CachePlugin;
use ESD\Plugins\Console\ConsolePlugin;
use ESD\Plugins\EasyRoute\EasyRoutePlugin;
use ESD\Plugins\Mysql\MysqlPlugin;
use ESD\Plugins\PHPUnit\PHPUnitPlugin;
use ESD\Plugins\Redis\RedisPlugin;
use ESD\Plugins\Saber\SaberPlugin;
use ESD\Plugins\Scheduled\ScheduledPlugin;
use ESD\Plugins\Security\SecurityPlugin;
use ESD\Plugins\Session\SessionPlugin;
use ESD\Plugins\Whoops\WhoopsPlugin;

class GoApplication extends Server
{
    /**
     * Application constructor.
     * @throws \DI\DependencyException
     * @throws \ESD\BaseServer\Exception
     * @throws \ESD\BaseServer\Server\Exception\ConfigException
     * @throws \ReflectionException
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
        $aopConfig = new AopConfig(__DIR__);
        $aopConfig->merge();
        $this->configure();
        $this->start();
    }

    /**
     * @param PluginInterface $plugin
     * @throws \ESD\BaseServer\Exception
     */
    public function addPlug(PluginInterface $plugin)
    {
        $this->getPlugManager()->addPlug($plugin);
    }

    /**
     * 所有的配置插件已初始化好
     * @return mixed
     */
    public function configureReady()
    {
        $this->log->info("configureReady");
    }

    public function onStart()
    {
        $this->log->info("onStart");
    }

    public function onShutdown()
    {
        $this->log->info("onShutdown");
    }

    public function onWorkerError(Process $process, int $exit_code, int $signal)
    {
        return;
    }

    public function onManagerStart()
    {
        $this->log->info("onManagerStart");
    }

    public function onManagerStop()
    {
        $this->log->info("onManagerStop");
    }
}