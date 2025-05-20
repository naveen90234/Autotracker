@extends('admin.layouts.app')
@section('content')
    <style>
        div.dt-buttons {
            float: none;
        }

        div#car-list_length {
            display: contents;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            color: #9b7f7f;
        }

        .dropdown-menu {
            min-width: 6rem;
        }
    </style>

    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="row">
                                <div class="col-sm-12">

                                    <a href="{{ route('admin.cars.create') }}" class="btn btn-lg btn-primary mb-4 font-weight-bold">Add New Car</a>
                                    <a href="{{ route('admin.cars.upload') }}" class="btn btn-lg btn-success mb-4 font-weight-bold">Upload CSV</a>

                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Car Library</h5>
                                        </div>
                                        <div class="card-body">
                                            @if (session('status'))
                                                <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
                                                    {{ session('status') }}
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif
                                            <div class="dt-responsive table-responsive">
                                                <table id="car-list" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Year</th>
                                                            <th>Make</th>
                                                            <th>Model</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#car-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.cars.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'year', name: 'year' },
                    { data: 'make', name: 'make' },
                    { data: 'model', name: 'model' },
                    { data: 'status', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            $('#status').change(function() {
            table.draw();
        });

        $(document).on('click', '.togbtn', function(event) {
    event.preventDefault();
    var id = $(this).data('id');
    var el = $(this);
    var url = "{{ route('admin.cars.status') }}";

    $.ajax({
        method: 'POST',
        url: url,
        data: {
            _token: "{{ csrf_token() }}",
            id: id
        },
        success: function(response) {
            swal("Success", response.success, "success");
            el.prop('checked', response.val);
        },
        error: function(xhr) {
            swal("Error", xhr.responseJSON.message || "An error occurred", "error");
        }
    });
});

      $(document).on('click', '.delete_car', function() {
            let id = $(this).data('id');

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this Model!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: "{{ url('admin/cars/delete-car') }}/" + id,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    swal({
                        title: "Car Model Deleted Successfully!",
                        text: response.success,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        window.location.href = "{{ route('admin.cars.index') }}"; // Redirect to index
                    });
                },
                error: function(xhr) {
                    swal("Error", xhr.responseJSON.message || "An error occurred", "error");
                }
            });
        }
    });
  });


        });
    </script>
@endpush