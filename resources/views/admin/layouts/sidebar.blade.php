<?php
$adminData = Auth::guard('admin')->user();
$currentAction = \Route::currentRouteAction();
[$controller, $action] = explode('@', $currentAction);
$controller = preg_replace('/.*\\\/', '', $controller);

?>
<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="user-profile">
        <div class="user-image">
            <img src="@if($adminData->profile_picture_url != "") {{ $adminData->profile_picture_url }} @else {{  asset('assets/common/images/default-avatar.png') }} @endif ">
        </div>
        <div class="user-name">
            {{ $adminData->name }}
        </div>
        <div class="user-designation">
            {{ $adminData->email }}
        </div>
    </div>
    <ul class="nav">
        <li class="nav-item  {{ \Request::is('admin/home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="icon-box menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <?php $active = $controller == 'HomeController' && in_array($action, ['profile', 'view']) ? 'active selected' : ''; ?>
         <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.profile') }}">
                <i class="icon-file menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.categories') }}">
                <i class="icon-align-justify menu-icon"></i>
                <span class="menu-title">Category</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.stock_images') }}">
                <i class="icon-cog menu-icon"></i>
                <span class="menu-title">Stock Images</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.driving_styles') }}">
                <i class="icon-book menu-icon"></i>
                <span class="menu-title">Driving Styles</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.selling_tips.index') }}">
                <i class="icon-flag menu-icon"></i>
                <span class="menu-title">Selling tips</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.cars.index') }}">
                <i class="icon-file menu-icon"></i>
                <span class="menu-title">Cars Management</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.car_parts.index') }}">
                <i class="icon-book menu-icon"></i>
                <span class="menu-title">Car Parts Management</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.maintenance_task_types.index') }}">
                <i class="icon-repeat menu-icon"></i>
                <span class="menu-title">Maintenance Task Types</span>
            </a>
        </li>


        <?php $active = $controller == 'UserController' && in_array($action, ['usersList']) ? 'active selected' : ''; ?>
        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.users-list') }}">
                <i class="icon-head menu-icon"></i>
                <span class="menu-title">User Management</span>
            </a>
        </li>


        <?php $active = $controller == 'SubscriptionController' && in_array($action, ['subscription']) ? 'active selected' : ''; ?>
        {{-- <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.subscription') }}">
                <i class="icon-repeat menu-icon"></i>
                <span class="menu-title">Subscription Plans</span>
            </a>
        </li> --}}

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.broadcasting') }}">
                <i class="icon-volume menu-icon"></i>
                <span class="menu-title">Broadcast</span>
            </a>
        </li>

        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('welcomePage') }}">
                <i class="icon-file menu-icon"></i>
                <span class="menu-title">Welcome Page</span>
            </a>
        </li>

        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.reported-user-list') }}">
                <i class="icon-flag menu-icon"></i>
                <span class="menu-title">Report Management</span>
            </a>
        </li>

        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ url('admin/email-templates') }}">
                <i class="icon-mail menu-icon"></i>
                <span class="menu-title">Email Templates</span>
            </a>
        </li>

        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.account-deletion-requests') }}">
                <i class="icon-delete menu-icon"></i>
                <span class="menu-title">Delete Request</span>
            </a>
        </li>

        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.support_list') }}">
                <i class="icon-mail menu-icon"></i>
                <span class="menu-title">Support / Feedback</span>
            </a>
        </li>

        <?php $active = $controller == 'AppPageController' && in_array($action, ['appPageList']) ? 'active selected' : ''; ?>
        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('appPage') }}">
                <i class="icon-file menu-icon"></i>
                <span class="menu-title">CMS Pages</span>
            </a>
        </li>
        <?php $active = $controller == 'SettingController' && in_array($action, ['editSetting']) ? 'active selected' : ''; ?>
        <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('edit_version') }}">
                <i class="icon-book menu-icon"></i>
                <span class="menu-title">App Versions</span>
            </a>
        </li>

        <?php $active = $controller == 'SettingController' && in_array($action, ['manageSetting']) ? 'active selected' : ''; ?>
        {{-- <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('manage_setting') }}">
                <i class="icon-cog menu-icon"></i>
                <span class="menu-title">Setting</span>
            </a>
        </li> --}}

        <?php $active = $controller == 'HomeController' && in_array($action, ['changePassword']) ? 'active selected' : ''; ?>
        {{-- <li class="nav-item {{ $active }}">
            <a class="nav-link" href="{{ route('admin.changepassword') }}">
                <i class="icon-lock menu-icon"></i>
                <span class="menu-title">Change Password</span>
            </a>
        </li> --}}


        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.logout') }}">
                <i class="icon-inbox menu-icon"></i>
                <span class="menu-title">Logout</span>
            </a>
        </li> --}}



    </ul>
</nav>
<!-- partial -->
