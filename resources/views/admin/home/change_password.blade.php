@extends('admin.layouts.app')
@section('content')

    <div class="row">

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">


                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="" accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="tile-body">

                                <div class="form-group">
                                    <label class="">Current Password</label>
                                    <input class="form-control" placeholder="Current Password" id="current_password"
                                        name="current_password" type="password" autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label class="">New Password</label>
                                    <input class="form-control" placeholder="New Password" id="new_password"
                                        name="new_password" type="password" value="">
                                </div>
                                <div class="form-group">
                                    <label class="">Confirm Password</label>
                                    <input class="form-control" placeholder="Confirm Password" id="confirm_password"
                                        name="confirm_password" type="password" value="">
                                </div>


                            </div>
                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>
                                    @lang("Update Password")
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function() {
            $('#submit-form').ajaxForm({

                beforeSubmit: function() {
                    $(".error").remove();
                    disable("#submit-btn", true);
                    $("body").LoadingOverlay("show");
                },
                error: function(err) {
                    $("body").LoadingOverlay("hide");
                    handleError(err);
                    disable("#submit-btn", false);
                },

                success: function(response) {
                    disable("#submit-btn", false);
                    $("body").LoadingOverlay("hide");
                    if (response.status == 'true') {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "success",
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {}
                        });

                        $('#submit-form')[0].reset();

                    } else {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "error",
                            dangerMode: true,
                        }).then(function(isConfirm) {
                            if (isConfirm) {}
                        });
                    }
                }

            });
        });
    </script>
@endsection
