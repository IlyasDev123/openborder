@extends('layouts.admin.admin')
@section('title', 'Create Packages')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2 mt-3">
                <a class="btn btn-app" href="{{ route('admin.package.create') }}">
                    <i class="fa fa-edit"></i> Add Category
                </a>
            </div>
            <div class="col-5"></div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('layouts.admin.messages')
                <div class="card">
                    <div class="card-body">
                        <table id="packages_list" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        ID
                                    </th>
                                    <th>
                                        Category
                                    </th>
                                    <th>
                                        Categoría
                                    </th>
                                    <th>
                                        Description
                                    </th>
                                    <th>
                                        Descripción
                                    </th>
                                    <th>
                                        Image
                                    </th>
                                    <th>
                                        Active/Deactive
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tablecontents">
                                @foreach ($packages as $package)
                                    <tr class="row1" data-id="{{ $package->id }}">
                                        <td>
                                            <i class="nav-icon fas fa-file-alt"></i>
                                            {{-- {{ $loop->index + 1 }} --}}
                                        </td>
                                        <td>
                                            {{ $package->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $package->package_name_es ?? '' }}
                                        </td>
                                        <td>
                                            @php
                                                $description = strip_tags($package->description);
                                            @endphp
                                            {!! \Illuminate\Support\Str::limit(html_entity_decode($description), 100, '...') !!}
                                            {{-- {!! Illuminate\Support\Str::limit(html_entity_decode($description),100) !!} --}}
                                        </td>
                                        <td>
                                            @php
                                                $description_es = strip_tags($package->package_description_es);
                                            @endphp
                                            {!! \Illuminate\Support\Str::limit(html_entity_decode($description_es), 100, '...') !!}
                                        </td>
                                        <td>
                                            <img src="{{ asset($package->image) }}" alt="" width="100"
                                                height="100">
                                        </td>
                                        <td>
                                            @if ($package->status != 'active')
                                                <form action="{{ route('admin.package.active') }}" id=""
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $package->id }}"
                                                        id="id">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn btn-primary">Activate</button>
                                                </form>
                                                {{-- <a class="btn btn-primary text-align-center "
                                                    href="{{ route('admin.package.edit', [$package->id]) }}"
                                                    title="View User">
                                                    Active
                                                </a> --}}
                                            @else
                                                <form action="{{ route('admin.package.deactivate') }}" id=""
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $package->id }}"
                                                        id="id">
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="btn btn-danger"> Deactivate</button>
                                                </form>
                                            @endif



                                        </td>
                                        <td>
                                            <a href="{{ route('admin.package.edit', [$package->id]) }}"
                                                title="Edit Package">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <hr>
                                            @if ($package->status != 'active')
                                            <a href="{{ route('admin.package.delete') }}" data-id="{{ $package->id }}"
                                                class="delete_category" title="Delete Flat Fee Category" data-toggle="modal"
                                                data-target="#delete_category_modal">
                                                <i class="fas fa-trash text-red"></i>
                                            </a>
                                            @endif
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

    <div class="modal fade" id="delete_category_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Delete Flat Fee Category
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure? You want to delete this Category?
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="" id="delete_category_form" method="post">
                        @csrf
                        <input type="hidden" name="id" value="" id="category_id">
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('admin.datatable.data-table-script-package')
    <script>
        $(document).on('click', '.delete_category', function() {

            var href = $(this).attr('href');
            var id = $(this).data('id');
            $("#delete_category_form").attr('action', href);
            $("#category_id").val(id);

            return false;
        });
    </script>

@endsection
