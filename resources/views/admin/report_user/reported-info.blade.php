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
                                            <th>User</th>
                                            <th>Reported By</th>
                                            <th>Reported Date</th>
                                            <th>Report Reason</th>
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
                    <h5 class="modal-title" id="descriptionModalLabel">Report Description</h5>
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
        var endPoint = window.location.pathname.split('/').pop();

        $('#reportedUserListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('admin/user/reported-info') }}" + "/" + endPoint,
                error: handleAjaxError,
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'user'
                },
                {
                    data: 'reporter',
                },
                {
                    data: 'created_at',
                    searchable: false
                },
                {
                    data: 'description',
                    searchable: false
                },
            ],
            // order: [
            //     [0, 'desc']
            // ]
        });



        /*change post status activate and deactivate*/
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

        $(document).on('click', '.viewDescriptionBtn', function(event) {
            $("#viewDescriptionBtn .modal-body").html($(this).parent().find('input[type=hidden]').val());
        });
    </script>
@endsection
