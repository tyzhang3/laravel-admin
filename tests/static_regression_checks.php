<?php

$root = dirname(__DIR__);

$failures = [];

$assert = function ($condition, $message) use (&$failures) {
    if (!$condition) {
        $failures[] = $message;
    }
};

$read = function ($path) use ($root) {
    return file_get_contents($root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path));
};

$admin = $read('src/Admin.php');
$provider = $read('src/AdminServiceProvider.php');
$composer = json_decode($read('composer.json'), true);

$assert(
    !preg_match("/'as'\\s*=>\\s*\\\$config\\['as'\\]/", $admin),
    'Builtin admin routes must not add an outer admin. name prefix.'
);

$assert(
    !str_contains($admin, 'empty(trim($prefix))'),
    'Admin route prefix must allow an empty string for root-mounted admin routes.'
);

$assert(
    !str_contains($provider, 'registerAdminRoutes') && !str_contains($provider, 'Admin::routes()'),
    'AdminServiceProvider must not auto-register builtin routes because installed routes.php already calls Admin::routes().'
);

$providerLines = preg_split('/\R/', $provider);
$hasExecutableRoutesGuard = false;
foreach ($providerLines as $line) {
    if (preg_match("/^\\s*if \\(file_exists\\(\\\$routes = admin_path\\('routes\\.php'\\)\\)\\) \\{/", $line)) {
        $hasExecutableRoutesGuard = true;
        break;
    }
}
$assert($hasExecutableRoutesGuard, 'AdminServiceProvider must keep the admin routes.php file_exists guard executable.');
$assert(!preg_match('/\/\/.*file_exists/', $provider), 'AdminServiceProvider route guard must not be hidden inside a line comment.');

$intervention = $composer['require-dev']['intervention/image'] ?? null;
$assert(
    is_string($intervention) && !str_contains($intervention, '^3'),
    'intervention/image constraint must match the existing Intervention Image 2.x API usage.'
);

$assert(
    !file_exists($root.'/database/migrations/2025_01_01_000002_fix_admin_permissions.php'),
    'Permission data repair must not be published as an automatic package migration.'
);

if ($failures) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }
    exit(1);
}

echo "Static regression checks passed.\n";
