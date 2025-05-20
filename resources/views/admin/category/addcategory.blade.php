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
                        <form method="POST" action="{{ route('admin.addcategory')}}" accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="tile-body">

                                <div class="form-group">
                                    <label class="">Category Name</label>
                                    <input class="form-control" placeholder="Category Name" id="cat_name" name="cat_name" type="text">
                                </div>

                                {{-- <div class="form-group">
                                    <label class="">Category Slug</label>
                                    <input class="form-control" name="cat_slug" placeholder="Category Slug" id="cat_slug" type="text">
                                </div> --}}

                                {{-- <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Choose Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Choose</button>
                                        </span>
                                    </div>
                                    <div id="image"></div>
                                </div> --}}




                            </div>

                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>
                                    @lang("Add Category")
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
                                // $('#submit-form').trigger("reset");
                                window.location.href = "{{ route('admin.categories') }}";
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
