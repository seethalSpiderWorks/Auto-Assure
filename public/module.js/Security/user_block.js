var token = $('meta[name="csrf-token"]').attr('content');
$('#datatable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": false,
  "bLengthChange": false,
  "info":     false,
  "order": [
            [0, 'desc']
          ],
  "columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,6]
                }
                ],
  "displayLength":25,
  "ajax": 
        {
        "url": url_dataTable,
        "type": "post",
        "data": function (data) 
          {
			   data.user_id = $('#user_id').val();
               data.user_name = $('#user_name').val();
               data.status = $('#status').val();
			  //alert($('#status').val());
          	   data._token = token;
          	   return data;
          }
        }, 
  "AutoWidth": false,
  "columns": 
            [
				{"data": "id", "name": "id"},
				{"data": "user_id", "name": "user_id"},
				{"data": "name", "name": "name"},
				{"data": "mobile", "name": "mobile"},
				{"data": "user_email", "name": "user_email"},
				{"data": "privilege_name", "name": "privilege_name"},
				{"data": "id", "name": "id"}
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    $('td:eq(0)', nRow).html(index).addClass('text-center');
	$('td:eq(1)', nRow).html(aData.user_id).addClass('text-center');
    //$('td:eq(3)', nRow).html(aData.mobile).addClass('text-center');
		
    var url_block = "javascript:userStatusChange('" + aData.id + "','" + aData.name + "',2)";
	var url_unblock = "javascript:userStatusChange('" + aData.id + "','" + aData.name + "',0)";
	//var url_userlock = 'javascript:userStatusChange("' + aData.id + '","' + aData.name + '","'+aData.status+'")';
		
    var action = '';
  	if(aData.status == 0)
	{
    	action += '<a href='+ url_block +'  title="block"><i class=" fa fa-unlock" style="color:green"></i></a>';
	}
	else if(aData.status == 2)
	{
		action += '<a href='+ url_unblock +'  title="unblock"><i class=" fa fa-lock" style="color:red"></i></a>';
	}


    $('td:eq(6)', nRow).html(action).addClass('center');
    }
});


function userStatusChange(id,name,status)
{  
	if(status == 0)
	{
		$('#blktxt').html('Are you sure, You want to Unblock user '+ name + ' ?');
        $('#blktxt').css('color','green');
	}
	else if(status == 2)
	{
		$('#blktxt').html('Are you sure, You want to Block user '+ name + ' ?');
        $('#blktxt').css('color','red');
	}
	$('#user_id').val(id);
    $('#status').val(status);
    $('#unblock_user').modal('show');
} 


function confirmBlock()
{
	var id = $('#user_id').val();
	var status = $('#status').val();
	 $.ajax({
		   type: 'POST',
			data: {'_token':token, 'id': id ,'status':status},
			url: url_unblockuser,
			success: function (result) {
			
					Command: toastr["success"](result.msg)
						toastr.options = {
						  "heading": "result.heading",
						  "text": "result.msg",
						  "icon": "success",
						  "closeButton": true,
						  "debug": false,
						  "newestOnTop": false,
						  "progressBar": false,
						  "positionClass": "toast-top-right",
						  "preventDuplicates": false,
						  "onclick": null,
						  "showDuration": 300,
						  "hideDuration": 1000,
						  "timeOut": 5000,
						  "extendedTimeOut": 1000,
						  "showEasing": "swing",
						  "hideEasing": "linear",
						  "showMethod": "fadeIn",
						  "hideMethod": "fadeOut"
						}	
			 $('#unblock_user').modal('hide');
		     $('#datatable').DataTable().ajax.reload();
         }
    });
}

function searchUser()
{
  	$('#datatable').DataTable().ajax.reload();
}

function clearForm()
{
   	$('#user_id').val('');
   	$('#user_name').val('');
 	$("#status").val("all").trigger("change");
	$('#datatable').DataTable().ajax.reload();
 }