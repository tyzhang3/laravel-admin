<?php

namespace Encore\Admin;

use Encore\Admin\Layout\Content;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\AdminCommand::class,
        Console\MakeCommand::class,
        Console\ControllerCommand::class,
        Console\MenuCommand::class,
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\UninstallCommand::class,
        Console\ImportCommand::class,
        Console\CreateUserCommand::class,
        Console\ResetPasswordCommand::class,
        Console\ExtendCommand::class,
        Console\ExportSeedCommand::class,
        Console\MinifyCommand::class,
        Console\FormCommand::class,
        Console\PermissionCommand::class,
        Console\ActionCommand::class,
        Console\GenerateMenuCommand::class,
        Console\ConfigCommand::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth'       => Middleware\Authenticate::class,
        'admin.guest'      => Middleware\RedirectIfAuthenticated::class,
        'admin.pjax'       => Middleware\Pjax::class,
        'admin.log'        => Middleware\LogOperation::class,
        'admin.permission' => Middleware\Permission::class,
        'admin.bootstrap'  => Middleware\Bootstrap::class,
        'admin.session'    => Middleware\Session::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.pjax',
            'admin.log',
            'admin.bootstrap',
            'admin.permission',
            //            'admin.session',
        ],
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin');

        $this->ensureHttps();

        // 注册Laravel Admin内置路由
        $this->registerAdminRoutes();

        // 加载自定义路由文件（如果存在）
        if (file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }

        $this->registerPublishing();

        Blade::directive('box', function ($title) {
            return "<?php \$box = new \Encore\Admin\Widgets\Box({$title}, '";
        });

        Blade::directive('endbox', function ($expression) {
            return "'); echo \$box->render(); ?>";
        });
    }

    /**
     * Force to set https scheme if https enabled.
     *
     * @return void
     */
    protected function ensureHttps()
    {
        // 获取配置
        $httpsEnabled = config('admin.https') || config('admin.secure');
        if (!$httpsEnabled) {
            return;
        }
        
        // 规范化前缀处理
        $adminPrefix = trim(config('admin.route.prefix', 'admin'), '/');
        if (empty($adminPrefix)) {
            return;
        }
        
        // 安全的URL路径检查
        $request = $this->app['request'];
        $requestUri = $request->getRequestUri();
        $requestPath = parse_url($requestUri, PHP_URL_PATH) ?: $requestUri;
        
        // 严格的路径匹配
        $normalizedPath = trim($requestPath, '/');
        $isAdminPath = $normalizedPath === $adminPrefix || 
                       strpos($normalizedPath, $adminPrefix . '/') === 0;
        
        if ($isAdminPath) {
            // 确保HTTPS强制生效
            if (!$request->isSecure()) {
                url()->forceScheme('https');
                $request->server->set('HTTPS', 'on');
                $request->server->set('SERVER_PORT', 443);
            }
        }
    }

    /**
     * Register Laravel Admin routes.
     *
     * @return void
     */
    protected function registerAdminRoutes()
    {
        // 调用Admin类的routes方法注册内置路由
        $this->app->booted(function () {
            \Encore\Admin\Admin::routes();
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-admin-config');
            // Laravel 12统一使用lang_path()辅助函数
            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path()
            ], 'laravel-admin-lang');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-admin-migrations');
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/laravel-admin')], 'laravel-admin-assets');
        }
    }


    /**
     * Extends laravel router.
     */
    protected function macroRouter()
    {
        Router::macro('content', function ($uri, $content, $options = []) {
            return $this->match(['GET', 'HEAD'], $uri, function (Content $layout) use ($content, $options) {
                return $layout
                    ->title(Arr::get($options, 'title', ' '))
                    ->description(Arr::get($options, 'desc', ' '))
                    ->body($content);
            });
        });

        Router::macro('component', function ($uri, $component, $data = [], $options = []) {
            return $this->match(['GET', 'HEAD'], $uri, function (Content $layout) use ($component, $data, $options) {
                return $layout
                    ->title(Arr::get($options, 'title', ' '))
                    ->description(Arr::get($options, 'desc', ' '))
                    ->component($component, $data);
            });
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadAdminAuthConfig();

        $this->registerRouteMiddleware();

        $this->commands($this->commands);

        $this->macroRouter();
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // 首先注册路由中间件别名
        foreach ($this->routeMiddleware as $key => $middleware) {
            if (!class_exists($middleware)) {
                throw new \InvalidArgumentException("Middleware class {$middleware} does not exist");
            }
            Route::aliasMiddleware($key, $middleware);
        }
        
        // 然后注册中间件组
        foreach ($this->middlewareGroups as $key => $middlewares) {
            $validatedMiddlewares = [];
            foreach ($middlewares as $middleware) {
                if (is_string($middleware) && !class_exists($middleware) && !isset($this->routeMiddleware[$middleware])) {
                    throw new \InvalidArgumentException("Invalid middleware: {$middleware}");
                }
                $validatedMiddlewares[] = $middleware;
            }
            Route::middlewareGroup($key, $validatedMiddlewares);
        }
    }
}
