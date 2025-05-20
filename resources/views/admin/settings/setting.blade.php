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
        <!-- Start Content Box -->
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="card-block">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">Poll Distance</label>
                                    <div class="col-md-9">
                                        <input type="text" name="poll_distance" placeholder="Set Poll Distance"
                                            class="form-control" required="" value="{{ $poll_distance->value }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="card-block">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="map_icon">Physical poll map icon</label>
                                    <div class="col-md-9">
                                        {{-- @if($map_icon['value']) --}}
                                        <div>
                                            <img class="sm-img" src="{{ $map_icon['value'] }}">
                                        </div>
                                        {{-- @endif --}}
                                        <input type="file" name="map_icon" id="map_icon" class="file-upload-default" accept="image/*;capture=camera">
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled
                                                placeholder="Upload Image">
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i>Update</button>

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

            const imgInput = document.querySelector('#map_icon');
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
                            if (isConfirm) {
                                window.location.href = '{{ route('manage_setting') }}';
                            }
                        });
                    } else {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "error",
                            dangerMode: true,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                //console.log(response);
                                window.location.href = '{{ route('manage_setting') }}';
                            }
                        });
                        // Alert(response.message,false);
                    }
                }
            });
        });
    </script>
@endsection
