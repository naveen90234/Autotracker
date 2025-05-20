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
                {{-- <h3 class="tile-title">{{ $title }}</h3> --}}
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" accept-charset="UTF-8" id="submit-form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-block">

                                <h3><strong>iOS Settings<strong></h3><hr>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="ios_maintenance">iOS Maintenance</label>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="ios_maintenance"
                                                {{ $data['ios_maintenance'] == 1 ? 'checked' : '' }}
                                                class="custom-control-input" id="ios_maintenance" value="1">
                                            <label class="custom-control-label" for="ios_maintenance">Yes</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="ios_maintenance" class="custom-control-input"
                                                id="ios_maintenance1" value="0"
                                                {{ $data['ios_maintenance'] == 0 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ios_maintenance1">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="ios_force_update">iOS Force
                                        Update</label>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="ios_force_update"
                                                {{ $data['ios_force_update'] == 1 ? 'checked' : '' }}
                                                class="custom-control-input" id="ios_force_update" value="1">
                                            <label class="custom-control-label" for="ios_force_update">Yes</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="ios_force_update" class="custom-control-input"
                                                id="ios_force_update1" value="0"
                                                {{ $data['ios_force_update'] == 0 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ios_force_update1">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="ios_message">iOS Message</label>
                                    <div class="col-md-9">
                                        <input type="text" name="ios_message" placeholder="Enter iOS Message"
                                            class="form-control" required="" value="{{ $data['ios_message'] }}" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="ios_version">iOS Version</label>
                                    <div class="col-md-9">
                                        <input type="text" name="ios_version" placeholder="Enter iOS Version"
                                            class="form-control" required="" value="{{ $data['ios_version'] }}" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="ios_app_link">iOS App Link</label>
                                    <div class="col-md-9">
                                        <input type="text" name="ios_app_link" placeholder="Enter iOS App Link"
                                            class="form-control" required="" value="{{ $data['ios_app_link'] }}" />
                                    </div>
                                </div>

                                <br><br><h3><strong>Android Settings<strong></h3><hr>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="android_maintenance">Android
                                        Maintenance</label>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="android_maintenance"
                                                {{ $data['android_maintenance'] == 1 ? 'checked' : '' }}
                                                class="custom-control-input" id="android_maintenance" value="1">
                                            <label class="custom-control-label" for="android_maintenance">Yes</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="android_maintenance" class="custom-control-input"
                                                id="android_maintenance1" value="0"
                                                {{ $data['android_maintenance'] == 0 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="android_maintenance1">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="android_force_update">Android Force
                                        Update</label>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="android_force_update"
                                                {{ $data['android_force_update'] == 1 ? 'checked' : '' }}
                                                class="custom-control-input" id="android_force_update" value="1">
                                            <label class="custom-control-label" for="android_force_update">Yes</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-sm-2">
                                            <input type="radio" name="android_force_update"
                                                class="custom-control-input" id="android_force_update1" value="0"
                                                {{ $data['android_force_update'] == 0 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="android_force_update1">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="android_message">Android
                                        Message</label>
                                    <div class="col-md-9">
                                        <input type="text" name="android_message" placeholder="Enter Android Message"
                                            class="form-control" required=""
                                            value="{{ $data['android_message'] }}" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="android_version">Android
                                        Version</label>
                                    <div class="col-md-9">
                                        <input type="text" name="android_version" placeholder="Enter Android Version"
                                            class="form-control" required=""
                                            value="{{ $data['android_version'] }}" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="android_app_link">Android App
                                        Link</label>
                                    <div class="col-md-9">
                                        <input type="text" name="android_app_link"
                                            placeholder="Enter Android App Link" class="form-control" required=""
                                            value="{{ $data['android_app_link'] }}" />
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-lg btn-primary">Update</button>

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
                            if (isConfirm) {
                                window.location.href = '{{ route('edit_version') }}';
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
                                window.location.href = '{{ route('edit_version') }}';
                            }
                        });
                        // Alert(response.message,false);
                    }
                }
            });
        });
    </script>
@endsection
