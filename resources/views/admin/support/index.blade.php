@extends('admin.layouts.app')
@section('content')
    
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <div class="tile-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="listData">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>User Email</th>
                                            <th>Message</th>
                                            <th>Reply Status</th>
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
    <div class="modal fade" id="sendMailResponse" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="card-primary">  
            <div class="card-header">
               <h3 id="ModalLongTitle" class="card-title">Add Response</h3>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: relative;bottom: 30px;">
             <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="card-body">
              
              <!-- form -->
               <form id="formSubmit" action="" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="email" id="email" > 
                    <input type="hidden" name="support_id" id="id" > 
                    <label class="">Message : </label>

                    <textarea placeholder="Enter Message" class="form-control" name="message" id="message" spellcheck="false"></textarea>
                    <div style="margin-top: 5px"  class="form-group">
                        <span id="message_error" class="text-danger" role="alert" ></span>
                    </div>
                    <input type="submit" class="form-control btn-success" style="background: gray;" id="submit" name="submit" class="btn btn-success btn-submits" value="Reply">
               </form>
             </div>
          </div>
         </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')

<script>

    $(function () {
        var table = $('#listData').DataTable({
          processing: true,
          serverSide: true,
          paging: true,
          autoWidth: false,
          responsive: true,
          ajax: {
            error: handleAjaxError,
          },
           columns: [
              {data: 'DT_RowIndex', searchable: false},
              { data: 'name'},
              { data: 'email', searchable: false},
              { data: 'message', searchable: false},
              { data: 'reply_status', searchable: false},
              { data: 'action', searchable: false}
           ]
        });
    });

    
    $('#message').on('keypress', function () {
      if ($(this).val().length > -1) {
          $("#message_error").text('');
      }
    });


    /* Ajax Delete confirm alert */

    $(document).on('click','.delete-confirm',function(event)
    {
        event.preventDefault();
        var id = $(this).data('id');
        const url = $(this).attr('href');
        var el = this;
        swal({
            title: 'Are you sure you want to delete?',
            text: 'This record will be permanantly deleted!',
            icon: 'warning',
            dangerMode: true,
            buttons: ["Cancel", "Yes!"],
       
        }).then(function(isconfirm)
        {
          var url = "{{route('delete_support_msg','')}}"+"/"+id;
          if(isconfirm==true)
          {
            $.ajax
            ({
                method: 'POST',
                url: url,
                data: {  "_token": "{{ csrf_token() }}", "id": id },
                beforeSend: function(response){
                  $(".error").remove();
                  disable("#submit", true);
                  $("body").LoadingOverlay("show");
                },
                success: function(response)
                {
                  disable("#submit", false);
                  $("body").LoadingOverlay("hide");

                  if(response)
                  {
                    $('#listData').DataTable().ajax.reload()
                        swal({
                            title: response.message,
                            icon: "success",
                            dangerMode: false,
                            timer: 2500,
                            title: 'Deleted!',
                            text: "Support message has been deleted.",
                            showCancelButton: false,
                            showConfirmButton: false
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                // location.reload();
                            }
                        });
                    }
                    else 
                    {
                        alert('Invalid ID.');
                    }
                }
            });
          }
        }); 
    });

  /*....... Send reply mail using ajax ............*/
     
    $(document).on("click","#sendMail",function(e){
        $("#message_error").text('');
        var email = $(this).data('email');
        var id = $(this).data('id');
        var msg = $(this).data('description');
        var v = (msg != 'notFound') ? msg : "";
        if(v != ""){
            $("#submit").val("Replied"); 
            $(':input[type="submit"]').prop('disabled', true);
            $('#message').prop('disabled', true);
        }else{
            $("#submit").val("Reply"); 
            $(':input[type="submit"]').prop('disabled', false);
            $('#message').prop('disabled', false);
        }
        $("#message").val(v); 
        $("#email").val(email); 
        $("#id").val(id); 
        $('.alert-danger').html("");
        $("#sendMailResponse").modal('show');
    });
    

    $(document).on("click","#submit",function(e)
    {
      e.preventDefault();
      var formdata = new FormData($('#formSubmit ')[0]);
      $.ajax({
            type:'POST',
            url: "{{ route('add_response') }}",
            data: formdata,
            cache:false,
            contentType: false,
            processData: false,
            beforeSend: function(response){
              $(".error").remove();
              disable("#submit", true);
              $("body").LoadingOverlay("show");
            },
            success: function(response)
            {
              disable("#submit", false);
              $("body").LoadingOverlay("hide");

              $('#listData').DataTable().ajax.reload() 
              if(response.status == true)
              {
                $("#sendMailResponse").modal('hide');
                swal({
                    title: response.message,
                    icon: "success",
                    dangerMode: false,
                    timer: 2500,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        location.reload();
                    }
                });
              }else{
                console.log(response.error);
                for (var key in response.error) {
                  $("#message_error").html(response.error[key]);
                }
              }
            }
        });
    });
 
</script>

@endsection