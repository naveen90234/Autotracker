<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Forgot Password - Cook Up</title>
        <!-- base:css -->
        <link rel="stylesheet" href="{{ asset('assets/common/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/common/vendors/feather/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/common/vendors/base/vendor.bundle.base.css') }}">
        <!-- endinject -->
        <!-- plugin css for this page -->
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href="{{ asset('assets/common/css/style.css') }}">
        <!-- endinject -->
        <!-- <link rel="shortcut icon" href="{{ asset('assets/common/images/logo.png') }}" /> -->
    </head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <img src="{{ asset('assets/common/images/logo.png') }}" style="width: 70px;"
                                    alt="logo">
                            </div>
                            <h4 class="text-center">Forgot Password</h4>

                            <form method="POST" class="pt-3" action="{{ route('admin.forgotpassword') }}">
                                {{ csrf_field() }}

                                <div class="form-group">

                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
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

                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-account-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="email" id="email" name="email"
                                            class="form-control form-control-lg border-left-0" placeholder="Email"
                                            required="" autofocus="">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn"
                                        type="submit">Request new password</button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    <a href="{{ route('admin.login') }}"
                                        class="text-primary text-decoration-none">Login</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- base:js -->
    <script src="{{ asset('assets/common/vendors/base/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="{{ asset('assets/common/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/common/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/common/js/template.js') }}"></script>
    <!-- endinject -->
</body>

</html>
