<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - {{ env('APP_NAME') }}</title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('assets/common/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/common/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/common/vendors/base/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/common/css/style.css') }}">
    <!-- endinject -->
    <!-- <link rel="shortcut icon" href="{{ asset('assets/common/images/logo.png') }}" /> -->

    <style type="text/css">
        .login-form-container {
            border-radius: 5px;
            background: #fff;
            box-shadow: 1px 1px 10px #f3e4e4;
            padding: 1rem 4rem;
            height: fit-content;
        }

        .form-control-lg {
            padding: 0.94rem 1.94rem 0.94rem 0rem;
        }
        .content-wrapper{
            padding-left: 0px !important;
        }
    </style>

</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 align-items-center justify-content-center mx-auto login-form-container">
                        <div class="auth-form-transparent text-left p-3">
                            <div class="brand-logo-box text-center">
                                <div class="brand-logo">
                                    <h1>{{ env('APP_NAME') }}</h1>
                                </div>
                                <h4>ADMIN LOGIN!</h4>
                            </div>
                            <form autocomplete="off" class="login-form pt-3" id="loginform" method="post"
                                action="{{ route('admin.login') }}">
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
                                    <label for="exampleInputEmail">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-account-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="email" id="email" name="email"
                                            class="form-control form-control-lg border-left-0" placeholder="Email"
                                            required="" autofocus>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-lock-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="password" id="password"
                                            class="form-control form-control-lg border-left-0" id="exampleInputPassword"
                                            placeholder="Password" required="">
                                        <span id="span-password-eye">
                                            <i class="mdi mdi-eye-off text-primary password-eye-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" name="checkbox" value="1"
                                                class="form-check-input">
                                            Keep me signed in
                                        </label>
                                    </div>
                                    <a href="{{ route('admin.forgetpassword.view') }}"
                                        class="auth-link text-black">Forgot password?</a>
                                </div>
                                <div class="my-3">
                                    <button id="submit-btn"
                                        class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn"
                                        type="submit"><i class="fa fa-sign-in fa-lg fa-fw"></i>LOGIN</button>
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

    <script>
        $('.password-eye-icon').on('click', function() {
            if ($("#password").attr('type') == 'password') {
                $("#password").attr('type', 'text');
                $('.password-eye-icon').addClass('mdi-eye').removeClass('mdi-eye-off');

            } else {
                $("#password").attr('type', 'password');
                $('.password-eye-icon').addClass('mdi-eye-off').removeClass('mdi-eye');
            }
        });
    </script>
    <!-- endinject -->
</body>

</html>
