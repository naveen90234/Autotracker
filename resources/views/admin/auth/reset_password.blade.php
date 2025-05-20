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

                            <div>
                                <h4 class="text-center">Set your new password</h4>

                                <form autocomplete="off" class="form-horizontal m-t-20" id="resetPasswordForm"
                                    method="post" action="{{ route('admin.resetpassword', $token) }}">
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
                                    <div class="row p-b-30">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <input name="password" id="password" type="password"
                                                    class="form-control form-control-lg" placeholder="New Password"
                                                    aria-label="Password" aria-describedby="basic-addon1">
                                            </div>
                                            <div class="mb-3">
                                                <input name="password_confirmation" id="password_confirmation"
                                                    type="password" class="form-control form-control-lg"
                                                    placeholder="Confirm Password" aria-label="Confirm Password"
                                                    aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row border-secondary">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="p-t-20">
                                                    <a href="{{ route('admin.login') }}"><button
                                                            class="btn btn-info" id="to-recover" type="button"><i
                                                                class="fa fa-lock m-r-5"></i> Back to Login</button></a>
                                                    <button id="submit-btn" class="btn btn-success float-right"
                                                        type="submit"><span id="licon"></span>Update Password</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
    <script src="{{ asset('assets/common/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/template.js') }}"></script>
    <script src="{{ asset('assets/common/js/plugins/jquery.form.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/custom.js') }}"></script>
    <!-- endinject -->

    <script>
        $(document).ready(function() {
            // bind 'myForm' and provide a simple callback function
            $('#resetPasswordForm').ajaxForm({
                beforeSubmit: function() {
                    $(".error").remove();
                    disable("#submit-btn", true);
                },
                error: function(err) {
                    handleError(err);
                    disable("#submit-btn", false);
                },
                success: function(response) {
                    if (response.status == 'true') {
                        swal({
                            title: response.message,
                            icon: "success",
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                window.location.href = response.url;
                            }
                        });
                    } else {
                        disable("#submit-btn", false);
                        Alert(response.message, false);
                    }
                }
            });
        });
    </script>

</body>

</html>
