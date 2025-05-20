@extends('admin.layouts.app')
@section('content')

 <style>
     div.dt-buttons{
        float: none;
    }
    div#userTable_length {
        display: contents;
    }

    .dropdown-item.active, .dropdown-item:active{
        color:#9b7f7f;
    }
    .dropdown-menu {
        min-width:6rem;
    }
 </style>
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <form id="filter_form">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select id='status' class="form-control" style="width: 200px">
                                        <option value="">Select</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id='subscribe' class="form-control" style="width: 200px">
                                        <option value="">Select</option>
                                        <option value="1">Premium Users</option>
                                        <option value="0">Free Users</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="reset" class="btn btn-primary btn-lg updateData">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tile-body">
                        <div class="table-responsive">
                            <table class="w-100 table table-hover table-bordered" id="userTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Subscription</th>
                                        {{-- <th>Created On</th> --}}
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
    </div>
</div>

@endsection


@section('scripts')
    <script>

        var table = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('admin.users-list') }}",
            error: handleAjaxError,
            data: function (d) {
                    d.status = $('#status').val(),
                    d.subscribe = $('#subscribe').val()
                },
            },
            /*dom: 'Blfrtip',
              buttons: [
                   {
                       extend: 'pdf',
                       exportOptions: {
                           columns: [1,2,3] // Column index which needs to export
                       }
                   },
                   {
                       extend: 'csv',
                       exportOptions: {
                           columns: [1,2,3] // Column index which needs to export
                       }
                   },
                   {
                       extend: 'excel',
                       exportOptions: {
                           columns: [1,2,3] // Column index which needs to export
                       }
                   },
              ],*/
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'user_name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'subscription_status'
                },
                // {
                //     data: 'created_at',
                //     searchable: false
                // },
                {
                    data: 'status',
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [],
        });


        $('#status').change(function(){
            table.draw();
        });
        $('#subscribe').change(function(){
            table.draw();
        });

        $('#filter_form').on('reset', function(){
            setTimeout(() => {
                table.draw();
            }, 500);
        })

        /*change user status activate and deactivate*/

        $(document).on('click', '.togbtn', function(event) {

            event.preventDefault();
            var id = $(this).data('id');
            var el = $(this);
            var url = "{{ route('admin.users.status') }}";

            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {

                    if (response.val) {
                        swal("Success", response.success, "success");

                    } else {
                        swal("Success", response.success, "success");
                    }

                    el.prop('checked', response.val);
                }
            });
        });


        $(document).on('click', '.delete_account', function() {
            let id = $(this).data('id');
            swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this account!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = "{{ url('admin/users/delete-account') }}" + '/' + id;
                    }
                });

        });
    </script>
@endsection
