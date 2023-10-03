@extends('layouts.admin.admin')
@section('title', 'Create Packages')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2 mt-3">
                <a class="btn btn-app" href="{{ route('package.plan.create') }}">
                    <i class="fa fa-edit"></i> Add Flat Fee
                </a>
            </div>
            <div class="col-5"></div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('layouts.admin.messages')

                <div class="card">
                    <div class="card-body">
                        <table id="flat_fee_list" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="no-sort">
                                        ID
                                    </th>
                                    <th>
                                        Category
                                    </th>
                                    <th>
                                        Flat Fee Name
                                    </th>
                                    <th>
                                        Package Plan Type
                                    </th>
                                    <th>
                                        Duration
                                    </th>
                                    <th>
                                        Recuring Periods
                                    </th>

                                    <th>
                                        Amount
                                    </th>
                                    <th>
                                        Description
                                    </th>
                                    <th>
                                        status
                                    </th>

                                    {{-- <th>
                                        Image : <br>
                                    </th> --}}
                                    {{-- <th>
                                        Action :<br>
                                    </th> --}}
                                </tr>
                            </thead>

                            <tbody id="tablecontents">
                                @foreach ($packages as $package)
                                    <tr class="row1" data-id="{{ $package->id }}">
                                        <td class="dt-control">
                                            <a data-target="#flat-fee-detail-{{ $package->id }}" data-toggle="modal"
                                                href="#"><span class="fa fa-plus"></span></a>
                                            @include('admin.package_price_plan.show')
                                        </td>

                                        <td>
                                            {{ $package->package->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $package->plan_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $package->plan_type ?? '' }}
                                        </td>
                                        <td>
                                            {{ $package->duration ?? '' }}
                                        </td>
                                        <td>
                                            {{ $package->recurring_period ?? '' }}
                                        </td>
                                        <td>{{ $package->amount ?? '' }}</td>

                                        <td>
                                            @php
                                                $description = strip_tags($package->description);
                                            @endphp
                                            {{ \Illuminate\Support\Str::limit(html_entity_decode($description), 100, '...') }}
                                        </td>

                                        <td>
                                            @if ($package->status != 1)
                                                <form action="{{ route('package.plan.active') }}" id=""
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $package->id }}"
                                                        id="id">
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm">Activate</button>
                                                </form>
                                                <hr>
                                                <a href="{{ route('package.plan.delete') }}" data-id="{{ $package->id }}"
                                                    class="delete_user" title="Delete User" data-toggle="modal"
                                                    data-target="#delete_user_modal">
                                                    <i class="fas fa-trash text-red m-2"></i>
                                                </a>
                                            @else
                                                <form action="{{ route('package.plan.deactivate') }}" id=""
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $package->id }}"
                                                        id="id">
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        Deactivate</button>
                                                </form>
                                            @endif


                                        </td>

                                        {{-- <td> --}}
                                        {{--   <img class="m-2" src="{{ asset($package->image) }}" alt=""
                                                width="100" height="100">
                                        </td> --}}
                                        {{-- <td> --}}
                                        {{-- <a href="{{ route('admin.manage.show.package', [$user->id]) }}" title="View User">
                                                    <i class="fas fa-eye"></i>
                                                </a> --}}
                                        {{-- <a href="{{ route('package.plan.edit', $package->id) }}"
                                                title="update pricing plan">
                                                <i class="fas fa-edit m-3"></i>
                                            </a> --}}
                                        {{-- <a href="{{ route('package.plan.delete') }}" data-id="{{ $package->id }}"
                                                class="delete_user" title="Delete User" data-toggle="modal"
                                                data-target="#delete_user_modal">
                                                <i class="fas fa-trash m-3"></i>
                                           </a> --}}
                                        {{-- </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_user_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Delete Flat Fee Price
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure? You want to delete this Flat fee?
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="" id="delete_package_form" method="post">
                        @csrf
                        <input type="hidden" name="package_id" value="" id="package_id">
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).on('click', '.delete_user', function() {

            var href = $(this).attr('href');
            var id = $(this).data('id');
            $("#delete_package_form").attr('action', href);
            $("#package_id").val(id);

            return false;
        });
        $(".button-spn").click(function() {
            $(".spanish-tab").show();
            $(".english-tab").hide();
        });
        $(".button-english").click(function() {
            $(".english-tab").show();
            $(".spanish-tab").hide();
        });
    </script>
    @include('admin.datatable.flat-fee-datatable')
@endsection
