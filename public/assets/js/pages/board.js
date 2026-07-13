function viewFollowuppopup(id,status)
{
    
    $.ajax({
            type: 'GET',
            url: 'leads/set_lead_session',
            dataType:'html',
            data:{'id':id},
            success: function (data) 
			{
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
									
									jQuery('select[name="follow_status"]').empty();
									
									$('select[name="follow_status"]').append('<option value="">Select Status</option>');
									jQuery.each(data, function(key,value)
									{
										$('follow_status').empty();
										
										$('select[name="follow_status"]').append('<option value="'+ key +'">'+ value +'</option>');
										
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
									$('select[name="follow_status"]').append('<option value="">Select Status</option>');
									jQuery.each(data, function(key,value)
									{
										$('follow_status').empty();
										$('select[name="follow_status"]').append('<option value="'+ key +'">'+ value +'</option>');
										
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
            url: '/leads/set_lead_session_followtable',
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
	
	
	function changeStatus(status)
	{
		//alert(status);
		$(this).val('').trigger('change');
		//$("#follow_status").val('').trigger('change.select2');
		
		//$('#follow_status').val('').trigger('change');
		//$("#follow_status").trigger('change.select2');
		//$('#follow_status').val(null).trigger("change");
		 //$('#follow_status').trigger('click');
		
		$("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
		$("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
		$("#div_follow2").hide();$("#div_follow2").find('select').removeAttr('required');
		$("#div_follow22").hide();$("#div_follow22").find('select').removeAttr('required');
  
            $("#intm").html('');
            $("#inty").html('');
   
		$("#btn_followup_div").attr('class','col-md-3');
		$("#div_follow3").show();
		$("#commentid").html('*');
		$("#error_followup_remark").show();
          
		$("#div_follow3").find('textarea').attr('required','required');
		if(status=="1")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			//  $("#div_follow1").find('input').attr('required','required');
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
			$("#div_follow22").val('');
         
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');
		}
		else if(status=="2")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
			$("#div_follow22").val('');
                   
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');        
		}
		else if(status=="3")
		{
			$("#div_follow1").show();
			$("#div_follow2").hide();
			$("#div_follow22").hide();    
		}
		else if(status=="4")
		{
			$("#div_follow1").show();                 
		}
		else if(status=="5")
		{
			$("#div_follow1").show();                    
		}
		else if(status=="6")
		{
			$("#div_follow1").show();                       
		}
		else if(status=="7")
		{
			$("#div_follow1").show();     
		}
		else if(status=="8")
		{
			$("#div_follow1").show();  
		}
		else if(status=="9")
		{
			$("#div_follow1").show();                 
		}    
		else if(status=="10")
		{        
			$("#div_follow1").show();                  
		}
		else if(status=="11")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');       
		}
		else if(status=="12")
		{        
			$("#div_follow1").show();         
		}   
		else if(status=="Converted")
		{
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');
         
			$("#error_followup_remark").hide();         
		}				
		else
		{
			$("#div_follow1").hide();
        
			$("#div_follow2").hide(); 
			$("#div_follow22").hide(); 
		}
	}  
	