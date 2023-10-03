@extends('layouts.admin.admin')
@section('title', 'Create Package')
@section('content')
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="m-2">
                <button class="m-1 btn btn-primary" type="click" id="btn-click">Español</button>
            </div>
            <div class="card-body">
                <form action="{{ route('package.plan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('layouts.admin.messages')
                    <div class="row">
                        <div class="col-10">
                            <div class="form-group">
                                <label for="name">Package Name</label>
                                <select name="package_id" id="type" class="form-control">
                                    <option value="" disabled>Select User Type</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id ?? '' }}">{{ $package->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="plan_name">Package Plan Name</label>
                                <input type="text" id="name" name="plan_name" class="form-control"
                                    value="{{ old('plan_name') }}" required>
                            </div>

                            <div class="form-group" id="plan-name-es" style="display: none;">
                                <label for="plan_name"> Nombre del plan de paquete </label>
                                <input type="text" id="name_ed" name="plan_name_es" class="form-control"
                                    value="{{ old('plan_name_es') }}">
                            </div>
                            <div class="form-group">
                                <label for="plan_type">Package Plan Type </label>
                                <select name="plan_type" id="plan_type" class="form-control"
                                    onchange="addAutoValueOnsignalPayment(event)" required>
                                    <option value="" disabled>Select Package Plan Type</option>
                                    <option value="single">single</option>
                                    <option value="recurring">Recurring</option>
                                </select>
                            </div>

                            <div id="recurring_period_div" style="display:none">
                                <div class="form-group">
                                    <label for="recurring_period">Period</label>
                                    <select name="recurring_period" id="recurring_period" class="form-control"
                                        onchange="myFunction(event)" required>
                                        <option value="" disabled default>Select Period</option>
                                        <option value="day">day</option>
                                        <option value="month">month</option>
                                        <option value="year">year</option>
                                        <option id="every_month" value="every_month">every month</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Duration (Set 1000 when select period is Every month)</label>
                                    <input type="number" min="1" step="1" id="duration" name="duration"
                                        class="form-control" value="1" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="amount">Price(To add multiple price use comma separate like: 100,
                                    200,300)</label>
                                <input type="text" id="name" name="amount" class="form-control"
                                    value="{{ old('plan_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="summernote"></textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>
                            <div class="form-group" id="plan-description-es" style="display: none;">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="description_es" id="summernote_es"></textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>
                            <div class="form-group">
                                <label for="password">Image</label>
                                <input type="file" id="file" name="image" class="form-control"
                                    value="{{ old('image') }}" required>
                                <small>Image dimensions not greater then 300 by 300</small>
                            </div>
                            <div class="form-group form-check">
                                <input class="form-check-input" name="is_quantity_enable" type="checkbox" value="1" id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    Have Quantity option of this package.
                                </label>
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
        function myFunction(e) {
            var value = e.target.value
            if (value == 'every_month') {
                return document.getElementById("duration").value = 2000;
            }
            document.getElementById("duration").value = '';
        }

        function addAutoValueOnsignalPayment(e) {
            var value = e.target.value
            if (value == 'recurring') {
                console.log("this is test");
                return $("#recurring_period_div").show();
            }
            document.getElementById("duration").value = 1;
            document.getElementById("recurring_period").value = 'day';
            return $("#recurring_period_div").hide();
        }
        $("#btn-click").click(function() {
            console.log("show");
            $("#plan-name-es").toggle();
            $("#plan-description-es").toggle();
        });
    </script>
@endsection
