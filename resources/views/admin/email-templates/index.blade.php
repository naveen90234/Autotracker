@extends('admin.layouts.app')
@section('content')

 <style>
     div.dt-buttons{
        float: none;
        }
        div#userTable_length {
    display: contents;
}
 </style>
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="card">
                <div class="card-body">
                    <div class="card-title m-2">
                        <h4><b>{{$title}}</b></h4>
                    </div>
                    <hr class="m-2">
                    <div class="tile-body">
                        <div class="table-responsive">
                            <table class="w-100 table table-hover table-striped" id="userTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Created On</th>
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
        $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:"{{ route('admin.email-templates') }}",
                error: handleAjaxError,
            },
            columns: [
                {data: 'DT_RowIndex',searchable: false},
                {data: 'title'},
                {data: 'slug'},
                {data: 'created_at',searchable: false},
                {data: 'action',orderable: false,searchable: false}
            ],
        });
    </script>
@endsection
