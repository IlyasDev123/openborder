@extends('layouts.admin.admin')
@section('title', 'Subscribtion List')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                {{-- @include('layouts.admin.messages') --}}
                <div class="card">
                    <div class="card-body">
                        <table id="subscription_list" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        ID
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Email
                                    </th>
                                    <th>
                                        Flat Fee
                                    </th>
                                    <th>
                                        Amount
                                    </th>
                                    <th>
                                        Subscription Type
                                    </th>
                                    <th>
                                        Start date
                                    </th>
                                    <th>
                                        End date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptionList as $consultation)
                                    <tr>
                                        <td>
                                            {{ $loop->index + 1 }}
                                        </td>
                                        <td>
                                            <a data-target="#user-detail-{{$consultation->id}}" data-toggle="modal" href="#user-detail" > {{ $consultation->user->first_name ?? '' }} {{ $user->user->last_name ?? '' }}</a>
                                            @include('admin.table_partial_views.user-detail')

                                        </td>

                                        <td>
                                            {{ $consultation->user->email ?? '' }}
                                        </td>
                                        <td>
                                            {{ $consultation->plan->plan_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $consultation->total_amount ?? '' }}
                                        </td>
                                        <td>
                                            @if ($consultation->subscription_type == 1)
                                                Monthly
                                            @elseif ($consultation->subscription_type == 2)
                                                Yearly
                                            @else
                                                Day
                                            @endif
                                        </td>
                                        <td>
                                             @php
                                                $startDate = date('F j, Y, g:i a', strtotime($consultation->stripe_start_at));
                                            @endphp
                                            {{ $startDate }}
                                        </td>
                                        <td>
                                            @php
                                                $endDate = date('F j, Y, g:i a', strtotime($consultation->stripe_ended_at));
                                            @endphp
                                            {{ $endDate }}
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




@endsection
@section('scripts')
    @include('admin.datatable.data-table-script')
    <script>
        $(document).ready(function() {
            $('#subscription_list').DataTable({
                "pageLength": 30,
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true
            });
        });
    </script>
@endsection
