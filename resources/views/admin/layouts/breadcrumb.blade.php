<div class="row">
    <div class="col-sm-12 mb-4 mb-xl-0">
        <h3 class="font-weight-bold text-dark"><i
                class="{{ $breadcrumbs[0]['icon'] ?? 'fa fa-th-list mr-2' }}"></i>{{ isset($title) ? $title : '' }}</h3>
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home fa-lg"></i></a></li>

            <?php if(isset($breadcrumbs) && count($breadcrumbs)>0){ ?>
            <?php  foreach($breadcrumbs as $breadcrumb){ ?>
            <?php if($breadcrumb['relation']=="link"){?>
            <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
            <?php }else{ ?>
            <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
            <?php } ?>
            <?php } ?>
            <?php } ?>

        </ul>
    </div>

</div>

@if (session('success_msg'))
    <div class="alert alert-success">
        {{ session('success_msg') }}
    </div>
@endif
@if (session('error_msg'))
    <div class="alert alert-danger">
        {{ session('error_msg') }}
    </div>
@endif

