<div class="row">
    <div class="col-12 col-sm-6 col-md-3 col-3 mt-3">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-user-plus"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">
                     Users
                </span>
                <span class="info-box-number">
                      <a href="{{ route('admin.users') }}" title="users">
                        <h4 class="text-center">{{ $total_users  }}</h4>
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 col-3 mt-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-book"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">
                    Total Consultantion
                </span>
                <span class="info-box-number">
                    <a href="{{ route('admin.consultation') }}" title="total consultation">
                        <h4 class="text-center">{{ $total_consultation }}</h4>
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 col-3 mt-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-money-bill"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">
                    Total Revenue
                </span>
                <span class="info-box-number">
                    <h4 class="text-center">${{ $total_revenue }}</h4>
                </span>
            </div>
        </div>
    </div>
<div class="col-12 col-sm-6 col-md-3 col-3 mt-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-money-bill"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">
                    Total Subscription
                </span>
                <span class="info-box-number">
                    <a href="{{ route('admin.subscriber.list') }}" title="total subscription">
                        <h4 class="text-center">{{ $total_subscriber }}</h4>
                    </a>
                </span>
            </div>
        </div>
    </div>

</div>
<div class="row">


</div>
