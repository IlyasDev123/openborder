<div id="success"></div>
<form id="cform" enctype="multipart/form-data"
oninput='password_confirmation.setCustomValidity(password_confirmation.value != password.value ? "Passwords do not match." : "")'>
    @csrf
    @include('layouts.admin.messages')

    <input type="hidden" name="user_id" value="{{ $user->id }}">

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="form-group">
                <label for="name">Email*</label>
                <input type="text" id="name" name="email" class="form-control" value="{{$user->email}}" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="form-group">
                <label for="name">New Password*</label>
                <input type="password" id="name" name="password" class="form-control" value="" required>
            </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="form-group">
                <label for="name">Confirm Password*</label>
                <input type="password" id="name" name="password_confirmation" class="form-control" value=""
                    required>
            </div>
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-lg btn-submit">Change Password
            <span style="display: none" id="loading-image"><img
                    src="{{ asset('loader/loader.gif') }}" title="loader"
                    width="30" height="30" /></span>
        </button>
    </div>
</form>
@section('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-submit").click(function(e) {
        e.preventDefault();
        var user_id = $("input[name=user_id]").val();
        var password = $("input[name=password]").val();
        var password_confirmation = $("input[name=password_confirmation]").val();
        var email = $("input[name=email]").val();

        $.ajax({
            type: 'POST',
            url: "{{ route('user.change.password') }}",
            data: {
                user_id: user_id,
                password: password,
                password_confirmation: password_confirmation,
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
            success: function(data) {
                $('#loading-image').hide();
                var message = data.message;
                if(data.status == true){
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+message+'</div>');
                    document.getElementById("cform").reset();
                }
            },
            error:function (response){

                $('#loading-image').hide();
                    $.each(response.responseJSON.message,function(field_name,error){
                        $(document).find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                    })
                }
        });

    });
</script>
@endsection
