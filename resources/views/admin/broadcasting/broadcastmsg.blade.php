@extends('admin.layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card mt-3">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif


                        <form id="sendnotify" action="{{ route('admin.broadcasting') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Send to</label>
                                <select class="form-control send-to-select" name="send_to"
                                    required>
                                    <option value="selected_users" selected>Selected Users</option>
                                    <option value="all_users">All Users</option>
                                </select>
                                <div style="margin-top: 15px" class="form-group">
                                    <span id="send_to_error" class="alert-danger" role="alert">
                                        @error('send_to')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>

                            <div class="form-group selected-users-box">

                                <label>Select Users</label>
                                <select class="select2 form-control select-users" multiple name="user_ids[]"
                                    required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->user_name }}</option>
                                    @endforeach
                                </select>
                                <div style="margin-top: 15px" class="form-group">
                                    <span id="user_ids_error" class="alert-danger" role="alert">
                                        @error('user_ids')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" value="{{ old('title') }}"
                                    id="title" placeholder="Enter title" name="title">
                                    <span id="title_error" class="alert-danger" role="alert">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div>
                            <div class="form-group">
                                <label for="notification_image">Notification Image</label>
                                <input type="file" class="form-control" id="notification_image" name="notification_image">
                            </div>

                            <div class="form-group">
                                <label for="notification_send_time">Notification Send Time</label>
                                <input type="datetime-local" class="form-control" id="notification_send_time" name="notification_send_time" required>
                            </div>
                            <div class="form-group">
                                <label>Message Body</label>
                                <textarea class="form-control" id="body" name="body"></textarea>
                                <div style="margin-top: 15px" class="form-group">
                                    <span id="body_error" class="alert-danger" role="alert">
                                        @error('body')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>
                            <button type="submit" id="submit" class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- The core Firebase JS SDK is always required and must be listed first -->
@endsection
@section('scripts')
    <script type="text/javascript">
        // var filter = $('.select-users');
        // filter.on('change', function() {
        //     if (this.selectedIndex) return; //not `Select All`
        //     filter.find('option:gt(0)').prop('selected', true);
        //     filter.find('option').eq(0).prop('selected', false);
        // });


        $('.send-to-select').on('change', function () {
            var select_val = $(this).val();
            if(select_val == 'all_users'){
                $('.selected-users-box').addClass('d-none');
            }else{
                $(".select-users").val([]).change();
                $('.selected-users-box').removeClass('d-none');
            }
        });
        $('.select-users').select2({
            placeholder: "Select Users",
            allowClear: true,
        });


        $(document).on("click", "#submit", function(e) {
            // alert("hello");
            e.preventDefault();
            var formdata = new FormData($('#sendnotify ')[0]);
            $("body").LoadingOverlay("show");
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.broadcasting') }}",
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $("body").LoadingOverlay("hide");

                    //console.log(response);

                    if (response.status == true) {

                        swal({
                            title: response.success,
                            icon: "success",
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                // $('#sendnotify ')[0].reset();
                                // $('.alert-danger').html('');
                                location.reload();

                            }
                        });

                        // $('.pip').remove();

                    } else {
                        $('.alert-danger').html('');
                        for (var key in response.error) {
                            //console.log(key);
                            console.log('value: ' + response.error[key]);

                            $("#" + key + "_error").html(response.error[key]);
                        }
                    }
                }
            });
        });
    </script>
@endsection
