<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-light-lightblue">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ asset('AdminLTE/dist/img/logo-head.png')}}" alt="BPO Tech" class="brand-image"
             style="opacity: .8">
        <span class="brand-text font-weight-bold text-lightblue"
              style="font-family: 'Nerko One', cursive; opacity: 0">BPO Tech</span>
    </a>

    <!-- Sidebar -->
    <div
            class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition">
        <!-- Sidebar user (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-5">
            <ul class="nav nav-pills nav-sidebar flex-column nav-legacy" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                        with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('file-manager.index')}}"
                        class="nav-link font-weight-light {{Request::is('file-manager*') ? 'active': ''}}">
                        <i class="nav-icon fas fa-folder-open text-primary"></i>
                        <p>
                            {{__('Flie Manager')}}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('users.index')}}"
                        class="nav-link font-weight-light {{Request::is('users*') ? 'active': ''}}">
                        <i class="nav-icon fas fa-user text-success"></i>
                        <p>
                            {{__('User')}}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>