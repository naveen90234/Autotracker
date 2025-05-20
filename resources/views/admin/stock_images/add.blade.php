@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Add New Stock Image</h4>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('admin.stock_images.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="title">Image Title</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Enter Image Title" required>
                            </div>

                            <div class="form-group">
                                <label for="image">Upload Image</label>
                                <input type="file" name="image" id="image" class="form-control-file" required>
                                <small class="text-muted">Allowed formats: jpeg, png, jpg, gif, svg. Max size: 2MB.</small>
                            </div>

                            <div class="form-group">
                                <label for="status">Display Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Active" selected>Active (Visible to users)</option>
                                    <option value="Inactive">Inactive (Hidden from users)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Upload Image</button>
                            <a href="{{ route('admin.stock_images') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            $('#image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#preview-image').attr('src', e.target.result).removeClass('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            });

            $('#submit-form').ajaxForm({
                beforeSubmit: function() {
                    $(".error").remove();
                    $("body").LoadingOverlay("show");
                },
                error: function(err) {
                    $("body").LoadingOverlay("hide");
                    swal({
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        dangerMode: true,
                    });
                },
                success: function(response) {
                    $("body").LoadingOverlay("hide");
                    swal({
                        title: "Success!",
                        text: "Stock image uploaded successfully.",
                        icon: "success",
                    }).then(() => {
                        window.location.href = '{{ route('admin.stock_images') }}';
                    });
                }
            });
        });
    </script>
@endsection
