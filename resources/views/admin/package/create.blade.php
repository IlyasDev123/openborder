@extends('layouts.admin.admin')
@section('title', 'Create Package')
@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="m-2">
            <button class="m-1 btn btn-primary" type="click" id="package-btn-click">Español</button>
        </div>
        <div class="card-body">
            <form action="{{ route("admin.package.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('layouts.admin.messages')
                <div class="row">
                    <div class="col-10">
                        <div class="form-group">
                            <label for="name">Package Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name' )}}" required>
                        </div>
                        <div class="form-group" id="package-name" style="display: none;">
                            <label for="name">Nombre del paquete</label>
                            <input type="text" id="name" name="package_name_es" class="form-control" value="{{ old('name' )}}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="summernote"></textarea>
                            {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                        </div>
                        <div class="form-group" id="package-description" style="display: none;">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" name="package_description_es" id="summernote_es"></textarea>
                            {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                        </div>
                        <div class="form-group">
                            <label for="password">Image</label>
                            <input type="file" id="file" name="image" class="form-control" value="{{ old('image' )}}">
                        <small>Image dimensions not greater then 300 by 300</small>
                        </div>
                        <div>
                            <input class="btn btn-danger" type="submit" value="Save">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $('#summernote').summernote({
            height: 250
        });
        $('#summernote_es').summernote({
            height: 250
        });

        $("#package-btn-click").click(function() {
            $("#package-name").toggle();
            $("#package-description").toggle();
        });
    </script>
@endsection
