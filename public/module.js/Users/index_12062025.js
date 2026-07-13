var token = $('meta[name="csrf-token"]').attr('content');

$('#usersDataTable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": true,
  "bLengthChange": true,
  "autoWidth":false,
   "info":     false,
  "order": [
            [1, 'desc']
          ],
  "columnDefs": [
                {
                "className": "text-center",
                "targets": [0,8]
                }
                ],
  "displayLength":25,
  "ajax":
        {
        "url": url_userDataTable,
        "type": "post",
        "data": function (data)
          {
          data._token = token;
          return data;
          }
        },
  "columns":
            [
            {"data": "user_id", "name": "user_id"},
            {"data": "user_id", "name": "user_id"},
			{"data": "username", "name": "username"},	
            {"data": "name", "name": "name"},
            //{"data": "lname", "name": "lname"},
            {"data": "mobile", "name": "mobile"},
            {"data": "user_email", "name": "user_email"},
            {"data": "privilege_name", "name": "privilege_name"},
            {"data": "addedbyname", "name": "addedbyname"},
            {"data": "branch_name", "name": "branch_name"}
            ],
  "fnCreatedRow": function (nRow, aData, iDataIndex)
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    var response = $('#usersDataTable').DataTable().ajax.json();
    var option   = response.option.opset_options;
    
    $('td:eq(0)', nRow).html(index).addClass('center');
    var deleteFunction = "javascript:delete_function('" + aData.userid + "')" ;
    var editFunction = "javascript:edit('" + aData.userid + "')";
    var viewFunction = "javascript:view('" + aData.userid + "')";
	var resetFunction = "javascript:resetpswd('" + aData.userid + "')"; 
    var change_password = "javascript:change_password('" + aData.userid + "')";
    var action = '';
	 
	 if(option.indexOf("2") !== -1){
		 		action += '&nbsp <a href='+ resetFunction +' title="View"><i class="fas fa-key" style="color:#7b8190"></i></a>';
	 }
    if(option.indexOf("1") !== -1 ){
    	action += '&nbsp <a href="users/profile/'+aData.userid+'" title="View"><i class="fa fa-eye " style="color:blue"></i></a>';
	}
	if(option.indexOf("2") !== -1){
    	action += '&nbsp <a href="#" onclick='+ editFunction +' title="Edit"><i class="far fa-edit " style="color:green"></i></a>';
    }
    if(option.indexOf("3") !== -1){
    	action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
	}
    // action += '&nbsp <a href='+change_password+' title="Change Password" ><i class="fa fa-key " style="color:red"></i></a>';
    $('td:eq(8)', nRow).html(action).addClass('center');
    }
});
  
function view(id)
{ 
    $.ajax({
        type: 'GET',
        data: {'id': id},
        url: url_userview,
	    //url : "users/profile1",
        success: function (result)
          {
			  //location.href = "users/profile1";
          $('#view-modal-body').html(result);
          $('#view_modal').modal('show');
          }
    });
}

function resetpswd(id)
{ 
    $.ajax({
        type: 'get',
        data: {'id': id},
        url: "users",
	    //url : "users/profile1",
        success: function (result)
          {
			  //location.href = "users/profile1";
        	//	$("#user_mypass_current").val('');
        	//	$("#user_mypass_new").val('');
        	//	$("#user_mypass_conf").val('');
        
       		 	$('#change_mypass_user').modal('show');
        		$('#user_id_mypasschange').val(id);
          }
    });
}

function edit(id)
{
        $('#parsley-id-5').html(" "); 
        $('#parsley-id-7').html(" "); 
        $('#parsley-id-9').html(" "); 
        $('#parsley-id-11').html(" "); 
        $('#parsley-id-15').html(" "); 
        $('#parsley-id-25').html(" "); 
        $('#parsley-id-27').html(" "); 
        $('#parsley-id-29').html(" "); 
        $.ajax({
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_edituser,
          success: function (result)
            {
            var result = result.data;
            $('.editButton').show();
            $('.saveButton').hide();
            $('#edit_id').val(result.id);
            $('#user_fname').val(result.name);
            $('#user_lname').val(result.lname);
            $('#user_email').val(result.user_email);
            $('#user_mobile').val(result.mobile);
            $('#user_date').val(result.user_dob);
            $('#user_qual').val(result.user_qualification);
            $('#user_address').val(result.user_perm_address);
            $('#user_comment').val(result.user_comment);
            $('#user_privilage').val(result.previlage);
            $('#user_company').val(result.user_company);
            $('#user_branch').val(result.user_branch);
			
			if(result.user_multiple_branch == 1){
				$('#multiple_branch').prop('checked',true);
				$('#multiple_branch').change();
				var arrayData = result.user_multiple_branch_id.split(',');
				
				$('#user_branch1').val(arrayData).trigger('change');
			}else{
				$('#user_branch').val(result.user_branch);
			}
			 
            $('#user_designation').val(result.user_designation);

               var user_type = result.user_type;
               // console.log(result.edit.user_department);
            if(user_type==10)
            {
                $('#dep1').show();
                $('#ddd').hide();
                $('#user_department').val(result.user_department);
            }
            else{
                $('#dep1').hide();
                $('#ddd').show();
            }
        }
    });
}
  
function delete_function(id)
{
  	$('#delete_entry').modal('show');
  	$('#del_id').val(id);
	$('#createForm')[0].reset();
	$('.editButton').hide();
    $('.saveButton').show();
}

function deleteEntry()
{
  var id = $('#del_id').val();
  $.ajax({
        type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_deleteuser,
        success: function (result)
          {
          if (result.status == 1)
            {
				Command: toastr["success"](result.msg)
					toastr.options = {
						  "heading": "data.heading",
						  "text": "data.msg",
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
					};
            $('#delete_entry').modal('hide');
            $('#usersDataTable').DataTable().ajax.reload();
            }
            else {}
          }
      });
  }
function clearForm()
  {
  $('#createForm').parsley().reset();
  $('#createForm')[0].reset();
  }
