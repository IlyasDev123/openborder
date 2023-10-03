<div class="mt-2 pb-4"><h3>Google Analytics Data</h3></div>
<div class="row">
	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fab fa-product-hunt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Active User On Site
				</span>
				<span class="info-box-number">
					{{$active_users_on_site}}
				</span>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fab fa-product-hunt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Daily Record
				</span>
				<span class="info-box-number d-flex ">
					<div class="ml-2">
						<div class="info-box-text">Visitors</div>
						<div class="text-center">{{$daily_visted_user->totalsForAllResults['ga:Users']}}</div>
					</div>
					<div class="ml-2">
						<div class="info-box-text">Page Views</div>
						<div class="text-center ">{{$daily_visted_user->totalsForAllResults['ga:pageviews']}}</div>
					</div>
				</span>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fab fa-product-hunt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Weekly Record
				</span>
				<span class="info-box-number d-flex ">
					<div class="ml-2">
						<div class="info-box-text">Visitors</div>
						<div class="text-center">{{$weekly_visted_user->totalsForAllResults['ga:Users']}}</div>
					</div>
					<div class="ml-2">
						<div class="info-box-text">Page Views</div>
						<div class="text-center ">{{$weekly_visted_user->totalsForAllResults['ga:pageviews']}}</div>
					</div>
				</span>
			</div>
		</div>
	</div>

	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fab fa-product-hunt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Monthly Record
				</span>
				<span class="info-box-number d-flex ">
					<div class="ml-2">
						<div class="info-box-text">Visitors</div>
						<div class="text-center">{{$monthly_visted_user->totalsForAllResults['ga:Users']}}</div>
					</div>
					<div class="ml-2">
						<div class="info-box-text">Page Views</div>
						<div class="text-center ">{{$monthly_visted_user->totalsForAllResults['ga:pageviews']}}</div>
					</div>
				</span>
			</div>
		</div>
	</div>

	<div class="col-12 col-sm-6 col-md-3">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1">
				<i class="fab fa-product-hunt"></i>
			</span>
			<div class="info-box-content">
				<span class="info-box-text">
					Yearly Record
				</span>
				<span class="info-box-number d-flex ">
					<div class="ml-2">
						<div class="info-box-text">Visitors</div>
						<div class="text-center">{{$yearly_visted_user->totalsForAllResults['ga:Users']}}</div>
					</div>
					<div class="ml-2">
						<div class="info-box-text">Page Views</div>
						<div class="text-center ">{{$yearly_visted_user->totalsForAllResults['ga:pageviews']}}</div>
					</div>
				</span>
			</div>
		</div>
	</div>
		<div>
		</div>
</div>