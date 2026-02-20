<?php

namespace Encore\Admin\Console;

use Encore\Admin\Auth\Database\AdminDefaults;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initDatabase();

        $this->initAdminDirectory();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        $userModel = config('admin.database.users_model');
        $roleModel = config('admin.database.roles_model');
        $permissionModel = config('admin.database.permissions_model');
        $menuModel = config('admin.database.menu_model');

        if ($userModel::count() == 0) {
            $this->call('db:seed', ['--class' => \Encore\Admin\Auth\Database\AdminTablesSeeder::class]);

            return;
        }

        $needRepair = $roleModel::count() == 0
            || $permissionModel::count() == 0
            || $menuModel::count() == 0;

        if ($needRepair) {
            $this->repairAdminData($userModel, $roleModel, $permissionModel, $menuModel);
        }
    }

    /**
     * Repair essential admin data without truncating existing records.
     *
     * @param string $userModel
     * @param string $roleModel
     * @param string $permissionModel
     * @param string $menuModel
     *
     * @return void
     */
    protected function repairAdminData($userModel, $roleModel, $permissionModel, $menuModel)
    {
        $administrator = AdminDefaults::administrator();
        $adminUser = $userModel::firstOrCreate(
            ['username' => $administrator['username']],
            [
                'password' => Hash::make($administrator['password']),
                'name'     => $administrator['name'],
            ]
        );

        $role = AdminDefaults::role();
        $administratorRole = $roleModel::firstOrCreate(
            ['slug' => $role['slug']],
            ['name' => $role['name']]
        );

        $adminUser->roles()->syncWithoutDetaching([$administratorRole->getKey()]);

        foreach (AdminDefaults::permissions() as $permission) {
            $permissionModel::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $permissionIds = $permissionModel::query()->pluck('id')->all();
        $administratorRole->permissions()->syncWithoutDetaching($permissionIds);

        $adminMenu = null;
        foreach (AdminDefaults::rootMenus() as $menu) {
            $created = $menuModel::firstOrCreate(
                [
                    'parent_id' => 0,
                    'title'     => $menu['title'],
                    'uri'       => $menu['uri'],
                ],
                [
                    'order' => $menu['order'],
                    'icon'  => $menu['icon'],
                ]
            );

            if ($menu['uri'] === '' && $menu['title'] === 'Admin') {
                $adminMenu = $created;
            }
        }

        if ($adminMenu) {
            foreach (AdminDefaults::adminChildMenus() as $menu) {
                $menuModel::firstOrCreate(
                    [
                        'parent_id' => $adminMenu->getKey(),
                        'title'     => $menu['title'],
                        'uri'       => $menu['uri'],
                    ],
                    [
                        'order' => $menu['order'],
                        'icon'  => $menu['icon'],
                    ]
                );
            }

            $adminMenu->roles()->syncWithoutDetaching([$administratorRole->getKey()]);
        }
    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {
        $this->directory = config('admin.directory');

        if (is_dir($this->directory)) {
            $this->line("<error>{$this->directory} directory already exists !</error> ");

            return;
        }

        $this->makeDir('/');
        $this->line('<info>Admin directory was created:</info> '.str_replace(base_path(), '', $this->directory));

        $this->makeDir('Controllers');

        $this->createHomeController();
        $this->createAuthController();
        $this->createExampleController();

        $this->createBootstrapFile();
        $this->createRoutesFile();
    }

    /**
     * Create HomeController.
     *
     * @return void
     */
    public function createHomeController()
    {
        $homeController = $this->directory.'/Controllers/HomeController.php';
        $contents = $this->getStub('HomeController');

        $this->laravel['files']->put(
            $homeController,
            str_replace('DummyNamespace', config('admin.route.namespace'), $contents)
        );
        $this->line('<info>HomeController file was created:</info> '.str_replace(base_path(), '', $homeController));
    }

    /**
     * Create AuthController.
     *
     * @return void
     */
    public function createAuthController()
    {
        $authController = $this->directory.'/Controllers/AuthController.php';
        $contents = $this->getStub('AuthController');

        $this->laravel['files']->put(
            $authController,
            str_replace('DummyNamespace', config('admin.route.namespace'), $contents)
        );
        $this->line('<info>AuthController file was created:</info> '.str_replace(base_path(), '', $authController));
    }

    /**
     * Create HomeController.
     *
     * @return void
     */
    public function createExampleController()
    {
        $exampleController = $this->directory.'/Controllers/ExampleController.php';
        $contents = $this->getStub('ExampleController');

        $this->laravel['files']->put(
            $exampleController,
            str_replace('DummyNamespace', config('admin.route.namespace'), $contents)
        );
        $this->line('<info>ExampleController file was created:</info> '.str_replace(base_path(), '', $exampleController));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createBootstrapFile()
    {
        $file = $this->directory.'/bootstrap.php';

        $contents = $this->getStub('bootstrap');
        $this->laravel['files']->put($file, $contents);
        $this->line('<info>Bootstrap file was created:</info> '.str_replace(base_path(), '', $file));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file = $this->directory.'/routes.php';

        $contents = $this->getStub('routes');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Routes file was created:</info> '.str_replace(base_path(), '', $file));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }
}
