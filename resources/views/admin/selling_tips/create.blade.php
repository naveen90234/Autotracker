@extends('admin.layouts.app')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <h4 class="card-title">Add New Selling Tip</h4>
                        <form action="{{ route('admin.selling_tips.store') }}" method="POST" id="submit-form" enctype="multipart/form-data">
                            @csrf
                            <div class="tile-body">
                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter Title" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Enter Description" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="image">Image:</label>
                                    <input type="file" name="image" class="form-control-file">
                                </div>
                            </div>

                            <div class="tile-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-fw fa-lg fa-check-circle"></i> Add Selling Tip
                                </button>
                                <a href="{{ route('admin.selling_tips.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $('#submit-form').on('submit', function (e) {
            e.preventDefault();
            
            console.log("Form Submitted");  // ✅ Debug Log

            let formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('admin.selling_tips.store') }}", // Ensure the route is correct
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    console.log("Sending AJAX Request");  // ✅ Debug Log
                    $("body").LoadingOverlay("show");
                },
                success: function (response) {
                    $("body").LoadingOverlay("hide");
                    console.log("Success Response:", response);  // ✅ Debug Log

                    if (response.status === 'true') {
                        swal({
                            title: response.message,
                            icon: "success",
                        }).then(() => {
                            window.location.href = "{{ route('admin.selling_tips.index') }}";
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });
                    }
                },
                error: function (xhr) {
                    $("body").LoadingOverlay("hide");
                    console.log("AJAX Error:", xhr);  // ✅ Debug Log
                    
                    let errors = xhr.responseJSON?.errors || {};
                    let errorMessage = Object.values(errors).flat().join("\n") || "Something went wrong!";
                    
                    swal({
                        title: "Validation Error",
                        text: errorMessage,
                        icon: "error",
                    });
                }
            });
        });
    });
</script>



@endsection