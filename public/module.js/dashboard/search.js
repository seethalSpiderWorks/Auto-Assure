
var token = $('meta[name="csrf-token"]').attr('content');	
	$('#student_name').on('keyup', function()
    {	
		//alert($(this).val());
		var thisInput = $(this).val();  //alert(thisInput.length);
		if(thisInput.length)
		{
             //alert(thisInput);
			$.ajax({
                        type:"post",
                        url: url_get_customer_data,
                        data:{'_token': token,'q':thisInput},
		
                        success:function(data)
                        { 
						 console.log(data);
							var sN = [];   
							for (var i = 0; i < data.length; i++) 
							{
								var breg_id = data[i]['breg_id'];
								//console.log(data);
								var test = data[i]['breg_fname']+' - '+'+'+data[i]['breg_mob_code']+' '+data[i]['breg_mob']+' - '+data[i]['breg_email']; 
								sN[i] = (test).toString();
								
								
								$('.input-drop').show();
								var editFunction = "javascript:search_input('" + breg_id + "','" + thisInput + "')";
								$('#input_search').html('<li><a onclick='+ editFunction +'>'+ sN[i] +'</a></li>');
							}
							
							
                        }

                    });
        }
		else
		{
			$('.input-drop').hide();
        }
		
	})	
	

function search_input(breg_id,thisInput)
{ 
	 //alert(breg_id);
	//var search_value = $(this).val();
	var search_value = thisInput;
	 $.ajax({
                url: url_get_customer_data,
                type: 'post',
                dataType: "json",
                data: {
                    q: thisInput,  
                    flag: 'get_single',
                    _token: token,
                },
                success: function(data) 
                {
                  console.log(data);
                    
					 //location.href = "https://crm.caddcentrekerala.com/myleads";
					 //getProfile(reg_id,stud_id);
                      
                      
                }
            });
}
<!------------------------------------------------------------->


/********
var token = $('meta[name="csrf-token"]').attr('content');
$(document).on('focus', 'input[data-action=input_field_customers_name_search]', function()
{

    var thisInput = $(this);
    autoCompleteForCustomerSearch(thisInput);
    
    $(thisInput).autocomplete({
       
        source: function(request, response) 
        {
            $.ajax({
                url: url_get_customer_data,
                type: 'post',
                dataType: "json",
                data: {
                    q: request.term,
                    _token: token,
                },
                success: function(data) 
                {
					
                    //console.log(data);
                    var sN = [];
                    for (var i = 0; i < data.length; i++) 
                    {
						$('.input-drop').show();
					   
					   var test = data[i]['breg_fname']+' - '+'+'+data[i]['breg_mob_code']+' '+data[i]['breg_mob']+' - '+data[i]['breg_email'];   //alert(test);
						
					     var breg_id = data[i]['breg_id'];
						 var editFunction = "javascript:search_input('" + breg_id + "')";
						
						$('#input_search').append('<li><a onclick='+ editFunction +'>'+ test +'</a></li>');
						
						//$('#input_search').append('<li><a onclick="search_input(breg_id)">'+ test +'</a></li>');
						
						/*  sN[i] = (test).toString(); */
					
/********						
                    }
                    response(sN);
                }
            });
        },
        select: function(event, ui) 
        {
            $(thisInput).val(ui.item.value);
            // var a = $(thisInput).val(ui.item.value);
            // alert(a);
          
            $.ajax({
                url: url_get_customer_data,
                type: 'post',
                dataType: "json",
                data: {
                    q: ui.item.value,
                    flag: 'get_single',
                    _token: token,
                },
                success: function(data) 
                {
                //   console.log(data);
                     var reg_id = data.cdata.breg_id;
                     var stud_id = data.cdata.student_id;
                    // alert(stud_id);
					 var name = data.cdata.breg_fname;
                     //console.log(data);
                     $('#student_name').val(name);
					 $('#reg_id').val(reg_id);
					 $('#stud_id').val()
					 
					 getProfile(reg_id,stud_id);
                      
                      
                }
            });
            return false;
        }
    });
});  

********/




function autoCompleteForCustomerSearch(thisInput) 
{
    
    $(thisInput).autocomplete({
        source: function(request, response) 
        {
            $.ajax({
                url: url_get_customer_data,
                type: 'post',
                dataType: "json",
                data: {
                    q: request.term,
                    _token: token,
                },
                success: function(data) 
                {
                    response(data);
                }
            });
        },
        select: function(event, ui) 
        {
     
        }
    });
}
