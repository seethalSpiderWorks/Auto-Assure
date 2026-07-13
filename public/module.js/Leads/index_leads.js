var rows_selected = new Array();
var dTable = $('#lead_table').DataTable({
    //$('#lead_table').DataTable(
    //{
    "responsive": false,
    "scrollX": true,
    "serverSide": true,
    "ordering": true,
    "searching": true,
    "bLengthChange": true,
    "bAutoWidth": false,
    "info":     false,
    "order": [
            [1, 'desc']
          ],
    "columnDefs": [
                {
                "className": "text-center",
                "targets": [0,12],
				// "type": "natural",	
				 //"sortable": true
                }
                ],
    "displayLength":25,
    "ajax": 
        {
		    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "url": url_lead_table,
            "type": "post",
            "data": function (data) 
            {
			    var token = '';
			    data._token = token;
		        data.filter_staff = $('#filter_staff').val();
		        data.assign_status = $('#assign_filter').val() || '';
		        return data;
            }
        }, 
	
    "AutoWidth": false,
    "columns":
            [
				{"data": "lead_id", "name": "lead_id"},
				{"data": "lead_id", "name": "lead_id"},
				{"data": "lead_date", "name": "lead_date"},
				{"data": "lead_unq_id", "name": "lead_unq_id"},
				{"data": "breg_fname", "name": "breg_fname"},
				{"data": "breg_mob", "name": "breg_mob"},
				{"data": "lead_pack_name", "name": "lead_pack_name"},
				{"data": "lead_form_type", "name": "lead_form_type"},
				{"data": "source_name", "name": "source_name"},
				{"data": "breg_id", "name": "breg_id"},                                        // Assign To
				{"data": "inspection_scheduled_at", "name": "inspection_scheduled_at", "orderable": false}, // Scheduled Date
				{"data": "breg_id", "name": "breg_id"},                                        // Status
				{"data": "breg_id", "name": "breg_id"},                                        // Action
            ],
    "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
		var info   = this.dataTable().api().page.info();
		var page   = info.page;
		var length = info.length;
		
		var response = $('#lead_table').DataTable().ajax.json();
		var option =response.option.opset_options;
		
		var check = '<input name="select_all" value="1" type="checkbox" style="">';
		$('td:eq(0)', nRow).html(check).addClass('center');
 
		var index  = (page * length + (iDataIndex + 1));
		$('td:eq(1)', nRow).html(index).addClass('text-center');
 
		var dateMod = new Date(aData.lead_date);
		dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
		
		$('td:eq(2)', nRow).html(dateMod).addClass('center');

		// Name → link to the lead detail view
		var viewUrl = public_path + '/leads/view/' + aData.lead_id;
		$('td:eq(4)', nRow).html('<a href="'+viewUrl+'" class="lead-name-link" title="View lead details">'+ (aData.breg_fname || '-') +'</a>');

		var assign = '<select class="change_staff form-control form-select select2" data-enq_id='+ aData.lead_id +'  style="border: 1px solid #08406330;color: #101010;cursor:pointer; padding-top:2px;padding-bottom:2px;width:110px"><option value="">Select Staff</option>' ;
        $.ajax({
               type: 'POST',
                data:{'_token':token,'branch_id':$('#branch_name_data').val()},
                url: url_staffData,
                success: function (data) 
				{
					var name = aData.lead_assigned_users;  //alert(name);
					 
                    $.each(data.result,function(index, item) 
					{			
						var staffFullname = item.name + ' '+item.lname ;
						if(name == item.staff_id) 
						{ 
							 var sel_status = "selected" ; 
						}
						else
						{ 
							var sel_status = ''; 
						}
						 
                        assign += '<option value='+item.staff_id+ '  '+sel_status+'>'+staffFullname+'</option>';
                    });
                    assign +='</select>';
                   
					$('td:eq(9)', nRow).html(assign).addClass('center');
				}
		});	 
		
		if(aData.lead_form_type == 1)	
		{
			 var lead_form = "Book Inspection";
		}
		else if(aData.lead_form_type == 2)	
		{
			 var lead_form = "Buy Assured";
		}
		else
		{
			var lead_form = aData.lead_form_type;
		}
		
		$('td:eq(7)', nRow).html(lead_form).addClass('center');

		// Scheduled Date (inspection scheduled_at) — shown after "Assign To".
		var schedHtml = '';
		if(aData.inspection_scheduled_at)
		{
			var sd = new Date(String(aData.inspection_scheduled_at).replace(' ', 'T'));
			if(!isNaN(sd.getTime()))
			{
				schedHtml = sd.getDate().toString().padStart(2, '0') + '-' + (sd.getMonth()+1).toString().padStart(2, '0') + '-' + sd.getFullYear();
			}
		}
		if(schedHtml === '') { schedHtml = '<span style="color:#98a2b3">—</span>'; }
		$('td:eq(10)', nRow).html(schedHtml).addClass('center');

		if(aData.followup_date != null)	
		{
			var dateMod_f =new Date(aData.followup_date);
			var dateMod_foll  = dateMod_f.getDate() + '-' + (dateMod_f.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod_f.getFullYear() ;
		}
		else
		{
			var dateMod_foll  = '' ;
		}
		 
		var badges = '';   
        if(aData.lead_assigned_status == 'Assign')
        {
            badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Assigned</span>';
        }
		else if(aData.lead_assigned_status == 'Reassign')
        {
            badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: auto !important;">Reassigned</span>';
        }
		else if(aData.lead_assigned_status == 'Followup')
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Followup</span>';
        }	
		else if(aData.lead_assigned_status == 'Inspection')
        {
            badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Inspection</span>';
        }
		else if(aData.lead_assigned_status == 'Rejected')
        {
            badges = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Rejected</span>';
        }
		else if(aData.lead_assigned_status == 'Closed')
        {
            badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Closed</span>';
        }
		else if(aData.lead_assigned_status == 'Plan / Shedule')
		{
			badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span>';
		}
		else if(aData.lead_assigned_status == 'Reshedule')
		{
			badges = '<span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span>';
		}
		else if(aData.lead_assigned_status == 'Inspection Completed')  
        {
            badges = '<span class="btn bg-primary" style="padding: 2px 6px; font-size:9.5px; line-height:1.2; min-width:50px; cursor:auto !important; color:#f5f6f8; white-space:nowrap;">Inspection Completed</span>';
        }
		else if(aData.lead_assigned_status == 'Approved')
		{
			badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span>';
		}
        else
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> New</span>';
        }
		
		var status;
	
		if(aData.lead_assigned_status)
        {
			status = aData.lead_assigned_users;
		}
		else
		{
			status = '0';
		}  
 
    	var deleteFunction = "javascript:confirmDelete('"+aData.lead_id+"','"+aData.lead_unq_id+"');" ;
    	var editFunction   = "javascript:confirmEdit_new('" + aData.lead_id + "')";
		//var editFunction = "javascript:edit_lead('" + aData.lead_id + "')";
    	var viewFunction   = "javascript:viewFollowuppopup('"+aData.lead_id+"','"+status+"');";
    	var action = '';
 
		var url = window.location.href;

		// Read-only lead detail page
		action += '<a title="View Details" href="'+public_path+'/leads/view/'+aData.lead_id+'" ><i class="far fa-eye" style="color:#00263D"></i></a>&nbsp;';

		// Edit lead (always available)
		action += '<a title="Edit" href="'+public_path+'/leads?id='+aData.lead_id+'" target="_blank"><i class="far fa-edit" style="color:#04B084"></i></a>&nbsp;';

		if(option.indexOf("1") !== -1 )
		{
			//action += '<a href='+ viewFunction +' title="View"><i class="fa fa-plus" style="color:#007bff"></i></a>';
			// data-target="#view_modal"
			action += '<a title="Followup" href="'+public_path+'/leads/followup?id='+aData.lead_id+'"  ><i class="fa fa-plus" style="color:#007bff"></i> </a>';
		}
		if(option.indexOf("3") !== -1)
		{
			action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="far fa-trash-alt" style="color:red"></i></a>';
		}
		
		$('td:eq(11)', nRow).html(badges).addClass('center');
    	$('td:eq(12)', nRow).html(action).addClass('center');
    }
});
  
