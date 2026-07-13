 // followup
function viewFollowuppopup(id,status)
{
    //alert(status);
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
				
				
				//alert(status);
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
										//alert(value);
										$('follow_status').empty();
										$('select[name="follow_status"]').append('<option name="'+ value +'" value="'+ key +'">'+ value +'</option>');
										
									});
								}
							});
                      
				}
    /* 
     if(status ==0 )
    {
		//alert(1);
     $('follow_status').empty();
    
    $('#follow_status').html('<option value="">Select</option><option value="Assigned">Assign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option value="Registered"> Registered </option><option >Maybe</option><option >Pickup and Reject</option><option >Less Likely</option><option >Closed</option><option >Callback</option><option >More Likely</option><option >Withdrawn</option>')
        
      
    }
    else if(status !="" )
    {
        $('follow_status').empty();
        
        $('#follow_status').html('<option value="">Select</option><option  value="Reassigned">Reassign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option value="Registered"> Registered </option><option >Maybe</option><option >Pickup and Reject</option><option >Less Likely</option><option >Closed</option><option >Callback</option><option >More Likely</option><option >Withdrawn</option>')
    }
    */

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
                //  alert(1);
                $("#view_modal_body_follow").html(data);
            }

        });
    			
}
	
function changeStatus(status,name)
	{
		//alert(status);
		$(this).val('').trigger('change');
		
		$("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
		$("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
  
            $("#intm").html('');
            $("#inty").html('');
   
		$("#btn_followup_div").attr('class','col-md-3');
		$("#div_follow3").show();
		//$("#commentid").html('*');
		$("#error_followup_remark").show();
          
		//$("#div_follow3").find('textarea').attr('required','required');
		if(status=="1")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			//  $("#div_follow1").find('input').attr('required','required');
			//$("#div_follow4").find('select').attr('required','required');
         
			$("#div_follow3").show();
			$("#commentid").html('');
		}
		else if(status=="2")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").show();
                   
			$("#div_follow3").show();
			$("#commentid").html('');        
		}
		else if(status=="3")
		{
			$("#div_follow1").show();
		}
		else if(status=="4")
		{
			$("#div_follow1").show();                 
		}
		else if(status=="5")
		{
			//$("#div_follow1").show();                    
		}
		else if(status=="6")
		{
			//$("#div_follow1").show();                       
		}
		else if(status=="7")
		{
			$("#div_follow1").show();     
		}
		
					
		else
		{
			$("#div_follow1").hide();
		}
	}  
	


function addFollowUp(id,date,status,remarks,assigned_user,convertstatus)      
 {
	 var name = $('#follow_status option:selected').text();
	 var staff = $("#assign_staff").val();
    
    /* $('#followup_form').parsley().validate();
       if(! $('#followup_form').parsley().isValid())
        {
            return false;
        }*/
	 
	//$("#follow_status").find('select2').attr('required','required');
        $.ajax({
                   type: 'POST',
                   url: url_add_followup,
            	   dataType:'json',
            	   data:{'id':id,'date':date,'status':status,'name':name,'remarks':remarks,'_token':token,'assinged_user':assigned_user,'staff':staff},
            	  
                    success: function (data) 
					{                      
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
						
						$.ajax({
							type: 'GET',
							url: url_view_followup_tables,
							dataType:'html',
							data:{'id':id},
							success: function (data) 
							{
								console.log(id);
                                $("#view_modal_body_follow").html(data);
                            }
                        });      
                    
						$("#followup_form").trigger('reset');
						$('#follow_next_date').val('').trigger('change');
						$('#followup_remark').val('').trigger('change')						
                       // $('#followup_form')[0].reset();
                        $('#assign_staff').val(null).trigger("change");
						$("#lead_table").DataTable().ajax.reload();
					}
                }); 
	             
 }


 
$("#from_date").change(function(){
   
    var fromdate = $("#from_date").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'fromdate': fromdate},
        url: url_setFromDatefilter,
        success: function (result) {
            if(result.status==1)
            {
               //window.location.reload();
            }
            
        }
    });

});

$("#to_date").change(function(){
   
    var to_date = $("#to_date").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'to_date': to_date},
        url: url_setToDatefilter,
        success: function (result) {
            if(result.status==1)
            {
               window.location.reload();
            }
            
        }
    });

});


$("#btn_unsetFilter").click(function(){
    $.ajax({
       type: 'POST',
        data: {'_token':token},
        url: url_unsetFilter,
        success: function (result) {
            if(result.status==1)
            {
            	window.location.reload();
            }
            
        }
    });

});

