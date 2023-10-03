<div id="user-detail-{{$consultation->id}}" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
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
                                    Time of Agreement to Terms and Conditions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td>
                                        {{ $loop->index + 1 }}
                                    </td>
                                    <td>
                                        {{$consultation->user->first_name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $consultation->user->last_name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $consultation->user->email ?? '' }}
                                    </td>
                                    <td>
                                        {{ $consultation->user->phone_no ?? '' }}
                                    </td>
                                    <td>
                                        {{ $consultation->user->user_address->street_address ?? '' }} {{ ' ' }}
                                        {{ $consultation->user->user_address->country ?? '' }} {{ ' ' }}<br>
                                        {{ $consultation->user->user_address->state ?? '' }} {{ ' ' }}
                                        {{ $consultation->user->user_address->zip_code ?? '' }}

                                    </td>
                                    {{-- <td>
                                        @if (isset($consultation->user->questionnaireStatesSummary->current_summary) && $consultation->user->questionnaireStatesSummary->current_summary != null)
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#myModal_{{ $loop->index + 1 }}">Evaluation</button>
                                            @include('admin.table_partial_views.user-questionaire')
                                        @else
                                            <h6>Null</h6>
                                        @endif
                                    </td> --}}

                                    <td>{{$consultation->user->created_at??""}}</td>

                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
