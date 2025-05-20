
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login - {{env('APP_NAME')}}</title>
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
        <link rel="shortcut icon" href="{{ asset('assets/common/images/logo.png') }}" />
    </head>
    <style>
    .policy-links {
        position: absolute;
        bottom: 10px;
        text-align: center;
        width: 70%;
        font-size: 14px;
        color: #2255a3;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .policy-links a {
        color: #2255a3;
        text-decoration: none;
        margin: 0 10px;
    }

    .policy-links a:hover {
        text-decoration: underline;
    }
</style>


<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
                <div class="row flex-grow">
                    <div class="col-lg-6 d-flex align-items-center justify-content-center">
                        <div class="auth-form-transparent text-left p-3" style="width:83%;">
                            <div class="brand-logo">
                                <h1>{{env('APP_NAME')}}</h1>
                            </div>
                            <div class="my-3">

                                <p style="font-size: 19px;margin-bottom: 34px; word-spacing: 2px;">
                                {{env('APP_NAME')}} write your info here......
                                </p>
                                <a href="{{route('admin.login')}}" class="text-decoration-none">
                                    <button id="submit-btn"
                                    class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn"
                                    type="submit" style="width: 50%;"><i class="fa fa-sign-in fa-lg fa-fw"></i>Click here to Login</button>
                                </a>
                                <div class="policy-links">
                                    <a href="{{url('page/privacy-policy')}}">Privacy Policy</a> | 
                                    <a href="{{url('page/terms-and-condition')}}">Terms & Conditions</a>
                                </div>
                            </div>
                        </div>
                            
                    </div>
                    <div class="col-lg-6 login-half-bg d-flex flex-row">
                        <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy;
                            {{ date('Y') }} All rights reserved.</p>
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
        $('.password-eye-icon').on('click', function(){
            if($("#password").attr('type') == 'password'){
                $("#password").attr('type', 'text');
                $('.password-eye-icon').addClass('mdi-eye').removeClass('mdi-eye-off');

            }else{
                $("#password").attr('type', 'password');
                $('.password-eye-icon').addClass('mdi-eye-off').removeClass('mdi-eye');
            }
        });
    </script>
    <!-- endinject -->
</body>

</html>