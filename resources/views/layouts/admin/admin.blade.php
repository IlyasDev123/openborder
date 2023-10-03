<!DOCTYPE html>
	<html>
		<!-- include head section section -->
	@include('layouts.admin.head')

	<body class="hold-transition sidebar-mini layout-fixed">
		<div class="wrapper">
			@include('layouts.admin.admin_header_navbar')

			@include('layouts.admin.admin_sidebar')

			<div class="content-wrapper">
				{{-- @include('layouts.admin.admin_breadcrumbs') --}}
				<section class="content">
					@yield('content')
				</section>
			</div>
			<aside class="control-sidebar control-sidebar-dark"></aside>
		</div>
		@include('layouts.admin.admin_js_scripts')
		@yield('scripts')
	</body>
</html>
