<?php

namespace Encore\Admin\Middleware;

use Closure;
use Encore\Admin\Facades\Admin;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \config(['auth.defaults.guard' => 'admin']);

        $redirectTo = admin_base_path($this->resolveRedirectTo());

        if (Admin::guard()->guest() && !$this->shouldPassThrough($request)) {
            return redirect()->to($redirectTo);
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        // 下面的路由不验证登陆
        $excepts = config('admin.auth.excepts', []);
        $excepts[] = $this->resolveRedirectTo();

        array_delete($excepts, [
            '_handle_action_',
            '_handle_form_',
            '_handle_selectable_',
            '_handle_renderable_',
        ]);

        return collect($excepts)
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }

    protected function resolveRedirectTo(): string
    {
        $loginMethod = config('admin.auth.login_method', 'password');
        $defaultLoginPath = $loginMethod === 'openid' ? 'auth/openid/login' : 'auth/login';
        $redirectTo = config('admin.auth.redirect_to');

        if (empty($redirectTo)) {
            return $defaultLoginPath;
        }

        if ($loginMethod === 'openid' && $redirectTo === 'auth/login') {
            return $defaultLoginPath;
        }

        return $redirectTo;
    }
}
