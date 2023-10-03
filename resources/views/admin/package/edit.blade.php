@extends('layouts.admin.admin')
@section('title', 'Create Package Pricing')
@section('content')
    <div class="container-fluid">
        <div class="card card-primary">
            @if (empty($package->package_name_es))
            <div class="m-2">
                <button class="m-1 btn btn-primary" type="click" id="package-btn-click">Español</button>
            </div>
            @endif
            <div class="card-body">
                <form action="{{ route('admin.package.update', $package->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @include('layouts.admin.messages')
                    <div class="row">
                        <div class="col-10">

                            <div class="form-group">
                                <label for="plan_name">Package Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ $package->name ?? '' }}" required>
                            </div>
                            <div class="form-group" id="package-name" style="{{ $package->package_name_es ? '':'display: none'}}";>
                                <label for="name">Nombre del paquete</label>
                                <input type="text" id="name" name="package_name_es" class="form-control" value="{{$package->package_name_es??""}}">
                            </div>
                            {{-- <div class="form-group">
                            <label for="plan_type">Package Plan Type </label>
								<select name="plan_type" id="plan_type" class="form-control" required>
									<option value="" disabled >Select Package Plan Type</option>
									<option value="single" {{$packagePlan->plan_type == 'single' ? 'selected' : ''}}>single</option>
                                    <option value="recurring" {{$packagePlan->plan_type == 'recurring' ? 'selected' : ''}}>Recurring</option>
								</select>
                        </div>


                        <div class="form-group">
                            <label for="recurring_period">Period</label>
								<select name="recurring_period" id="recurring_period" class="form-control" required>
									<option value="" disabled>Select Period</option>
									<option value="day" {{$packagePlan->day == 'day' ? 'selected' : ''}}>day</option>
                                    <option value="month" {{$packagePlan->day == 'month' ? 'selected' : ''}}>month</option>
                                    <option value="year" {{$packagePlan->year == 'year' ? 'selected' : ''}}>year</option>
								</select>
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration</label>
                            <input type="number"  min="1" step="1" id="name" name="duration" class="form-control" value="{{$packagePlan->duration??"" }}" required>
                        </div> --}}

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="summernote">{{ $package->description }}</textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>
                            <div class="form-group" id="package-description"
                                 style="{{ $package->package_name_es ? '':'display: none'}}"; >
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="package_description_es" id="summernote_es">{{ $package->package_description_es??""}}</textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>

                            <div class="form-group">
                                <label for="password">Image</label>

                                <input type="file" id="file" name="image" class="form-control"
                                    value="{{ $package->image }}"
                                    onchange="document.getElementById('image').src = window.URL.createObjectURL(this.files[0])">
                                <div class="mt-2">
                                    <img id="image" src={{ asset($package->image ?? '') }} width="100"
                                        height="100">
                                </div>
                                <small>Image dimensions not greater then 300 by 300</small>
                            </div>

                            <div>
                                <input class="btn btn-danger" type="submit" value="Update">
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
