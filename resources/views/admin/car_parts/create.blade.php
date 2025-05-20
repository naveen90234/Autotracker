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
                    <h4 class="card-title">Add New Car Part</h4>
                    <form action="{{ route('car_parts.store') }}" method="POST" id="submit-car-part-form">
                        @csrf
                        <div class="tile-body">
                            <div class="form-group">
                                <label for="name">Part Name:</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Part Name" required>
                            </div>

                            <div class="form-group">
                                <label for="cars">Select Cars:</label>
                                <select name="cars[]" class="form-control select2" multiple required>
                                    @foreach ($cars as $car)
                                        <option value="{{ $car->id }}">{{ $car->model }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="task_types">Maintenance Task Types</label>
                                <select name="task_types[]" id="task_types" class="form-control select2" multiple required>
                                    @foreach($taskTypes as $taskType)
                                        <option value="{{ $taskType->id }}" {{ in_array($taskType->id, $selectedTaskTypes ?? []) ? 'selected' : '' }}>
                                            {{ $taskType->title }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="tile-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Add Car Part
                            </button>
                            <a href="{{ route('admin.car_parts.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 for Multi-select Dropdown -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ placeholder: "Select Options" });

    $('#submit-car-part-form').on('submit', function (e) {
        e.preventDefault();

        console.log("Car Parts Form Submitted");  // ✅ Debug Log

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('car_parts.store') }}",
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
                        window.location.href = "{{ route('admin.car_parts.index') }}";
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
