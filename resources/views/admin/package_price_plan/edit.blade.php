@extends('layouts.admin.admin')
@section('title', 'Create Package Pricing')
@section('content')

<style>
    /* #recurring_period_div {
        display: none
    } */
    </style>


    <div class="container-fluid">
        <div class="card card-primary">
            @if(empty($packagePlan->plan_name_es))
            <div class="m-2">
                <button class="m-1 btn btn-primary" type="click" id="btn-click">Español</button>
            </div>
            @endif
            <div class="card-body">
                <form action="{{ route('package.plan.update', $packagePlan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @include('layouts.admin.messages')
                    <div class="row">

                        <div class="col-10">
                            <div class="form-group">
                                <label for="plan_name">Slug Url</label>
                                <input type="text" id="slug" name="slug" class="form-control"
                                    value="{{ $packagePlan->slug ?? '' }}" required>
                            </div>
                            <div class="form-group">

                                <label for="name">Package Name</label>
                                <select name="package_id" id="type" class="form-control">
                                    <option value="" disabled>Select User Type</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id ?? '' }}"
                                            {{ $packagePlan->package_id == $package->id ? 'selected' : '' }}>
                                            {{ $package->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="plan_name">Package Plan Name</label>
                                <input type="text" id="name" name="plan_name" class="form-control"
                                    value="{{ $packagePlan->plan_name ?? '' }}" required>
                            </div>

                            <div class="form-group" id="plan-name-es" style="{{$packagePlan->plan_name_es ? '': 'display: none'}};">
                                <label for="plan_name"> Nombre del plan de paquete </label>
                                <input type="text" id="name_ed" name="plan_name_es" class="form-control"
                                    value="{{ $packagePlan->plan_name_es ??"" }}">
                            </div>

                            <div class="form-group">
                                <label for="plan_type">Package Plan Type </label>
                                <select name="plan_type" id="plan_type" class="form-control" required>
                                    <option value="" disabled>Select Package Plan Type</option>
                                    <option id="plan_type_opt" value="single"
                                        {{ $packagePlan->plan_type == 'single' ? 'selected' : '' }}>single</option>
                                    <option id="plan_type_opt" value="recurring"
                                        {{ $packagePlan->plan_type == 'recurring' ? 'selected' : '' }}>Recurring</option>
                                </select>
                            </div>
                            <div id="recurring_period_div">
                                <div class="form-group">
                                    <label for="recurring_period">Period</label>
                                    <select name="recurring_period" id="recurring_period" class="form-control"
                                        onchange="myFunction(event)" required>
                                        <option value="" disabled default>Select Period</option>
                                        <option value="day" {{$packagePlan->recurring_period == 'day' ? 'selected' : ''}}>day</option>
                                        <option value="month" {{ $packagePlan->recurring_period == 'month' ? 'selected' : '' }}>month
                                        </option>
                                        <option value="year" {{ $packagePlan->recurring_period == 'year' ? 'selected' : '' }}>year
                                        </option>
                                        <option id="every_month" value="every_month">every month</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Duration (Set 1000 when select period is Every month)</label>
                                    <input type="number" min="1" step="1" id="duration" name="duration"
                                        class="form-control" value="{{ $packagePlan->duration }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="amount">Price</label>
                                @if (isset($packagePlan->planPrices))
                                    @php
                                        $data = $packagePlan->planPrices->map(function ($q, $key) {
                                            return $q->price ?? '';
                                        });
                                    @endphp
                                    <input type="hidden" id="name" name="data" class="form-control"
                                        value="{{ $data }}">
                                    <div class="row">
                                        <?php  $data = array();
                                         $price = $packagePlan->planPrices->map(function ($q, $key) {
                                        ?>
                                        <div class="col-1">
                                            <input type="text" id="name" name="amount[{{ $key }}]"
                                                class="form-control" value="{{ $q->price }}">
                                            <input type="hidden" id="name" name="id[{{ $key }}]"
                                                class="form-control" value="{{ $q->id }}">
                                        </div>
                                        <?php
                                        });
                                        ?>
                                        {{-- {{$data}} --}}

                                    </div>
                                    @endif
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="summernote">{{ $packagePlan->description }}</textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>

                            <div class="form-group" id="plan-description-es" style="{{$packagePlan->plan_name_es ? '': 'display: none'}};">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="description_es" id="summernote_es">{{$packagePlan->description_es}} </textarea>
                                {{-- <textarea name="description" class="form-control" id="description" rows="3"></textarea> --}}
                            </div>

                            <div class="form-group">
                                <label for="password">Image</label>

                                <input type="file" id="file" name="image" class="form-control"
                                    value="{{ $packagePlan->image }}"
                                    onchange="document.getElementById('image').src = window.URL.createObjectURL(this.files[0])">
                                <div class="mt-2">
                                    <img id="image" src={{ asset($packagePlan->image ?? '') }} width="100"
                                        height="100">
                                </div>
                                <small>Image dimensions not greater then 300 by 300</small>
                            </div>
                            <div class="form-group form-check">
                                <input class="form-check-input" name="is_quantity_enable" type="checkbox" value="1"
                                    {{ $packagePlan->is_quantity_enable == 1 ? 'checked' : null }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Have Quantity option of this package.
                                </label>
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
        function myFunction(e) {
            var php_var = "<?php echo $packagePlan->duration; ?>";
            var value = e.target.value
            console.log(value);
            if (value == 'every_month') {
                return document.getElementById("duration").value = 2000;
            }
            return document.getElementById("duration").value = php_var;
        }

        function addAutoValueOnsignalPayment(e) {
            console.log(e);
            var value = e.target.value
            if (value == 'recurring') {
                console.log("this is test");
                return $("#recurring_period_div").show();
            }
            document.getElementById("duration").value = 1;
            document.getElementById("recurring_period").value = 'day';
            return $("#recurring_period_div").hide();
        }

        $('#plan_type').change(function(){
            if($('#plan_type').val() == 'recurring') {
                $('#recurring_period_div').show();
            } else {
                $('#recurring_period_div').hide();
            }
        });

        var planType = "<?php echo $packagePlan->plan_type; ?>";
        if( planType == "single") {
            document.getElementById("recurring_period").value = 'day';
            $('#recurring_period_div').hide();
        }
        $("#btn-click").click(function() {
            console.log("show");
            $("#plan-name-es").toggle();
            $("#plan-description-es").toggle();
        });
    </script>
@endsection
