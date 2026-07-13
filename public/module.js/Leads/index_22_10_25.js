var rows_selected = new Array();
var dTable = $('#lead_table').DataTable({
//$('#lead_table').DataTable(
  //{
	"responsive": true,
	"serverSide": true,
	"ordering": true,
	"searching": true,
	"bLengthChange": true,
	"bAutoWidth": false,
	"info":     false,
	"order": [
            [0, 'desc']
          ],
	"columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,10],
				 "type": "natural",	
				 "sortable": true
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
			return data;
          }
        }, 
	
	"AutoWidth": false,
	"columns": 
            [
				{"data": "lead_id", "name": "lead_id"},
				{"data": "breg_id", "name": "breg_id"},
				{"data": "lead_date", "name": "lead_date"},
				{"data": "breg_fname", "name": "breg_fname"},
				{"data": "breg_mob", "name": "breg_mob"},
				{"data": "lead_pack_name", "name": "lead_pack_name"},
				{"data": "lead_form_type", "name": "lead_form_type"},
				{"data": "source_name", "name": "source_name"},
				{"data": "breg_id", "name": "breg_id"},
				{"data": "breg_id", "name": "breg_id"},
				{"data": "breg_id", "name": "breg_id"},
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
 		
		var dateMod =new Date(aData.lead_date);
		dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
		
		$('td:eq(2)', nRow).html(dateMod).addClass('center');
		
		var assign = '<select class="change_staff form-control form-select select2" data-enq_id='+ aData.lead_id +'  style="border: 1px solid #08406330;color: #101010;cursor:pointer; padding-top:2px;padding-bottom:2px;width:110px"><option value="">Select Staff</option>' ;
            $.ajax({
               type: 'POST',
                data:{'_token':token,'branch_id':$('#branch_name_data').val()},
                url: url_staffData,
                success: function (data) 
				{
 					var name = aData.lead_assigned_users;
		 
                    $.each(data.result,function(index, item) 
					{
                        var staffFullname = item.name + ' '+item.lname ;
						if(name ==item.staff_id) 
						{ 
							var sel_status ="selected" ; 
						}
						else
						{ 
							var sel_status = ''; 
						}
                        assign += '<option value='+item.staff_id+ '  '+sel_status+'>'+staffFullname+'</option>';
                    });
                    assign +='</select>';
                   
					$('td:eq(8)', nRow).html(assign).addClass('center');
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
		
		$('td:eq(6)', nRow).html(lead_form).addClass('center');
 		
		var badges = '';   
		//alert(aData.lead_assigned_status);
        if(aData.lead_assigned_status == 'Assign')
        {
            badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Assigned</span>';
        }
		else if(aData.lead_assigned_status == 'Reassign')
        {
            badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor:auto !important;">Reassigned</span>';
        }
		else if(aData.lead_assigned_status == 'Followup')
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Followup</span>';
        }	
		else if(aData.lead_assigned_status == 'Registered')
        {
            badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Registered</span>';
        }
		else if(aData.lead_assigned_status == 'Plan / Shedule')
		{ 
			badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span>';
		}
		else if(aData.lead_assigned_status == 'Reshedule')
		{
			badges = '<span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span>';
		}
		else if(aData.lead_assigned_status == 'Inspection')
        {
            badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Inspection</span>';
        }
		else if(aData.lead_assigned_status == 'Inspection Completed')  
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> Inspection Completed </span>';
        }
		else if(aData.lead_assigned_status == 'Approved')
		{
			badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span>';
		}
		else if(aData.lead_assigned_status == 'Rejected')
        {
            badges = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Rejected</span>';
        }
		else if(aData.lead_assigned_status == 'Closed')
        {
            badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto!important;">Closed</span>';
        }
        else
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor:auto!important;color:#f5f6f8;"> New</span>';
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
    	var editFunction = "javascript:confirmEdit_new('" + aData.lead_id + "')";
    	var viewFunction = "javascript:viewFollowuppopup('"+aData.lead_id+"','"+status+"');";
    	var action = '';

		if(option.indexOf("1") !== -1 )
		{
			//action += '<a href='+ viewFunction +' title="View"><i class="fa fa-plus" style="color:#007bff"></i></a>';
			//onclick='+ viewFunction +' data-target="#view_modal"
			action += '<a title="View" href="'+public_path+'/leads/followup?id='+aData.lead_id+'"  ><i class="fa fa-plus" style="color:#007bff"></i> </a>';
		}
		if(option.indexOf("2") !== -1)
		{
			action += '&nbsp <a href="#" onclick='+ editFunction +' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
		}
		if(option.indexOf("3") !== -1)
		{
			action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="far fa-trash-alt" style="color:red"></i></a>';
		}

		//var pageurl = document.URL; // alert(pageurl);
		
		$('td:eq(9)', nRow).html(badges).addClass('center');
    	$('td:eq(10)', nRow).html(action).addClass('center');
    }
});
  

