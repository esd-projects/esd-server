<?php
namespace ESD\Examples\Controller;
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