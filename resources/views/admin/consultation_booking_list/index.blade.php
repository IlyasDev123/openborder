@extends('layouts.admin.admin')

@section('title', 'Consultation List')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-5">
		</div>
		<div class="col-2">
			{{-- <a class="btn btn-app" href="{{ route('admin.create.user') }}">
				<i class="fas fa-plus"></i> Create Doctor
			</a> --}}
		</div>
		<div class="col-5">
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			@include('layouts.admin.messages')
			<div class="card">
				<div class="card-body">
					@include('admin.table_partial_views.consultation-list')
				</div>
			</div>
		</div>
	</div>
</div>
@if (Session::has('success'))
 <?php 
	 $notification = Session::get('notification');
 ?>
@endif
@endsection

@section('scripts')
	@include('admin.datatable.data-table-script')
@endsection
