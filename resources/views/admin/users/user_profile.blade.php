@extends('layouts.admin.admin')

@section('title', 'User Profile')

@section('content')
	<div class="container-fluid">
		@include('layouts.admin.messages')
		<div class="row">
			<div class="col-md-3">
				<!-- Profile Image -->
				<div class="card card-primary card-outline">
					<div class="card-body box-profile">
						<div class="text-center">
							<img class="profile-user-img img-fluid img-circle" src="{{ user_img_storage($user->profile_image) }}" alt="User profile picture">
						</div>
						<h3 class="profile-username text-center">
							{{ $user->name }}
						</h3>
						<p class="text-muted text-center">
							{{ $user->type }}
						</p>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="card">
					<div class="card-header p-2">
						<ul class="nav nav-pills">
							<li class="nav-item">
								<a class="nav-link active" href="#edit_profile" data-toggle="tab">
									Edit Profile
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#change_password" data-toggle="tab">
									Change Password
								</a>
							</li>
						</ul>
					</div><!-- /.card-header -->
					<div class="card-body">
						<div class="tab-content">
							<div class="tab-pane active" id="edit_profile">
								<form class="form-horizontal" id="edit_profile_form" action="{{ route('admin.save_profile') }}" method="post" enctype="multipart/form-data">
									@csrf
									<div class="form-group row">
										<label for="inputName" class="col-sm-2 col-form-label">
											Name
										</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="name" placeholder="Enter First Name" name="name" value="{{ $user->name }}">
										</div>
									</div>
									<div class="form-group row">
										<label for="email" class="col-sm-2 col-form-label">
											Email
										</label>
										<div class="col-sm-10">
											<input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="{{ $user->email }}">
										</div>
									</div>
									<div class="form-group row">
										<label for="" class="col-sm-2 col-form-label">
										</label>
										<div class="col-sm-10 custom-file">
                      						<input type="file" class="custom-file-input" id="customFile" name="upload">
                      						<label class="custom-file-label" for="customFile">Choose File</label>
                    					</div>
									</div>
									<div class="form-group row">
										<div class="offset-sm-2 col-sm-10">
											<button type="submit" class="btn btn-danger">
												Submit
											</button>
										</div>
									</div>
								</form>
							</div>
							<div class="tab-pane" id="change_password">
								<form class="form-horizontal" id="change_password_form" action="{{ route('admin.change_password') }}" method="post" enctype="multipart/form-data">
									@csrf
									<div class="form-group row">
										<label for="new_password" class="col-sm-3 col-form-label">
											New Password
										</label>
										<div class="col-sm-9">
											<input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" placeholder="Enter New Password" name="new_password">
											@error('new_password')
												<span class="invalid-feedback" role="alert">
													<strong>
														{{ $message }}
													</strong>
												</span>
											@enderror
										</div>
									</div>
									<div class="form-group row">
										<label for="confirm_password" class="col-sm-3 col-form-label">
											Confirm Password
										</label>
										<div class="col-sm-9">
											<input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password">
											@error('confirm_password')
												<span class="invalid-feedback" role="alert">
													<strong>
														{{ $message }}
													</strong>
												</span>
											@enderror
										</div>
									</div>
									<div class="form-group row">
										<div class="offset-sm-3 col-sm-9">
											<button type="submit" class="btn btn-danger">
												Submit
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection