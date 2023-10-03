<div class="mt-2 pb-4"><h3>Treatment Request</h3></div>
<div class="row">
	
			@foreach ($diseases as $disease)
		
			<div class="col-12 col-sm-6 col-md-3">
				<div class="info-box">
					<span class="info-box-icon bg-info elevation-1">
						<i class="fab fa-product-hunt"></i>
					</span>
					<div class="info-box-content">
						<span class="info-box-text">
							{{$disease->name}}
						</span>
						<span class="info-box-number">
							<a href="{{ route('admin.treatments',['disease'=>$disease->url_name]) }}" title="{{$disease->name}}">
							{{$disease->treatments_count}}
							</a>
						</span>
						</div>
				</div>
				</div>
		
			@endforeach
		
	{{-- <div class="col-12 col-sm-6 col-md-3">
		<div class="info-box mb-3">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fas fa-coins"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					E-coins
				</span>
				<span class="info-box-number">
					<a href="{{ route('admin.ecoins') }}" title="Inactive Users">
						{{ $total_ecoins }}
					</a>
				</span>
			</div>
		</div>
	</div>
	<div class="clearfix hidden-md-up"></div>
	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box mb-3">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fas fa-star-half-alt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Skill/Items Reviews
				</span>
				<span class="info-box-number">
					<a href="{{ route('admin.reviews') }}" title="Banned Users">
						{{ $total_reviews }}
					</a>
				</span>
			</div>
		</div>
	</div> --}}
</div>