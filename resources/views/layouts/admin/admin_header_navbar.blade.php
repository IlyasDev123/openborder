<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="javascript:void(0)" role="button">
				<i class="fas fa-bars"></i>
			</a>
		</li>
	</ul>
	<ul class="navbar-nav ml-auto">
		<li class="nav-item dropdown user-menu">
			<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
				{{-- <img src="{{ user_img_storage(Auth::guard('admin')->user()->profile_image) }}" class="user-image img-circle elevation-2" alt="User Image"> --}}
				<span class="d-none d-md-inline">
					{{ Auth::guard('admin')->user()->name }}
				</span>
			</a>
			<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<li class="user-header bg-primary">
					{{-- <img src="{{ user_img_storage(Auth::guard('admin')->user()->profile_image) }}" class="img-circle elevation-2" alt="User Image"> --}}
					<p>
						{{ Auth::guard('admin')->user()->name }}
					</p>
				</li>
				<li class="user-footer">
					<a href="{{ route('update.profile')}}" class="btn btn-default btn-flat">
						My Profile
					</a>
					<a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat float-right">
						Sign out
					</a>
				</li>
			</ul>
		</li>
	</ul>
</nav>
