<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{route('intranet')}}" class="app-brand-link">
           <span class="app-brand-logo demo " style="width: 24%;">
                <img src="{{asset('assets/img/icons/logo.png')}}" style="width: 100%;" alt="">
           </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">EdLarva10</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    @livewire('intranet.sidebar')

</aside>
