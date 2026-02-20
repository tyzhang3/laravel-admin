<aside class="{{ config('admin.ui.sidebar_class', 'main-sidebar sidebar-dark-primary elevation-4') }}">
    <a href="{{ admin_url('/') }}" class="{{ config('admin.ui.brand_class', 'brand-link') }}">
        <span class="brand-text font-weight-light">{!! config('admin.logo', config('admin.name')) !!}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Admin::user()->avatar }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Admin::user()->name }}</a>
            </div>
        </div>

        @if(config('admin.enable_menu_search'))
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input type="search" autocomplete="off" class="form-control form-control-sidebar autocomplete" placeholder="Search...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                    <ul class="dropdown-menu" role="menu" style="min-width: 210px;max-height: 300px;overflow: auto;">
                        @foreach(Admin::menuLinks() as $link)
                            <li>
                                <a href="{{ admin_url($link['uri']) }}" class="dropdown-item">
                                    <i class="{{ admin_icon_class($link['icon']) }}"></i>&nbsp;{{ admin_trans($link['title']) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column sidebar-menu" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">{{ trans('admin.menu') }}</li>
                @each('admin::partials.menu', Admin::menu(), 'item')
            </ul>
        </nav>
    </div>
</aside>
