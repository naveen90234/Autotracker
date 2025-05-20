@extends('admin.layouts.app')

@section('content')
    <style>
        div.dt-buttons {
            float: none;
        }

        div#driving-list_length {
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

    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="row">
                                <!-- HTML5 Export Buttons table start -->
                                <div class="col-sm-12">

                                    <a href="{{ route('admin.adddrivingstyle') }}"
                                        class="btn btn-lg btn-primary mb-4 font-weight-bold">Add Driving Style</a>

                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Driving Style List</h5>
                                        </div>
                                        <div class="card-body">
                                            @if (session('status'))
                                                <div class="alert alert-{{ session('type') }} alert-dismissible fade show"
                                                    role="alert">
                                                    {{ session('status') }}
                                                    <button type="button" class="close" data-dismiss="alert"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                            @endif
                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="close" data-dismiss="alert"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                            @endif
                                            <div class="dt-responsive table-responsive">
                                                <table id="driving-list" class="w-100 table table-striped table-bordered nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Driving Style</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection('content')

@push('scripts')
    <script>
    $(document).ready(function() {
        var table = $('#driving-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.driving_styles') }}",
                error: handleAjaxError,
                data: function(d) {
                    d.status = $('#status').val(); // Fixed trailing comma issue
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
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
    var url = "{{ route('admin.driving_styles.status') }}";

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


       $(document).on('click', '.delete_driving', function() {
            let id = $(this).data('id');

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this style!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: "{{ url('admin/driving_styles/delete-style') }}/" + id,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    swal({
                        title: "Driving Style Deleted Successfully!",
                        text: response.success,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        window.location.href = "{{ route('admin.driving_styles') }}"; // Redirect to index
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

@endpush('scripts')
