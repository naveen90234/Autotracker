@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">

                {{-- <div class="card-header">
                    <div class="mailbox-controls">
                        <h3 class="card-title">{{ $title }}
                        </h3>
                    </div>
                </div> --}}

                <div class="card">
                    <div class="card-body">
                        <div class="tile-body">
                            <div class="table-responsive">
                                <table class="w-100 table table-hover table-bordered" id="contactTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Created On</th>
                                            <th>Message</th>
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
                    <h5 class="modal-title" id="descriptionModalLabel">Contact Message</h5>
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
        $('#contactTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:"{{ route('admin.contact_us') }}",
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
                    searchable: false
                },
                {
                    data: 'message',
                    searchable: false
                },
            ],
        });

        $(document).on('click', '.viewDescriptionBtn', function(event) {
            $("#viewDescriptionBtn .modal-body").html($(this).parent().find('input[type=hidden]').val());
        });


    </script>
@endsection
