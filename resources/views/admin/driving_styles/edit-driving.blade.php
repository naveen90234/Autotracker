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
                        <form method="POST" action="{{ url('admin/driving_styles/update/' . $driving->id) }}" accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="tile-body">

                                <div class="form-group">
                                    <label class="">Driving Style Name</label>
                                    <input class="form-control" placeholder="Driving Style Name" id="name" name="name"
                                        type="text" value="{{ old('name') ?? $driving->name }}">
                                </div>
                            </div>

                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>
                                    @lang('Update Driving Style')
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
    <!-- container-scroller -->
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
                                window.location.href = "{{ url('admin/driving_styles') }}";

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
