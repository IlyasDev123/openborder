@extends('layouts.admin.admin_login')

@section('content')
 	<div class="login-box">
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">
					Admin Login
				</p>
				<?php if (Session::has('error')) { ?>
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                        <?php echo Session::get('error') ?>
                    </div>
                <?php } ?>

                <?php if (Session::has('success')) { ?>
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                        <?php echo Session::get('success') ?>
                    </div>
                <?php } ?>

                <?php if ($errors->any()) { ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors->all() as $error) { ?>
                            <li><?= $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
				<form id="quickForm" action="{{ route('admin.login') }}" method="post">
					@csrf
					<div class="input-group mb-3">
						<input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
						@error('email')
							<span class="invalid-feedback" role="alert">
								<strong>
									{{ $message }}
								</strong>
							</span>
						@enderror
					</div>
					<div class="input-group mb-3">
						<input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
						@error('password')
							<span class="invalid-feedback" role="alert">
								<strong>
									{{ $message }}
								</strong>
							</span>
						@enderror
					</div>
					<div class="row">
						<div class="col-8">
							{{-- <div class="icheck-primary">
								<input type="checkbox" id="remember">
								<label for="remember">
									Remember Me
								</label>
							</div> --}}
						</div>
						<div class="col-4">
							<button type="submit" class="btn btn-primary btn-block">Sign In</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@section('scripts')
	<script>
		$(document).ready(function() {
			$('#quickForm').validate({
				rules: {
					email: {
						required: true,
						email: true,
					},
					password: {
						required: true
					}
				},
				messages: {
					email: {
						required: "Please enter a email address",
						email: "Please enter a vaild email address"
					},
					password: {
						required: "Please provide a password"
					}
			},
			errorElement: 'span',
			errorPlacement: function (error, element) {
				error.addClass('invalid-feedback');
				element.closest('.input-group').append(error);
			},
			highlight: function (element, errorClass, validClass) {
				$(element).addClass('is-invalid');
			},
			unhighlight: function (element, errorClass, validClass) {
				$(element).removeClass('is-invalid');
			}
			});
		});
	</script>
@endsection
@endsection