// Staff filter no longer auto-applies on change — it is applied together with
// the other filters by the "Apply Filters" button (see applyFilters() in the
// view). This removes the stale-session race that showed the wrong results.

 // followup
function viewFollowuppopup(id,status)
{
    $.ajax({
            type: 'GET',
            url: url_view_followup,
            dataType:'html',
            data:{'id':id},
            success: function (data) 
			{
				// $("#followup_table").DataTable().ajax.reload();
				$("#modal_followup").modal('show');   
				$("#followup_table").DataTable().ajax.reload();   
				$("#modal_lead_id").val(id);
				$("#modal_lead_ids").val(id);
				$("#view_modal_body").html(data);
				$("#statususer").val(status);

				if(status == 0 )
				{
					jQuery.ajax({
						url : 'leads/followup_type_assign/',
						type : "GET",
						dataType : "json",
						success:function(data)
						{
							console.log(data);
							jQuery('select[name="follow_status"]').empty();
									
							$('select[name="follow_status"]').append('<option value="">-- Select --</option>');
							jQuery.each(data, function(key,value)
							{
								$('follow_status').empty();
								$('select[name="follow_status"]').append('<option name="'+ value +'" value="'+ key +'">'+ value +'</option>');
							});
						}
					});
				}	
				else if(status != "")
				{
					jQuery.ajax({
								url : 'leads/followup_type_reassign/',
								type : "GET",
								dataType : "json",
								success:function(data)
								{
									console.log(data);
									jQuery('select[name="follow_status"]').empty();
									$('select[name="follow_status"]').append('<option value="">-- Select --</option>');
									jQuery.each(data, function(key,value)
									{
										$('follow_status').empty();
										$('select[name="follow_status"]').append('<option name="'+ value +'" value="'+ key +'">'+ value +'</option>');
									});
								}
							});
				}
    		} ,
    		error:function(data)
    		{
    		    alert(data.responseText);
    		}
    });
     
    $.ajax({
            type: 'GET',
            url: url_view_followup_table,
            dataType:'html',
            data:{'id':id},
            success: function (data) 
			{
                //$("#view_modal_body_follow").empty();
                $("#view_modal_body_follow").html(data);
            }
        });
}
 
