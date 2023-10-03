@extends('layouts.admin.admin')
@section('title', 'Admin User')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            User Detail
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 20%">
                                     Name
                                    </th>
                                   
                                    <td>
                                        {{ $user->name }}
                                    </td>
                                </tr>
                             
                                <tr>
                                    <th style="width: 20%">
                                        Email
                                    </th>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                </tr>
                               
                                <tr>
                                    <th>
                                        Roles
                                    </th>
                                    <td>
                                        @foreach($user->roles()->pluck('name') as $role)
                                            <span class="label label-info label-many">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Permissions
                                    </th>
                                    <td>
                                        @foreach($user->getAllPermissions()->pluck('name') as $role)
                                            <span class="badge badge-success">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Image
                                    </th>
                                    <td>
                                        <img src="{{ user_img($user->profile_image) }}" class="btn" style="max-width: 150px;min-height: 100px;" alt="User Image">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
