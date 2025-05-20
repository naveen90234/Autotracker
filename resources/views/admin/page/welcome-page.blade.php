@extends('admin.layouts.app')

<!-- data tables css -->
<link rel="stylesheet" href="{{ asset('assets/common/js/plugins/summernote/summernote.min.css') }}">

@section('content')

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

                                    <div class="card">
                                        <div class="card-header table-card-header">
                                            <h5>Welcome Page</h5>
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
                                                <table id="welcomePage-list"
                                                    class="w-100 table table-striped table-bordered nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Section Description</th>
                                                            <th width="150px">Action</th>
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



    <div id="editWelcomePageModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="editWelcomePageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWelcomePageModalTitle">Edit Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('welcomePageUpdate') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="page_content">Section Description</label>
                            <textarea class="form-control" name="page_content" required id="page_content"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" value="" name="page_id" id="edit_welcomePage_id">
                            <button type="submit" class="btn btn-primary m-auto d-block rounded-0"
                                style="width:200px">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection('content')

@push('scripts')
    <script>
        var table = $('#welcomePage-list').DataTable({
            processing: true,
            serverSide: true,
            // pageLength: 2,
            ajax: {
                url: "{{ route('welcomePage') }}",
                error: handleAjaxError,
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'value',
                    name: 'value'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', '.edit-content-btn', function() {
            let welcome_section_id = $(this).data('id');
            let welcome_desc = $(this).data('value');
            $('#editWelcomePageModal #edit_welcomePage_id').val(welcome_section_id);
            $('#page_content').val(welcome_desc);
            $('#editWelcomePageModal').modal();
        })
    </script>
@endpush('scripts')
