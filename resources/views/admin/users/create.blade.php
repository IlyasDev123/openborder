@extends('layouts.admin.admin')
@section('title', 'Create User')
@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-body">
            <form action="{{ route("admin.package") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('layouts.admin.messages')
                <div class="row">
                    <div class="col-10">
                        <div class="form-group">
                            <label for="name">Package Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name' )}}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Package Description</label>
                            <textarea name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="password">Password*</label>
                            <input type="file" id="file" name="image" class="form-control" value="{{ old('image' )}}" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="zip_codes">
                                Assign Zip Code*
                            </label>
                            <select name="zip_codes[]" id="zip_codes" class="form-control select2" multiple="multiple" required>
                                @foreach($zip_codes as $id => $zipcode)
                                    <option value="{{ $id }}" {{ (in_array($id, old('zip_codes', []))) ? 'selected' : '' }}>{{ $zipcode }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success btn-xs chosen-toggle select">Select all</button>
                            <button type="button" class="btn btn-danger btn-xs chosen-toggle deselect">Deselect all</button>
                        </div> --}}
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
