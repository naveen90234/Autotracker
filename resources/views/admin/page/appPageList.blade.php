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
                                            <h5>CMS Page List</h5>
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
                                                <table id="appPage-list" class="w-100 table table-striped table-bordered nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Title</th>
                                                            <th>Slug</th>
                                                            <th width="150px">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    {{-- <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Slug</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot> --}}
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



    <div id="editAppPageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editAppPageModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAppPageModalTitle">Edit CMS Page</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('appPage.edit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="page_title">Title</label>
                            <input type="text" class="form-control" name="page_title" required id="page_title">
                        </div>
                        <div class="form-group">
                            <label for="page_slug">Slug</label>
                            <input type="text" class="form-control" name="page_slug" required id="page_slug" readonly>
                        </div>
                        <div class="form-group">
                            <label for="page_content">Content</label>
                            <textarea class="form-control" name="page_content" required id="page_content"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" value="" name="page_id" id="edit_appPage_id">
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
    <!-- Ckeditor js -->
    <script src="{{ asset('assets/common/js/plugins/summernote/summernote.min.js') }}"></script>
    <!-- Ckeditor js -->
    <script>
        $('#page_content').summernote({
            height: 300,
        });
    </script>
    <script>
        var table = $('#appPage-list').DataTable({
            processing: true,
            serverSide: true,
            // pageLength: 2,
            ajax: {
                url: "{{ route('appPage.list') }}",
                error: handleAjaxError,
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        function getAppPageDetail(appPage_id) {
            jQuery.ajax({
                url: "{{ route('appPage.detail') }}",
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": appPage_id,
                },
                success: function(result) {
                    $('#editAppPageModal #page_title').val(result.title);
                    $('#editAppPageModal #edit_appPage_id').val(result.id);
                    $('#editAppPageModal #page_slug').val(result.slug);
                    $('#page_content').summernote('code', result.description);
                    $('#editAppPageModal').modal();

                }
            });
        }
    </script>
@endpush('scripts')
