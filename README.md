#  EasySwooleDistributed

**简单，易用，高性能，高扩展性，拥有强大的插件管理和丰富的插件系统，高颜值的PHP协程框架，简称ESD。**

交流群：994811283

![](./screenshots/start.jpg)
## ✨ 文档地址
https://www.kancloud.cn/tmtbe/goswoole/1067764
## ✨ 日志系统
![](./screenshots/log.jpg)
类似SpringBoot的日志系统，更加清楚的日志展现。

## ⚡️ 插件系统
![](./screenshots/run.jpg)
丰富插件库，目前收录了接近30个插件，通过加载组装不同的插件提供更强大的功能。
```php
$this->addPlug(new EasyRoutePlugin());
$this->addPlug(new ScheduledPlugin());
$this->addPlug(new RedisPlugin());
$this->addPlug(new MysqlPlugin());
$this->addPlug(new AutoreloadPlugin());
$this->addPlug(new AopPlugin());
```
加载插件只需要一行代码

## ⚡️ DI与注解
支持注解，框架提供大量可使用的注解，比如注解路由，注解事务，注解缓存，注解验证等。
![](./screenshots/rest.jpg)
```php
/**
 * @RestController("user")
 * Class CUser
 * @package ESD\Examples\Controller
 */
class CUser extends Base
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    /**
     * @GetMapping("login")
     * @return string
     */
    public function login()
    {
    }
}
```
注解不是强制使用的，完全可以不使用注解。框架均提供了常规使用方式。
## ⚡️ AOP
完整支持面向切片编程。
```php
$this->addAspect(new MyAspect);
```
![](./screenshots/aspect.jpg)
```php
/**
 * @param MethodInvocation $invocation Invocation
 *
 * @Around("@execution(ESD\Plugins\Mysql\Annotation\Transactional)")
 * @return mixed
 * @throws \Throwable
 */
public function aroundTransactional(MethodInvocation $invocation)
```

