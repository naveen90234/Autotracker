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
<link rel="stylesheet" href="{{ asset('assets/common/js/plugins/summernote/summernote.min.css') }}">

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4><b>{{$title}}</b></h4>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-8">
                            <form method="post" action="{{route('admin.update-email-templates',['id'=>$data->id])}}" enctype="multipart/form-data" id="submit-form">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="title">Template Title</label>
                                        <input type="text" class="form-control" name="title" id="title" value="{{$data->title}}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="slug">Slug</label>
                                        <input type="text" class="form-control" name="slug" required id="slug" readonly value="{{$data->slug}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" class="form-control" name="subject" required id="subject" value="{{$data->subject}}">
                                </div>

                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea class="form-control" name="content" id="content">{{$data->content}}</textarea>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" value="" name="template_id" id="edit_template_id">
                                    <button type="submit" class="btn btn-primary m-auto d-block rounded-0" style="width:200px">Update Template</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <h6 style="color:red;"><b>Note:*</b></h6>
                            <h6>Use following keywords in content</h6>
                            <table class="table table-hover ">
                              <thead>
                                <tr>
                                  <th scope="col"><b>For</b></th>
                                  <th scope="col"><b>Keyword</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>User Name</td>
                                  <td>&#123;&#123;USER_NAME&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>User Email</td>
                                  <td>&#123;&#123;USER_EMAIL&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>App Name</td>
                                  <td>&#123;&#123;APP_NAME&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>App Email</td>
                                  <td>&#123;&#123;APP_EMAIL&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>OTP</td>
                                  <td>&#123;&#123;OTP&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>Message</td>
                                  <td>&#123;&#123;MESSAGE&#125;&#125;</td>
                                </tr>
                                <tr>
                                  <td>URL</td>
                                  <td>&#123;&#123;URL&#125;&#125;</td>
                                </tr>
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
    <script src="{{ asset('assets/common/js/plugins/summernote/summernote.min.js') }}"></script>

    <script>
        $('#content').summernote({
            height:200,
        });

        $(function() {
            $('#submit-form').ajaxForm({
                beforeSubmit: function() {
                    $(".error").remove();
                    disable("#submit-btn", true);
                    $("body").LoadingOverlay("show");
                },
                error: function(err) {
                    $("body").LoadingOverlay("hide");
                    handleError(err);
                    disable("#submit-btn", false);
                },

                success: function(response) {
                    disable("#submit-btn", false);
                    $("body").LoadingOverlay("hide");
                    if (response.status == 'true') {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "success",
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                window.location.href = '{{ route("admin.email-templates") }}';
                            }
                        });
                    } else {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "error",
                            dangerMode: true,
                        }).then(function(isConfirm) {
                            if (isConfirm) {}
                        });
                    }
                }

            });
        });
    </script>
@endsection
