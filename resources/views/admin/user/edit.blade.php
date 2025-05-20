@extends('admin.layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">

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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ url('admin/users/update/' . $user->id) }}" class="forms-sample"
                            accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="user_name">Username</label>
                                <input type="text" class="form-control" value="{{ old('user_name') ?? $user->user_name }}"
                                    id="user_name" placeholder="Username" name="user_name">
                            </div>

                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control" id="email" placeholder="Email"
                                    value="{{ old('email') ?? $user->email }}" name="email" autocomplete="off" disabled>
                            </div>
                            <div class="form-group">
                                <label for="country_code">Country Code</label>
                                <span id="country_code_span"><input type="number" class="form-control" id="country_code" placeholder="Country Code"
                                    value="{{ old('country_code') ?? $user->country_code }}" name="country_code">
                                </span>
                                </div>
                            <div class="form-group">
                                <label for="mobile">Mobile no.</label>
                                <input type="number" class="form-control" id="mobile" placeholder="Mobile Number"
                                    value="{{ old('mobile') ?? $user->mobile }}" name="mobile">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Password"
                                    name="password" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="confirm-password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password"
                                    placeholder="Confirm Password" name="password_confirmation" autocomplete="new-password">
                            </div>

                            @if ($user->profile_picture)
                                <div class="form-group">
                                    <label>Profile Picture</label>
                                    <div>
                                        <a href={{$user->profile_picture}} target="_blank"><img class="sm-img" src="{{ $user->profile_picture }}"></a>
                                    </div>
                                    <input type="file" name="profile_picture" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" @if ($user->status == 1) selected @endif>Active</option>
                                    <option value="0" @if ($user->status == 0) selected @endif>Inactive
                                    </option>
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="latitude" class="form-control" id="latitude" placeholder="Latitude"
                                    name="latitude" value="{{ old('latitude') ?? $user->latitude }}">
                            </div>

                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="longitude" class="form-control" id="longitude" placeholder="Longitude"
                                    name="longitude" value="{{ old('longitude') ?? $user->longitude }}">
                            </div> --}}

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a href="{{ url('admin/users-list') }}"><button type="button"
                                    class="btn btn-light">Cancel</button></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

        // const imgInput = document.querySelector('.file-upload-default');
        // const imgEl = document.querySelector('.sm-img');
        // imgInput.addEventListener('change', () => {
        // if (imgInput.files && imgInput.files[0]) {
        //     const reader = new FileReader();
        //     reader.onload = (e) => {
        //     imgEl.src = e.target.result;
        //     }
        //     reader.readAsDataURL(imgInput.files[0]);
        // }
        // });

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
                                window.location.href = "{{ url('admin/users-list') }}";
                            }
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
