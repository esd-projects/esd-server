<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:14
 */

namespace GoSwoole\Go;


use GoSwoole\BaseServer\Server\Config\ServerConfig;
use GoSwoole\BaseServer\Server\Plugin\PluginInterface;
use GoSwoole\BaseServer\Server\Process;
use GoSwoole\BaseServer\Server\Server;
use GoSwoole\Plugins\Actuator\ActuatorPlugin;
use GoSwoole\Plugins\Aop\AopPlugin;
use GoSwoole\Plugins\AutoReload\AutoReloadPlugin;
use GoSwoole\Plugins\Cache\CachePlugin;
use GoSwoole\Plugins\Console\ConsolePlugin;
use GoSwoole\Plugins\EasyRoute\EasyRoutePlugin;
use GoSwoole\Plugins\Mysql\MysqlPlugin;
use GoSwoole\Plugins\Redis\RedisPlugin;
use GoSwoole\Plugins\Saber\SaberPlugin;
use GoSwoole\Plugins\Scheduled\ScheduledPlugin;
use GoSwoole\Plugins\Session\SessionPlugin;
use GoSwoole\Plugins\Whoops\WhoopsPlugin;

class GoApplication extends Server
{
    /**
     * Application constructor.
     * @throws \DI\DependencyException
     * @throws \GoSwoole\BaseServer\Exception
     * @throws \GoSwoole\BaseServer\Server\Exception\ConfigException
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
        $this->configure();
        $this->start();
    }

    /**
     * @param PluginInterface $plugin
     * @throws \GoSwoole\BaseServer\Exception
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