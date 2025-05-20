<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/common/images/logo.png') }}"> -->

    <!-- Favicon icon -->

    <title>{{isset($title)?$title:config('app.name', env('APP_NAME'))}}</title>
    <!-- Custom CSS -->
    @include('admin.layouts.css')
    @yield('styles')
</head>

<body>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div class="container-scroller">
	<!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        @include('admin.layouts.header')
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
		 <div class="container-fluid page-body-wrapper">
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        @include('admin.layouts.sidebar')
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
          <div class="main-panel">

            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
			<div class="content-wrapper">
			 @include('admin.layouts.breadcrumb')

               @yield('content')
			</div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->

		 <!-- footer -->
            <!-- ============================================================== -->

            @include('admin.layouts.footer')
            <!-- ============================================================== -->
            <!-- End footer -->


        </div>
        </div>

        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    @include('admin.layouts.js')
    @yield('scripts')
</body>

</html>
