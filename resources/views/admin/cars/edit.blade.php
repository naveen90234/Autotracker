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
                    <h4 class="card-title">Edit Car</h4>
                    <form action="{{ route('admin.cars.update', $car->id) }}" method="POST" id="edit-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="tile-body">
                            <div class="form-group">
                                <label for="year">Year:</label>
                                <input type="text" name="year" class="form-control" value="{{ $car->year }}" required>
                            </div>

                            <div class="form-group">
                                <label for="make">Make:</label>
                                <input type="text" name="make" class="form-control" value="{{ $car->make }}" required>
                            </div>

                            <div class="form-group">
                                <label for="model">Model:</label>
                                <input type="text" name="model" class="form-control" value="{{ $car->model }}" required>
                            </div>

                        </div>

                        <div class="tile-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Update Car
                            </button>
                            <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#edit-form').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.cars.update', $car->id) }}", // Ensure route is correct
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                console.log("Sending AJAX Request");
                $("body").LoadingOverlay("show");
            },
            success: function (response) {
                $("body").LoadingOverlay("hide");

                if (response.status === 'true') {
                    swal({
                        title: response.message,
                        icon: "success",
                    }).then(() => {
                        window.location.href = "{{ route('admin.cars.index') }}";
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
