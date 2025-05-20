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
                    <h4 class="card-title">Edit Maintenance Task Type</h4>
                    <form action="{{ route('admin.maintenance_task_types.update', $taskType->id) }}" method="POST" id="update-task-type-form">
                        @csrf
                        @method('PUT')

                       <div class="mb-3">
                            <label for="title" class="form-label">Task Type Name</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $taskType->title }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="car_parts" class="form-label">Select Car Parts</label>
                            <select class="form-control select2" id="car_parts" name="car_parts[]" multiple required>
                                @foreach($carParts as $carPart)
                                    <option value="{{ $carPart->id }}" @if($taskType->carParts->contains($carPart->id)) selected @endif>
                                        {{ $carPart->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="tile-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Update Maintenance Task
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
    $(".select2").select2(); // Enable multi-select

    $('#update-task-type-form').on('submit', function (e) {
        e.preventDefault();

        console.log("Updating Maintenance Task Type..."); // ✅ Debug Log

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.maintenance_task_types.update', $taskType->id) }}",  // ✅ Fixed URL
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                console.log("Sending AJAX Update Request...");
                $("body").LoadingOverlay("show");
            },
            success: function (response) {
                $("body").LoadingOverlay("hide");
                console.log("Success Response:", response);

                if (response.status) {  // ✅ Fixed success check
                    swal({
                        title: response.message,
                        icon: "success",
                    }).then(() => {
                        window.location.href = "{{ route('admin.maintenance_task_types.index') }}";
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
                console.log("AJAX Error:", xhr);
                
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
