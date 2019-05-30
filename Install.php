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
copy_dir(__DIR__ . "/examples/src", $path.'/src');
copy_dir(__DIR__ . "/examples/resources", $path.'/resources');

updateComposer();
createBase();
createIndex();
createStart();


exec("composer dumpautoload",$output);
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
    if(!$dir) {
        print_r("$src 权限问题或目录不合法，安装错误\n");
        return;
    }
    if (file_exists($dst) && $force == false) {
        print_r("$dst 目录已存在（跳过）\n");
        return;
    }
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_dir($src . '/' . $file, $dst . '/' . $file);
                continue;
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    print_r("已创建$dst 目录\n");
}


function createStart(){
    global $path;
    $cfg = <<<EOF
<?php
namespace app;
use ESD\Go\GoApplication;
class Application extends GoApplication
{
}
EOF;
    file_put_contents($path .'/src/Application.php', $cfg);

    $cfg = <<<EOF
<?php
require __DIR__ . '/vendor/autoload.php';
define("ROOT_DIR", __DIR__);
define("RES_DIR", __DIR__ . "/resources");

new app\Application();
EOF;
    file_put_contents($path .'/start_server.php', $cfg);
}

function updateComposer(){
    global $path;
    if(!$composer = file_get_contents($path .'/composer.json')){
        exit('composer.json not found');
    }

    $composer = json_decode( $composer,true);
    $composer['autoload']['psr-4']['app\\'] = 'src/';
    file_put_contents($path .'/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}



function createBase(){
    global $path;
    $tpl = <<<EOF
<?php
namespace app\Controller;
use ESD\Go\GoController;
use ESD\Plugins\AnnotationsScan\Annotation\Component;
use ESD\Plugins\EasyRoute\GetHttp;
use ESD\Plugins\Mysql\GetMysql;
use ESD\Plugins\Redis\GetRedis;
use ESD\Plugins\Session\GetSession;

/**
 * @Component()
 * Class Base
 * @package app\Controller
 */
class Base extends GoController
{
    use GetSession;
    use GetRedis;
    use GetHttp;
    use GetMysql;
}
EOF;
    file_put_contents($path.'/src/Controller/Base.php', $tpl);

}

function createIndex(){
    global $path;
$tpl = <<<EOF
<?php
namespace app\Controller;
use ESD\Plugins\EasyRoute\Annotation\GetMapping;
use ESD\Plugins\EasyRoute\Annotation\RestController;

/**
 * @RestController()
 * Class Index
 * @package ESD\Plugins\EasyRoute
 */
class Index extends Base
{

    /**
     * @GetMapping("/")
     * @return string
     */
    public function index()
    {
        return 'hello world';
    }
}
EOF;
file_put_contents($path.'/src/Controller/Index.php', $tpl);
}