@extends('admin.layouts.app')

@section('content')
    <style>
        div.dt-buttons {
            float: none;
        }

        div#category-list_length {
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

                                    <a href="{{ route('admin.addcategory') }}"
                                        class="btn btn-lg btn-primary mb-4 font-weight-bold">Add category</a>

                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Categories List</h5>
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
                                                <table id="category-list" class="w-100 table table-striped table-bordered nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            {{-- <th>Image</th> --}}
                                                            <th>Created at</th>
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
        var table = $('#category-list').DataTable({
            processing: true,
            serverSide: true,
            // pageLength: 2,
            ajax: {
                url:"{{ route('admin.categories') }}",
                error: handleAjaxError,
            },
            // dom: 'Blfrtip',
            //   buttons: [
            //        {
            //            extend: 'pdf',
            //            exportOptions: {
            //                columns: [1,3] // Column index which needs to export
            //            }
            //        },
            //        {
            //            extend: 'csv',
            //            exportOptions: {
            //                columns: [1,3] // Column index which needs to export
            //            }
            //        },
            //        {
            //            extend: 'excel',
            //            exportOptions: {
            //                columns: [1,3] // Column index which needs to export
            //            }
            //        },
            //   ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                // {
                //     data: 'image',
                //     name: 'image',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });


        $(document).on('click', '.delete_category', function() {
            let id = $(this).data('id');
            swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this category!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = "{{ url('admin/category/delete') }}" + '/' + id;
                    }
                });

        });
    </script>
@endpush('scripts')
