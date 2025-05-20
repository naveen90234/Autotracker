function Alert(text,type=true){
    var actionTextColor = '#219864';
    if(type==false){
		$('#turn-up-error').html('');
                  swal({
                    title: text,
                    icon: "error",
                    dangerMode: true,
                }).then(function(isConfirm) {
                      if (isConfirm) {
                         //window.location.href = '{{route('admin.make_index')}}';
                      }
                });
        //actionTextColor = '#C04522';
    }else{
		$('#turn-up-error').html('');
                swal({
                    title: text,
                    icon: "success",
                    dangerMode: false,
                }).then(function(isConfirm) {
                      if (isConfirm) {
                         //window.location.href = '{{route('admin.make_index')}}';
                      }
			});
	}
   // Snackbar.show({text: text,showAction:true,pos:'bottom-center',actionTextColor:actionTextColor,duration:30000});
}
function handleError(error){
    if(error.status === 422){
      var response = JSON.parse(error.responseText);
      $.each(response.errors, function (k, v) {
		  $("body #"+k).after("<div class='text-danger error'>"+v+"</div>");
			$("body #"+k).focus();

      });
    }else{
        Alert(error.statusText,false);
    }
}


function MealHandleError(error){
    if(error.status === 422){
      var response = JSON.parse(error.responseText);
      $.each(response.errors, function (k, v) {

		  $("body #"+k).after("<div class='text-danger error'>"+v+"</div>");
			$("body #"+k).focus();
		  if(k == 'images.0'){
			 //$("body #images_0").after("<div class='text-danger error'>Meal image field is required and must be a file of type: jpeg, png, jpg</div>");
			 $("body #images_0").after("<div class='text-danger error'>"+v+"</div>");
			$("body #images_0").focus();
		  }



      });
    }else{
        Alert(error.statusText,false);
    }
}

function beforeSubmit(button){
    $("#"+button).attr('disabled',true);
}

function disable(btn,type){
    if(type==true){
        $("body "+btn).attr('disabled',true);
        $("body #licon").html('<i class="fa fa-spinner fa-spin"></i> ');
    }else{
        $("body  "+btn).removeAttr('disabled');
        $("body #licon").html('');
    }
}

function enable(btn,text){
    $("body "+btn).removeAttr('disabled');
    // $(btn).html(text);
    $("body #licon").html('');
}

function convertToSlug(Text) {
    return Text
        .toLowerCase()
        .replace(/ /g, '-')
        .replace(/[^\w-]+/g, '');
}

function convertToName(Text) {
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-');
}

function changeStatus(id)
{
    $.ajax({
        url: $("#status_"+id).attr('data-link'),
        type: 'POST',
        data:{id:id},
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend:function(){
            $("#status_"+id).html('<i class="fa fa-spinner fa-spin"></i>');
        },
        error:function(error){
            Alert(error.statusText,false);
            $("#status_"+id).html(response);
        },
        success: function (response) {
            $("#status_"+id).html(response);
        }
    });
}

function confirm_delete(id){
    if(id!="" && confirm("Are you sure?")){
        $.ajax({
            url: $("#delete_"+id).attr("data-link"),
            type: 'POST',
            data:{id:id},
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend:function(){
                $("#delete_"+id+ " i").removeClass('fa-trash');
                $("#delete_"+id+ " i").addClass('fa-spinner fa-spin');
                $("#delete_"+id).addClass('disabled');
            },
            success: function (response) {
                if(response.status=='true'){
                    $("#delete_"+id).parents("tr").remove();
                    Alert(response.message,true);
                }else{
                    Alert(response.message,false);
                    $("#delete_"+id+ " i").addClass('fa-trash');
                    $("#delete_"+id+ " i").removeClass('fa-spinner fa-spin');
                    $("#delete_"+id).removeClass('disabled');
                }
            },error:function(error){
                Alert(error.statusText,false);
                $("#delete_"+id+ " i").addClass('fa-trash');
                $("#delete_"+id+ " i").removeClass('fa-spinner fa-spin');
                $("#delete_"+id).removeClass('disabled');
            }
        });
    }
}

function toggleSidebar(link){
     $.ajax({
        url: link,
        type: 'GET',
        success: function (response) {
            if(response.status=='false'){
                Alert(response.message,false);
            }
        },error:function(error){
            Alert(error.statusText,false);
        }
    });
}
