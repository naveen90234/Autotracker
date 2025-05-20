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
                                    <label class="">Name</label>
                                    <input class="form-control" placeholder="Name" id="name" name="name" type="text"
                                        value="@if(!empty($data)){{$data->name}}@endif">
                                </div>

                                <div class="form-group">
                                    <label class="">Email</label>
                                    <input class="form-control" placeholder="Email" id="email" type="email"
                                        disabled="disabled"
                                        value="@if (!empty($data)) {{ $data->email }} @endif">
                                </div>


                                <div class="form-group">
                                    <label>Profile picture</label>
                                    @if($data->profile_picture_url)
                                    <div>
                                        <img class="sm-img" src="{{ $data->profile_picture_url }}">
                                    </div>
                                    @endif
                                    <input type="file" name="profile_picture" id="profile_picture" class="file-upload-default" accept="image/*;capture=camera">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>
                                    @lang("Update Profile")
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

        const imgInput = document.querySelector('#profile_picture');
        const imgEl = document.querySelector('.sm-img');
        imgInput.addEventListener('change', () => {
        if (imgInput.files && imgInput.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
            imgEl.src = e.target.result;
            }
            reader.readAsDataURL(imgInput.files[0]);
        }
        });

        $(function() {

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
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
