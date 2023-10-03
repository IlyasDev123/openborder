@extends('layouts.admin.admin')
@section('title', 'Subscribtion List')
@section('content')
    <div class="container-fluid">
        @php
            $immigrationHistory = [];
            $factorsOptions = [];
            $inadmissibility = [];
            // $immigrationHistory = [];
        @endphp
        <div class="row">
            <div class="col-12">
                @include('layouts.admin.messages')
                <div class="card">
                    <div class="card-body">
                        <table id="users_list" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        ID
                                    </th>
                                    <th>
                                        First Name
                                    </th>
                                    <th>
                                        Last Name
                                    </th>
                                    <th>
                                        Email
                                    </th>
                                    <th>
                                        Phone Number
                                    </th>
                                    <th>
                                        Address
                                    </th>
                                    <th>
                                        Evaluation
                                    </th>
                                    <th>
                                        Time of Agreement to Terms and Conditions
                                    </th>
                                    <th>
                                        Petitioner First Name
                                    </th>
                                    <th>
                                       Petitioner Last Name
                                    </th>
                                    <th>
                                       Petitioner Email
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            {{ $loop->index + 1 }}
                                        </td>
                                        <td>
                                            {{ $user->first_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $user->last_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $user->email ?? '' }}
                                        </td>
                                        <td>
                                            {{ $user->phone_no ?? '' }}
                                        </td>
                                        <td>
                                            {{ $user->user_address->street_address ?? '' }} {{ ' ' }}
                                            {{ $user->user_address->country ?? '' }} {{ ' ' }}<br>
                                            {{ $user->user_address->state ?? '' }} {{ ' ' }}
                                            {{ $user->user_address->zip_code ?? '' }}

                                        </td>
                                        <td>
                                            @if (isset($user->questionnaireStatesSummary->current_summary) &&
                                                $user->questionnaireStatesSummary->current_summary != null)
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#myModal_{{ $loop->index + 1 }}">Evaluation</button>
                                                @include('admin.table_partial_views.user-questionaire')
                                            @else
                                                <h6>Null</h6>
                                            @endif
                                        </td>

                                        <td>{{ $user->created_at ?? '' }}</td>
                                        <td>{{ $user->petitionerDetail->first_name ?? '' }}</td>
                                        <td>{{ $user->petitionerDetail->last_name ?? '' }}</td>
                                        <td>{{ $user->petitionerDetail->email ?? '' }}</td>
                                        <td>
                                            <a href="{{ route('user.edit', $user->id) }}"
                                                title="User Edited">
                                                <i class="fas fa-edit m-3"></i>
                                            </a>
                                            <a href="{{ route('user.delete') }}" data-id="{{ $user->id }}"
                                                class="delete_user" title="Delete user" data-toggle="modal"
                                                data-target="#delete_user_form"
                                                data-dismiss="modal">
                                                <i class="fas fa-trash text-red m-3"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_user_form">
        <div id="success"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Delete User
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure? You want to delete this User. If you delete this user all associate data will be delete.
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="" id="delete_user_form" method="post">
                        @csrf
                        <input type="hidden" name="id" value="" id="user_id">
                        <button type="submit"  class="btn btn-primary btn-submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    @include('admin.datatable.data-table-script')

    <script type="text/javascript">

        $(document).on('click', '.delete_user', function(){

            $('#delete_user_form').attr('action', $(this).closest('a').attr('href'));
            $('#delete_user_form').find('input[name=id]').val($(this).attr('data-id'));
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(".btn-submit").click(function(e) {
            e.preventDefault();
            var id = $('#delete_user_form').find('input[name=id]').val();


            $.ajax({
                type: 'POST',
                url: "{{ route('user.delete') }}",
                data: {
                    id: id,
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
                success: function(data) {
                    $('#loading-image').hide();
                    $('#delete_user_form').modal('hide');
                    var message = data.message;
                    if(data.status == true){
                        alert(message);
                        // $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+message+'</div>');
                        // document.getElementById("cform").reset();
                    }
                    location.reload();
                },
                error:function (response){

                    $('#loading-image').hide();
                    }
            });

        });
    </script>
@endsection