function edit_lead(id)
{
	$.ajax({
        type: 'GET',
        data: {'id': id},
        url: url_edit_lead,
        success: function (result) 
          {
         // $('#view-modal-body').html(result);
          //$('#view_modal').modal('show');
          }
    });
}

function confirmEdit_new(id)
{
	$.ajax({
            url: "leads/getleadsdata/"+id,
            method: "GET",
			dataType:'JSON',
            success: function(result)
            {
				$('#add').hide();
				$('#reset').hide();
				$('#edit').show();
				
				$("#lead_id").val(result.data.lead_id);
				$("#breg_id").val(result.data.breg_id);
				$("#first_name").val(result.data.breg_fname);
				$("#email").val(result.data.breg_email);
				$("#branch_state").val(result.data.breg_state).trigger('change.select2');
				$("#company_city").val(result.data.breg_district).trigger('change.select2');
				$("#centre").val(result.data.lead_branch_id).trigger('change.select2');
				$("#course").val(result.data.lead_course_id).trigger('change.select2');
				$("#source").val(result.data.lead_source).trigger('change.select2');
				$("#message").val(result.data.breg_message);
				$('#mobile').val(result.data.breg_mob);
				$("#phonecode").val(result.data.breg_mob_code).trigger('change.select2');
				//$('#country').val(result.data.breg_country).trigger('change.select2');				
				flag_function(result.flag,result.data.breg_mob_code);	
			}
		});
}

function confirmDelete(id,lead_id)
{
    $('#delete_modal').modal('show');
	//$('#del_id').val(id);
    $("#del_lead_id").val(id);
    $("#delete_lead").text(lead_id);
}
  
function deleteLead(id)
{
    $.ajax({
       type: 'GET',
       url: "leads/delete",
	   dataType:'JSON',
	   data:{'id':id},
        success: function (data) {
           
			Command: toastr["success"](data.text)
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
    	$('#lead_table').DataTable().ajax.reload();	
        }
    }) 
}

