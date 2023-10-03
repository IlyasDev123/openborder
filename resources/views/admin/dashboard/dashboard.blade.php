@extends('layouts.admin.admin')

@section('title', 'Dashboard')

@section('content')
	<div class="container-fluid">

		
        {{-- @include('layouts.admin.messages')
        {{-- @include('admin.dashboard._users_total_counts') --}}
        @include('admin.dashboard.total_consultant')
        {{-- @include('admin.dashboard._google_analytics_record') --}}
        {{-- @include('admin.table_partial_views._google_analytics_data') --}}
    </div>
@endsection

@section('scripts')
{{-- <script>
	$(document).ready(function() {
		// alert();
		$('#google_analytics').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
            "pagination":true,
		});

	});
</script> --}}
@endsection