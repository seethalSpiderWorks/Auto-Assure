var token = $('meta[name="csrf-token"]').attr('content');
$('#branchDatatable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": true,
  "bLengthChange": true,
  "info":     false,
  "order": [
            [0, 'desc']
          ],
  "columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,7]
                }
                ],
  "displayLength":25,
  "ajax": 
        {
        "url": url_branchDataTable,
        "type": "post",
        "data": function (data) 
          {
          data._token = token;
          return data;
          }
        }, 
  "AutoWidth": false,
  "columns": 
            [
				{"data": "branch_id", "name": "branch_id"},
				{"data": "branch_name", "name": "branch_name"},
				{"data": "branch_code", "name": "branch_code"},
				{"data": "branch_unq_id", "name": "branch_unq_id"},
				{"data": "branch_mob", "name": "branch_mob"},
				{"data": "branch_email", "name": "branch_email"},
				//{"data": "branch_gst", "name": "branch_gst"},
				//{"data": "branch_logo", "name": "branch_logo"},
				{"data": "name", "name": "name"},
				{"data": "branch_id", "name": "branch_id"}
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    var response = $('#branchDatatable').DataTable().ajax.json();
    var option =response.option.opset_options;
    
    $('td:eq(0)', nRow).html(index).addClass('center');
    $('td:eq(2)', nRow).html(aData.branch_code).addClass('text-center');
  	$('td:eq(3)', nRow).html(aData.branch_unq_id).addClass('text-center');	
	$('td:eq(4)', nRow).html(aData.branch_mob).addClass('text-center');	
    var deleteFunction = "javascript:delete_function('" + aData.branch_id + "')" ;
    var editFunction = "javascript:edit('" + aData.branch_id + "')";
    var viewFunction = "javascript:view('" + aData.branch_id + "')";
    var action = '';
    if(option.indexOf("1") !== -1 || option.indexOf("2") !== -1){
    action += '<a href='+ viewFunction +' title="View"><i class="fa fa-eye " style="color:blue"></i></a>';
    }
    if(option.indexOf("2") !== -1){
    action += '&nbsp <a href='+ editFunction +' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
    }
    if(option.indexOf("3") !== -1){
    action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
    }
    $('td:eq(7)', nRow).html(action).addClass('center');
	//$('td:eq(6)', nRow).addClass('text-center');
    }
  });
function view(id)
  {
  $.ajax({
        type: 'GET',
        data: {'id': id},
        url: url_viewbranch,
        success: function (result) 
          {
          $('#view-modal-body').html(result);
          $('#view_modal').modal('show');
          }
    });
  }
function edit(id)
  {
  $.ajax({
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_editbranch,
          success: function (result) 
            {
				$('.editButton').show();
				$('.saveButton').hide();
				$('#company_i').focus();
				$('#edit_id').val(result.data.branch_id);
				$('#branch_code').val(result.data.branch_code);
				$('#branch_name').val(result.data.branch_name);
				$('#branch_person').val(result.data.branch_person);
				$('#branch_mob').val(result.data.branch_mob);
				$('#branch_lan').val(result.data.branch_lan);
				$('#branch_email').val(result.data.branch_email);
				$('#branch_web').val(result.data.branch_web);
				$('#branch_address').val(result.data.branch_address);
				$('#branch_country').val(result.data.branch_country);
				$('#branch_state').attr('data-state',result.data.branch_state);
				$('#branch_district').attr('data-district',result.data.branch_city);
				//$('#branch_city').attr('data-city',result.data.branch_city);
				$('#branch_country').trigger('change');
				$('#branch_gmb_id').val(result.data.branch_gmb_id);	
				$('#branch_gmb_link').val(result.data.branch_gmb_link);		
				$('#branch_pincode').val(result.data.branch_pincode);				
				$('#branch_whatsapp').val(result.data.branch_whatsapp);
				//$('#branch_long').val(result.data.branch_long);
            
            $.each(result.company,function(i,value){
                var company_id = value.company_id;
                var company_name = value.company_name;
                if(result.data.company_id = company_id)
                {
                    var select = "Selected";
                }
               $('#company_i').html('<option value="'+company_id+'" '+select+'>'+company_name+'</option>');
            });
            
            }
    });
  }
function delete_function(id) 
  {
  $('#delete_entry').modal('show');
  $('#del_id').val(id);
  }
function deleteEntry()
  {
  var id = $('#del_id').val();
  $.ajax({
        type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_deletebranch,
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
					}
            $('#delete_entry').modal('hide');
            $('#branchDatatable').DataTable().ajax.reload();
            document.getElementById("createForm").reset();

            } 
          else 
            {
            }
          }
      });
  }
function clearForm()
  {
  $('#createForm').parsley().reset();
  $('#createForm')[0].reset();
  }