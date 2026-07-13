(function($) {
'use strict';
    //users data table
})(jQuery);

function filtering(from_date,to_date,source,staff){

    var token = $('meta[name="csrf-token"]').attr('content');    
        date=from_date;
	 	if(from_date=='unset')
		  {
		      var date="";
		  }
    $.ajax({
       type: 'post',
       url: "leads/filter",
	   data:{'_token':token,'from_date':date,'to_date':to_date,'source':source,'staff':staff},
        success: function (data) 
		{
			$('#lead_table').DataTable().ajax.reload();
			$('#lead_table_all').DataTable().ajax.reload();
		  if(from_date=='unset')
		  {
		      window.location.reload();
		  }

        }
    })
} 
  
function confirmDelete(id,lead_id)
{
    $("#delete_modal").modal('show');
    $("#del_lead_id").val(id);
     $("#delete_lead").text(lead_id);
}

function confirmWhatsapp(id,mob)
{
    $("#whatsapp_modal").modal('show');
    $("#whats_mob").val(mob);
     $("#wati_lead").val(id);
}

function viewLead(id)
{
    $.ajax({
       type: 'GET',
       url: "leads/view_lead",
	   dataType:'HTML',
	   data:{'id':id},
        success: function (data) {
           
            $("#modal_view").modal('show');
            $("#view_modal_body").html(data);
        }
    }) ;
}

function deleteLead(id)
{
    $.ajax({
       type: 'GET',
       url: "leads/delete",
	   dataType:'JSON',
	   data:{'id':id},
        success: function (data) {
            $("#delete_modal").modal('hide');
				
				Command: toastr["success"](data.text)
						toastr.options = {
						  "heading": "data.heading",
						  "text": "data.text",
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
function sendWatiMessage(mob,id)
{
    $.ajax({
       type: 'GET',
       url: "leads/sendWati",
	   dataType:'JSON',
	   data:{'mob':mob,'id':id},
        success: function (data) {
            $("#whatsapp_modal").modal('hide');
             $.toast({
				  heading: data.heading,
				  text: data.text,
				  showHideTransition: 'slide',
				  icon: data.icon,
				  loaderBg: '#f96868',
				  position: 'top-right'
    			}); 
    	$('#lead_table').DataTable().ajax.reload();	
    	
        }
    }) 
}
  
$("#modal_followup").on('hide.bs.modal', function(){
    $('#modal_followup').find('input, select, textarea').val('');
  });
  
function confirmEdit(id)
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
    		  //$("#formtypeid").val(result.data.lead_form_type_id);
    		  //$("#lead_form_type_old").val(result.data.lead_form_type_id);
    		  //$("#lead_form_type_check").val(result.data.lead_form_type_check);
    		  $("#first_name").val(result.data.breg_fname);
    		  $("#last_name").val(result.data.breg_lname);
    		  $("#email").val(result.data.breg_email);
		  	
    		  $("#dob").val(result.data.breg_date_birth).trigger('change.select2');
    		  $("#qualification").val(result.data.qualification).trigger('change.select2');
    		  $("#course").val(result.data.course);
    		  $("#message").val(result.data.message);
    		  $("#fathername").val(result.data.breg_father_name);
    		  $("#fatheroccupation").val(result.data.breg_father_occu);
    		  $("#address").val(result.data.breg_address);
              $("#college").val(result.data.breg_college);

    		   //$("#mobile").val(result.breg_mob);
    		   //iti__flag iti__us
    		   //console.log(result.country_code);
    		   //var countrycode = '';
    		    //$('.iti__flag').addClass('iti__us');
    		  //  $('.iti__flag').removeClass('iti__in');
    		   //$('.iti__flag').addClass('iti__'+result.country_code);
    		   //$('.iti__selected-flag').siblings('.iti__flag').addClass('iti__'+result.country_code);
    		  // parents('..iti__selected-flag:first').find('.iti__flag').addClass('iti__'+result.country_code);
		  
    		  $( "div .iti__selected-flag" ).find('.iti__flag').removeClass('iti__in');
    		  $( "div .iti__selected-flag" ).find('.iti__flag').addClass('iti__'+result.country_code);
    		  $( "div .iti__selected-flag" ).attr("title", result.data.breg_mobilecountrydata);
    		  
    		  $('#mobile').val(result.data.breg_mob);
              $("#phonecode").val(result.data.breg_mob_code).trigger('change.select2');
              $('#country').val(result.data.breg_country).trigger('change.select2');
    		  //$("#country").val(result.breg_country).trigger('change');
    		 // $("#form_type").val(result.data.lead_form_type_id).trigger('change');
    		  $("#source").val(result.data.lead_source).trigger('change.select2');
    		  flag_function(result.flag,result.data.breg_mob_code);
		  
			}
	});
}

var rows_selected = new Array();
$("#assign_btn").click(function()
{
    //alert("assign_btn");
         var staff = $("#assign_lead_staff").val();
         
         $("#error_assigns").html("");
         $("#error_assigns_lead").html("")
         if(staff.length==0)
         {
             $("#error_assigns").html("Please Select Staff");
             return false;
         }
         
        if(rows_selected.length==0)
        {
            $("#error_assigns_lead").html("Please select at least one lead");
            return false;
        }
        $.ajax({
                   type: 'POST',
                   url: "{{url('/leads/assign_leads')}}",
            	   dataType:'json',
            	   data:{'leads':rows_selected,'user_id':staff,'_token':"{{csrf_token()}}"},
                    success: function (data) {
                         $.toast({
				  heading: data.heading,
				  text: data.text,
				  showHideTransition: 'slide',
				  icon: data.icon,
				  loaderBg: '#f96868',
				  position: 'top-right'
    			}); 
    			rows_selected=[];
    // 			$("#lead_table").DataTable().ajax.reload();
    			 $("#lead_table").DataTable().ajax.reload( null,false );
    			$("#assign_lead_staff").val("").trigger('change');
                    }
                }); 
})

function flag_function(flag,code,type='')
{
    $('#phonecode').val(code);
    var flg = flag.toString().toLowerCase();
    var flag_img = "https://auto-assure.com/crm/public/img/flag/"+flg+".jpg" ;
    var image = '<img src="'+flag_img+'" style="width: 25px;padding-right:5px;">';

    $('#cimage').html(image);
    $('#code').html('+'+code);
    if(type=='drop')
    {
        $('#csflag').trigger('click');
    }
}