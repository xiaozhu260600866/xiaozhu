<?php namespace Xiaozhu;
use Illuminate\Support\ServiceProvider;
class XiaozhuProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true; // 延迟加载服务
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*$this->loadViewsFrom(__DIR__ . '/views', 'Packagetest'); // 视图目录指定
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/packagetest'),  // 发布视图目录到resources 下
            __DIR__.'/config/packagetest.php' => config_path('packagetest.php'), // 发布配置文件到 laravel 的config 下
        ]);*/
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
         // 单例绑定服务
        require_once __DIR__."/function.php";
        $config =  require_once base_path('config/xiaozhu.php');
        $GLOBALS["config"] = $config;
        $this->app->singleton('app', function ($app) {

            return new Wechat\app( $GLOBALS["config"]);
        });
         $this->app->singleton('wechatOpen', function ($app) {

            return new Wechat\wechatOpen( $GLOBALS["config"]);
        });

        $this->app->singleton('corp', function ($app) {
            return new Wechat\corp( $GLOBALS["config"]);
        });
         $this->app->singleton('workOpen', function ($app) {
            return new Wechat\workOpen( $GLOBALS["config"]);
        });
        $this->app->singleton('redisAuth', function ($app) {
            return new Redis\RedisAuthClass( $GLOBALS["config"]);
        });
        $this->app->singleton('redisHistory', function ($app) {
            return new Redis\RedisHistoryClass( $GLOBALS["config"]);
        });
         $this->app->singleton('redisFormId', function ($app) {
            return new Redis\RedisFormIdClass( $GLOBALS["config"]);
        });
          $this->app->singleton('redisToken', function ($app) {
            return new Redis\RedisTokenClass( $GLOBALS["config"]);
        });
        $this->app->singleton('qiniu', function ($app) {
          
            return new Qiniu\qiniu( $GLOBALS["config"]);
        });
        $this->app->singleton('sms', function ($app) {
          
            return new Sms\SendTemplateSMS( $GLOBALS["config"]);
        });

          
         
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        // 因为延迟加载 所以要定义 provides 函数 具体参考laravel 文档
        return ['app','corp','redisAuth','redisHistory','redisFormId','redisToken','qiniu','sms','workOpen','wechatOpen'];
    }
}
?>
