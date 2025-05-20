@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">

                <div class="card">
                    <div class="card-body">
                        <div class="tile-body">
                            <div class="table-responsive">
                                <table class="w-100 table table-hover table-bordered" id="reportedUserListTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Request Date</th>
                                            <th>Reason</th>
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

    <!-- Modal -->
    <div class="modal fade" id="viewDescriptionBtn" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ....
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $('#reportedUserListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:"{{ route('admin.account-deletion-requests') }}",
                error: handleAjaxError,
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'email',
                },
                {
                    data: 'created_at',
                },
                {
                    data: 'description',
                    searchable: false
                },
                {
                    data: 'action',
                    searchable: false
                },
            ],
            // order: [
            //     [0, 'desc']
            // ]
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
                        window.location.href = "{{ url('admin/delete/user-account') }}" + '/' + id;
                    }
                });

        });


        $(document).on('click', '.viewDescriptionBtn', function(event) {
            $("#viewDescriptionBtn .modal-body").html($(this).parent().find('input[type=hidden]').val());
        });
    </script>
@endsection
