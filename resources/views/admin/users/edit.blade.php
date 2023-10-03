@extends('layouts.admin.admin')
@section('title', 'Admin User')
@section('content')

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Edit User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Change Password</a>
                </li>

            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('layouts.admin.messages')
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="name">First Name*</label>
                                    <input type="text" id="name" name="first_name" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->first_name : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="name">Last Name*</label>
                                    <input type="text" id="name" name="last_name" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->last_name : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="name">Phone Number*</label>
                                    <input type="text" id="name" name="phone_no" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->phone_no : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="email">Email*</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ old('email', isset($user) ? $user->email : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="name">Streat Address*</label>
                                    <input type="text" id="name" name="street_address" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->user_address->street_address ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="name">Zip code*</label>
                                    <input type="text" id="name" name="zip_code" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->user_address->zip_code ?? '' : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="name">Country</label>
                                    <input type="text" id="name" name="country" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->user_address->country ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="name">State</label>
                                    <input type="text" id="name" name="state" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->user_address->state ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="name">City</label>
                                    <input type="text" id="name" name="city" class="form-control"
                                        value="{{ old('name', isset($user) ? $user->user_address->city ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div>
                            <input class="btn btn-danger" type="submit" value="Update">
                        </div>
                    </form>

                </div>
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    @include('admin.users.change-password')
                </div>

            </div>



        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // $('#roles').select2();
            $('#zip_codes').select2();
        });

        // Select all
        $('.chosen-toggle.select').click(function() {
            $('#zip_codes').select2('destroy').find('option').prop('selected', 'selected').end().select2();
        });

        // Unselect all
        $('.chosen-toggle.deselect').click(function() {
            $('#zip_codes').select2('destroy').find('option').prop('selected', false).end().select2();
        });

    </script>
@endsection
