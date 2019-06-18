<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 10:44
 */

namespace ESD\Examples;

use ESD\Go\GoApplication;
use ESD\Plugins\EasyRoute\Filter\CorsFilter;
use ESD\Plugins\EasyRoute\Filter\FilterManager;

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
        $application = new GoApplication();
        $application->run(Application::class);
    }

    /**
     * Application constructor.
     * @param FilterManager $filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $filterManager->addFilter(new CorsFilter());
    }
}