function filtering(from_date,to_date,source,type)
{       
	date = from_date;
	if(from_date=='unset')
	{
		var date="";
	}
	
    $.ajax({
       type: 'GET',
       url: "leads/filter",
	   dataType:'html',
	   data:{'from_date':date,'to_date':to_date,'source':source,'type':type},
        success: function (data) {

		  if(from_date=='unset')
		  {
		      window.location.reload();
		  }

		  if(type==1)
		  {
			  $("#filter_div1").show();
			  if($("#filter_div1").find('select').val()=='')
			  {
			  $("#filter_div1").find('select').val('').trigger('change');
			  }
			  $("#filter_div3").hide();
			  
			  $("#filter_div3").find('select').val('').trigger('change');
			  $("#filter_div2").show();
			  if($("#filter_div2").find('select').val()=='')
			  {
			  $("#filter_div2").find('select').val('').trigger('change');
			  }
			  
			  $("#filter_div5").hide();
             if($("#filter_div5").find('select').val()=='')
			  {
			  $("#filter_div5").find('select').val('').trigger('change');
			  }
			  $("#filter_div6").hide();
    		  if($("#filter_div6").find('select').val()=='')
    		  {
    			$("#filter_div6").find('select').val('').trigger('change');
    		  }
		  }
		  else if(type==-1)
		  {
			  $("#filter_div1").show();
			  if($("#filter_div1").find('select').val()=='')
			  {
			  $("#filter_div1").find('select').val('').trigger('change');
			  }
			  $("#filter_div3").show();
			  if($("#filter_div3").find('select').val()=='')
			  {
			  $("#filter_div3").find('select').val('').trigger('change');
			  }
			  if($("#filter_div2").find('select').val()=='')
			  {
			  $("#filter_div2").find('select').val('').trigger('change');
			  }
			  $("#filter_div5").hide();
             if($("#filter_div5").find('select').val()=='')
			  {
			  $("#filter_div5").find('select').val('').trigger('change');
			  }
		  }
		  else
		  {
		      $("#filter_div1").hide();
			  if($("#filter_div1").find('select').val()=='')
			  {
			  $("#filter_div1").find('select').val('').trigger('change');
			  }
			  $("#filter_div3").hide();
			  if($("#filter_div3").find('select').val()=='')
			  {
			  $("#filter_div3").find('select').val('').trigger('change');
			  }
			  $("#filter_div2").hide();
			  if($("#filter_div2").find('select').val()=='')
			  {
			  $("#filter_div2").find('select').val('').trigger('change');
			  }
			   $("#filter_div4").hide();
			  if($("#filter_div4").find('select').val()=='')
			  {
			  $("#filter_div4").find('select').val('').trigger('change');
			  }
			  $("#filter_div5").hide();
    		  if($("#filter_div5").find('select').val()=='')
    		  {
    			$("#filter_div5").find('select').val('').trigger('change');
    		  }
    		
			  if(type==2)
			  {
			      $("#filter_div6").show();
    		  if($("#filter_div6").find('select').val()=='')
    		  {
    			$("#filter_div6").find('select').val('').trigger('change');
    		  }
    		
			  }
			 
			  else 
    		  {
        		  $("#filter_div1").hide();
        		  $("#filter_div2").hide();
        		  $("#filter_div3").hide();
        		  $("#filter_div4").hide();
                  $("#filter_div5").hide();
                  $("#filter_div6").hide();
                  $("#filter_div7").hide();
                  $("#filter_div8").hide();
    		  }
		  }
		  
		    $("#form_comtents").html(data);
			$("#filter_source").val(source);
			$("#lead_status").val(type);
			$("#to_date").val(to_date);
			$("#from_date").val(from_date);
			$('#lead_table').DataTable().ajax.reload();
        }
    })
  } 
  
//************** Staff assign *******************/ 
$('body').on('change','.change_staff',function(e){
    var token = $('meta[name="csrf-token"]').attr('content');
    var staff_value = $(this).val();
    var enq_id = $(this).data('enq_id');
    if(staff_value == '')
    {
        $.toast({
                    heading: 'Warning',
                    text:'Select Staff',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 1500,
                    stack: 6
                });
     }else{
       $.ajax({
       type: 'POST',
        data: {'_token':token, 'staff_id': staff_value,'enq_id':enq_id},
        url: url_assignenquery,
        success: function (data) {
         // if (data.status == 1) {
			  
			  Command: toastr["success"](data.text)
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
				$('#lead_table').DataTable().ajax.reload(); 
             }
        });
    }
	  
});

function clearForm()
{
    $('#lead_form').parsley().reset();
    $('#lead_form')[0].reset();
}