$('#filter_staff').change(function(e) {
    $('#lead_table').DataTable().ajax.reload(); 
});

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
                // console.log(data.follow);
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

    /*if(status ==0 )
    {
    $('follow_status').empty();
    $('#follow_status').html('<option value="">Select</option><option value="Assigned">Assign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option value="Registered"> Registered </option><option >Maybe</option><option >Pickup and Reject</option><option >Less Likely</option><option >Closed</option><option >Callback</option><option >More Likely</option><option >Withdrawn</option>')
    }
    else if(status !="" )
    {
    $('follow_status').empty();
    $('#follow_status').html('<option value="">Select</option><option  value="Reassigned">Reassign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option value="Registered"> Registered </option><option >Maybe</option><option >Pickup and Reject</option><option >Less Likely</option><option >Closed</option><option >Callback</option><option >More Likely</option><option >Withdrawn</option>')
    }*/

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
 
function confirmEdit_new(id)
{
	$.ajax({
            url: "leads/getleadsdata/"+id,
            method: "GET",
			dataType:'JSON',
            success: function(result)
            {
				//console.log(result.lead_id);
				$('#add').hide();
				$('#reset').hide();
				$('#edit').show();

				$("#lead_id").val(result.data.lead_id);
				$("#breg_id").val(result.data.breg_id);
				$("#first_name").val(result.data.breg_fname);
				$("#fname_ar").val(result.data.breg_fname_ar);
				$("#email").val(result.data.breg_email);
				$("#branch_state").val(result.data.breg_state).trigger('change.select2');
				$("#centre").val(result.data.lead_branch_id).trigger('change.select2');
				
				$("#source").val(result.data.lead_source).trigger('change.select2');
				$("#message").val(result.data.breg_message);
				$("#breg_place").val(result.data.breg_place);
  				
				$('#mobile').val(result.data.breg_mob);
				$("#phonecode").val(result.data.breg_mob_code).trigger('change.select2');
				flag_function(result.flag,result.data.breg_mob_code);
				
				$("#formtype").val(result.data.lead_form_type).trigger('change');
				
				//$("#make").val(result.data.lead_make).trigger('change.select2');
				var makeData = result.data.lead_make.split(',');
				$('#make').val(makeData).trigger('change');
				
				//$("#model").val(result.data.lead_model).trigger('change.select2');
				var modelData = result.data.lead_model.split(',');
				$('#model').val(modelData).trigger('change');
				
				$("#year").val(result.data.lead_year).trigger('change.select2');
				$('#color').val(result.data.lead_color); 
				$('#color_ar').val(result.data.lead_color_ar); 
				$('#sellername').val(result.data.lead_seller_name); 
				$('#sellername_ar').val(result.data.lead_seller_name_ar); 
				$('#sellermobile').val(result.data.lead_seller_mobile); 
				$('#location').val(result.data.lead_location); 
				//alert(result.data.lead_package);
				$('#package').val(result.data.lead_pack_name_id).trigger('change'); 
				$('#payment').val(result.data.lead_mode_pay).trigger('change'); 
				
				$("#yearfrom").val(result.data.lead_year_from).trigger('change.select2');
				$("#yearto").val(result.data.lead_year_to).trigger('change.select2');
				
				$('#budget').val(result.data.lead_budget);
				$('#yourmobile').val(result.data.lead_your_mobile);
				$('#additionaldet').val(result.data.lead_add_details);
				
 				//var arrayData = result.data.lead_software.split(',');
				//$('#software').val(arrayData).trigger('change');
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
    date=from_date;
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
    		  $("#filter_div7").hide();
    		  if($("#filter_div7").find('select').val()=='')
    		  {
    			$("#filter_div7").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div8").hide();
    		  if($("#filter_div8").find('select').val()=='')
    		  {
    			$("#filter_div8").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div9").hide();
    		  if($("#filter_div9").find('select').val()=='')
    		  {
    			$("#filter_div9").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div10").hide();
    		  if($("#filter_div10").find('select').val()=='')
    		  {
    			$("#filter_div10").find('select').val('').trigger('change');
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
			  $("#filter_div6").hide();
    		  if($("#filter_div6").find('select').val()=='')
    		  {
    			$("#filter_div6").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div7").hide();
    		  if($("#filter_div7").find('select').val()=='')
    		  {
    			$("#filter_div7").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div8").hide();
    		  if($("#filter_div8").find('select').val()=='')
    		  {
    			$("#filter_div8").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div9").hide();
    		  if($("#filter_div9").find('select').val()=='')
    		  {
    			$("#filter_div9").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div10").hide();
    		  if($("#filter_div10").find('select').val()=='')
    		  {
    			$("#filter_div10").find('select').val('').trigger('change');
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
    		  $("#filter_div6").hide();
    		  if($("#filter_div6").find('select').val()=='')
    		  {
    			$("#filter_div6").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div7").hide();
    		  if($("#filter_div7").find('select').val()=='')
    		  {
    			$("#filter_div7").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div8").hide();
    		  if($("#filter_div8").find('select').val()=='')
    		  {
    			$("#filter_div8").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div9").hide();
    		  if($("#filter_div9").find('select').val()=='')
    		  {
    			$("#filter_div9").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div10").hide();
    		  if($("#filter_div10").find('select').val()=='')
    		  {
    			$("#filter_div10").find('select').val('').trigger('change');
    		  }
    		  
			  if(type==2)
			  {
			      $("#filter_div6").show();
    		  if($("#filter_div6").find('select').val()=='')
    		  {
    			$("#filter_div6").find('select').val('').trigger('change');
    		  }
    		  $("#filter_div7").show();
    		  if($("#filter_div7").find('select').val()=='')
    		  {
    			$("#filter_div7").find('select').val('').trigger('change');
    		  }
			  }
			  else if(type==3)
			  {
			      $("#filter_div8").show();
    		  if($("#filter_div8").find('select').val()=='')
    		  {
    			$("#filter_div8").find('select').val('').trigger('change');
    		  }
			  }
			  else if(type==7)
			  {
			      $("#filter_div9").show();
    		  if($("#filter_div9").find('select').val()=='')
    		  {
    			$("#filter_div9").find('select').val('').trigger('change');
    		  }
			  }
			 else if(type==8)
			  {
			      $("#filter_div10").show();
    		  if($("#filter_div10").find('select').val()=='')
    		  {
    			$("#filter_div10").find('select').val('').trigger('change');
    		  }
			  }
			  else if(type==6)
			  {
    			  $("#filter_div5").show();
    		      if($("#filter_div5").find('select').val()=='')
    			  {
    			  $("#filter_div5").find('select').val('').trigger('change');
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
// 			$("#filter_tracking").val(track);
			$("#to_date").val(to_date);
			$("#from_date").val(from_date);
			$('#lead_table').DataTable().ajax.reload();
        }
    })
  } 
  
<!---------------------- Staff assign -------------------------> 
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
    }
    else{
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
