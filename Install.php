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
copy_dir(__DIR__ . "/install/resources", $path . '/resources');
copy_dir(__DIR__ . "/install/src", $path . '/src');
copy(__DIR__ . '/install/start_server.php', $path . '/start_server.php');
updateComposer();


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
            }
        }
    }
    closedir($dir);
    print_r("已创建$dst 目录\n");
    return true;
}

function updateComposer()
{
    global $path;
    if (!$composer = file_get_contents($path . '/composer.json')) {
        exit('composer.json not found');
    }

    $composer = json_decode($composer, true);
    $composer['autoload']['psr-4']['App\\'] = 'src/';
    file_put_contents($path . '/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

