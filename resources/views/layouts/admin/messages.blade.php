@if (Session::has('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<?php echo Session::get('success') ?>
	</div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger web-alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <?php echo Session::get('error') ?>
    </div>
@endif

@if (Session::has('invalid'))
    @foreach(Session::get('invalid') as $message)
        <div class="alert alert-danger web-alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
            {{ $message[0] }}
        </div>
    @endforeach
@endif

@if (Session::has('info'))
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ Session::get('info') }}
    </div>
@endif
@if (Session::has('danger'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ Session::get('danger') }}
    </div>
@endif

