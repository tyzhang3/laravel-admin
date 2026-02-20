@if(Admin::user()->visible(\Illuminate\Support\Arr::get($item, 'roles', [])) && Admin::user()->can(\Illuminate\Support\Arr::get($item, 'permission')))
    @php($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title']))))
    @php($title = Lang::has($titleTranslation) ? __($titleTranslation) : admin_trans($item['title']))
    @if(!isset($item['children']))
        <li class="nav-item">
            @if(url()->isValidUrl($item['uri']))
                <a href="{{ $item['uri'] }}" target="_blank" class="nav-link">
            @else
                <a href="{{ admin_url($item['uri']) }}" class="nav-link">
            @endif
                <i class="nav-icon {{ admin_icon_class($item['icon']) }}"></i>
                <p>{{ $title }}</p>
            </a>
        </li>
    @else
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon {{ admin_icon_class($item['icon']) }}"></i>
                <p>
                    {{ $title }}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @foreach($item['children'] as $item)
                    @include('admin::partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif
