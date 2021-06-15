<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-light-lightblue">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link bg-lightblue">
        <img src="{{ asset('/VozDoremonlogo.png')}}" alt="Doremon" class="brand-image">
        <span class="brand-text text-lightblue">.</span>
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
                    <a href="{{route('home')}}"
                        class="nav-link font-weight-light {{Request::is('/') ? 'active': ''}}">
                        <i class="nav-icon fas fa-laptop-house text-primary"></i>
                        <p>
                            {{__('Home')}}
                        </p>
                    </a>
                </li>
                @if (auth()->user()->is_admin === 1)
                @if (auth()->id() === 1)
                <li class="nav-item">
                    <a href="{{route('users.index')}}"
                        class="nav-link font-weight-light {{Request::is('users*') ? 'active': ''}}">
                        <i class="nav-icon fas fa-user text-danger"></i>
                        <p>
                            {{__('Users')}}
                        </p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{route('books.index')}}"
                        class="nav-link font-weight-light {{Request::is('books*') ? 'active': ''}}">
                        <i class="nav-icon fas fa-torah text-warning"></i>
                        <p>
                            {{__('Books')}}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('volumes.index')}}"
                        class="nav-link font-weight-light {{Request::is('volumes*') ? 'active': ''}}">
                        <i class="nav-icon  fas fa-file-archive text-info"></i>
                        <p>
                            {{__('Volumes')}}
                        </p>
                    </a>
                </li>
                @if (auth()->id() === 1)
                <li class="nav-item">
                    <a href="{{route('file-manager.index')}}"
                        class="nav-link font-weight-light {{Request::is('file-manager*') ? 'active': ''}}">
                        <i class="nav-icon fas fa-folder-open text-success"></i>
                        <p>
                            {{__('File Manager')}}
                        </p>
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>