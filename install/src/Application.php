<?php
namespace App;
use ESD\Go\GoApplication;
class Application
{
    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\Core\Exception
     * @throws \ESD\Core\Plugins\Config\ConfigException
     * @throws \ReflectionException
     */
    public static function main()
    {
        GoApplication::runApp(Application::class);
        //如果要添加插件和切片使用下面的代码启动
        /* $goApp = new GoApplication();
         $goApp->addPlug(new EasyRoutePlugin());
         $goApp->addAspect(new RouteAspect());
         $goApp->run(Application::class);*/
    }
}
