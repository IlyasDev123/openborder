<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            Admin Panel
        </span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            {{-- <div class="image">
				<img src="{{ user_img_storage(Auth::guard('admin')->user()->profile_image) }}" class="img-circle elevation-2" alt="User Image">
			</div> --}}
            <div class="info">
                <a href="" class="d-block">
                    {{ Auth::guard('admin')->user()->name }}
                </a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}"
                        class="nav-link {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('user.index') }}"
                        class="nav-link {{ Request::segment(2) == 'users' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ url('/package/index') }}"
                        class="nav-link {{ Request::segment(1) == 'package' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Flat Fee Categories
                        </p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('package.plan.index') }}"
                        class="nav-link {{ Request::segment(1) == 'plan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Flat Fees
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ url('/consultation') }}"
                        class="nav-link {{ Request::segment(1) == 'consultation' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Consultations
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ url('/subscriber-list') }}"
                        class="nav-link {{ Request::segment(1) == 'subscriber-list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Flat Fee Payments
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
