@extends('admin.layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Add New Maintenance Task Type</h4>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.maintenance_task_types.store') }}" method="POST" id="submit-task-form">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Task Type Name</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="car_parts" class="form-label">Select Car Parts</label>
                            <select class="form-control select2" id="car_parts" name="car_parts[]" multiple required>
                                @foreach($carParts as $carPart)
                                    <option value="{{ $carPart->id }}">{{ $carPart->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Add Task Type
                            </button>
                            <a href="{{ route('admin.maintenance_task_types.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Initialize Select2 for dropdown
    $('#car_parts').select2({
        placeholder: "Select Car Parts",
        allowClear: true,
        width: '100%'
    });

    $('#submit-task-form').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('admin.maintenance_task_types.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("body").LoadingOverlay("show");
            },
            success: function (response) {
    $("body").LoadingOverlay("hide");
    console.log("Success Response:", response); // Debugging

    if (response.status === true || response.status === "true") {  // Ensure both boolean and string check
        swal({
            title: response.message || "Success!",
            icon: "success",
        }).then(() => {
            window.location.href = "{{ route('admin.maintenance_task_types.index') }}"; // Redirect after success
        });
    } else {
        swal({
            title: "Error",
            text: response.message || "Something went wrong!",
            icon: "error",
        });
    }
},

            error: function (xhr) {
                $("body").LoadingOverlay("hide");

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
