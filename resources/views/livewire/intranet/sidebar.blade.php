<div>
    <ul class="menu-inner py-1">
        @php $menutab = $urlactual_sidebar @endphp
        <!-- Dashboard -->
        <li class="menu-item {{ ($menutab[0]== 'intranet')? 'active' : ''  }} ">
            <a href="{{route('intranet')}}" class="menu-link ">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Inicio</div>
            </a>
        </li>

        @foreach($list_menus as $list_men)
            @can($list_men->menu_controller)
                <li class="menu-item {{ ($menutab[0]==$list_men->menu_controller)? 'active open' :''  }}" wire:key="menu-{{ $list_men->id_menu }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon {{$list_men->menu_icons}}"></i>
                        <div data-i18n="Layouts">{{$list_men->menu_name}}</div>
                    </a>
                    @if(count($list_men->submenu) > 0)
                        <ul class="menu-sub {{ ($menutab[0]==$list_men->menu_controller)? 'active' :''  }} ">
                            @foreach($list_men->submenu as $sub)
                                @can($sub->submenu_function)
                                    <li class="menu-item {{ isset($menutab[1]) && $sub->submenu_function == $menutab[1] ? 'active' : '' }}" wire:key="submenu-{{ $sub->id_submenu }}">
                                        <a href="{{ url($list_men->menu_controller . '/' . $sub->submenu_function) }}" class="menu-link">
                                            <div data-i18n="Without menu">{{ $sub->submenu_name }}</div>
                                        </a>
                                    </li>
                                @endcan
                            @endforeach
                        </ul>
                    @endif

                </li>
            @endcan
        @endforeach
    </ul>
</div>
