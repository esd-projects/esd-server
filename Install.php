<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2019/5/20
 * Time: 10:26 AM
 */
$path = getcwd();
print_r("将在当前位置创建项目，是否确定(y/n)？\n");
if (count($argv) < 2 || $argv[1] != '-y') {
    $read = read();
    if (strtolower($read) != 'y') {
        exit();
    }
}
mkdir($path . '/src');
copy_dir(__DIR__ . "/install/resources", $path . '/resources');
updateComposer();
createStart();


exec("composer dump", $output);
print_r("根目录下 start_server.php 是启动文件，祝君使用愉快。\n");
exit();

function read()
{
    $fp = fopen('php://stdin', 'r');
    $input = fgets($fp, 255);
    fclose($fp);
    $input = chop($input);
    return $input;
}

function copy_dir($src, $dst, $force = false)
{
    $dir = opendir($src);
    if (!$dir) {
        print_r("$src 权限问题或目录不合法，安装错误\n");
        return false;
    }
    if (file_exists($dst) && $force == false) {
        print_r("$dst 目录已存在（跳过）\n");
        return false;
    }
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_dir($src . '/' . $file, $dst . '/' . $file);
                continue;
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
                $content = file_get_contents($dst . '/' . $file);
                $content = str_replace('namespace ESD\Examples', 'namespace app', $content);
                file_put_contents($dst . '/' . $file, $content);
            }
        }
    }
    closedir($dir);
    print_r("已创建$dst 目录\n");
    return true;
}

function createStart()
{
    global $path;
    $cfg = '<?php
namespace app;
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
';
    file_put_contents($path . '/src/Application.php', $cfg);

    $cfg = <<<EOF
<?php
require __DIR__ . '/vendor/autoload.php';
define("ROOT_DIR", __DIR__);
define("RES_DIR", __DIR__ . "/resources");

app\Application::main();
EOF;
    file_put_contents($path . '/start_server.php', $cfg);
}

function updateComposer()
{
    global $path;
    if (!$composer = file_get_contents($path . '/composer.json')) {
        exit('composer.json not found');
    }

    $composer = json_decode($composer, true);
    $composer['autoload']['psr-4']['app\\'] = 'src/';
    file_put_contents($path . '/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

