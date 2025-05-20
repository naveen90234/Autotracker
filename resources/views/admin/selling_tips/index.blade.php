@extends('admin.layouts.app')
@section('content')
    <style>
        div.dt-buttons {
            float: none;
        }

        div#selling-tips-list_length {
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
                                <!-- Selling Tips Articles Table -->
                                <div class="col-sm-12">

                                    <a href="{{ route('admin.selling_tips.create') }}"
                                        class="btn btn-lg btn-primary mb-4 font-weight-bold">Add New Selling Tip</a>

                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Selling Tips Articles</h5>
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
                                                <table id="selling-tips-list" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Image</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
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

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
    var table = $('#selling-tips-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.selling_tips.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

   $('#status').change(function() {
            table.draw();
        });

        $(document).on('click', '.toggle-status', function(event) {
    event.preventDefault();
    var id = $(this).data('id');
    var el = $(this);
    var url = "{{ route('admin.selling_tips.status') }}";

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


       $(document).on('click', '.delete_selling', function() {
    let id = $(this).data('id');

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this Article!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: "{{ url('admin/selling_tips/delete-article') }}/" + id,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    swal({
                        title: "Article Deleted Successfully!",
                        text: response.success,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        window.location.href = "{{ route('admin.selling_tips.index') }}"; // Redirect to index
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
