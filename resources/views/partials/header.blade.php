<nav class="{{ config('admin.ui.navbar_class', 'main-header navbar navbar-expand navbar-white navbar-light') }}">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        {!! Admin::getNavbar()->render('left') !!}
    </ul>

    <ul class="navbar-nav ml-auto">
        {!! Admin::getNavbar()->render() !!}

        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ Admin::user()->avatar }}" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline">{{ Admin::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-primary">
                    <img src="{{ Admin::user()->avatar }}" class="img-circle elevation-2" alt="User Image">
                    <p>
                        {{ Admin::user()->name }}
                        <small>Member since admin {{ Admin::user()->created_at }}</small>
                    </p>
                </li>
                <li class="user-footer">
                    <a href="{{ admin_url('auth/setting') }}" class="btn btn-default btn-flat">{{ trans('admin.setting') }}</a>
                    <a href="{{ admin_url('auth/logout') }}" class="btn btn-default btn-flat float-right">{{ trans('admin.logout') }}</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
