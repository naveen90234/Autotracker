<!-- partial:partials/_navbar.html -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

        {{-- <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}"><img style="width: 60px; height:50px;"
                src="{{ asset('assets/common/images/logo.png') }}" alt="logo" /></a>
                {{-- <h4>{{ config('app.name') }}</h4>
        <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}"><img
                src="{{ asset('assets/common/images/logo.png') }}" style="width: 35px; height:30px;" alt="logo" /></a> --}}
        <h3 class="app-logo-text">{{env('APP_NAME')}}</h3>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize"
            style="border-right:none">
            <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item dropdown d-flex mr-4 ">
                <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center"
                    id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="icon-cog"></i>
                </a>
                <div class="dropdown-menu top-right-menu-dropdown dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Settings</p>
                    <a href="{{ route('admin.profile') }}" class="dropdown-item preview-item">
                        <i class="icon-head"></i> Profile
                    </a>
                    <a href="{{ route('admin.changepassword') }}" class="dropdown-item preview-item">
                        <i class="icon-head fa fa-key fa-lg"></i> Change Password
                    </a>

                    <a href="{{ route('admin.logout') }}" class="dropdown-item preview-item">
                        <i class="icon-inbox"></i> Logout
                    </a>
                </div>
            </li>

        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->
