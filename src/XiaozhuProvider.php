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
        $this->app->singleton('app', function ($app) {
            return new Wechat\app($app['config']);
        });
        $this->app->singleton('corp', function ($app) {
            return new Wechat\corp($app['config']);
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
        return ['app','corp'];
    }
}
?>
