/*
 * function createOrUpdateWithFile
 * 
 * send data to server for create or update  
 * 
 * @param url, formId, modalId  , dataTable , redirectUrl
 * @return null
 * 
 */
function createOrUpdate(url, formId, modalId, dataTable, redirectUrl, validation) 
{
    if (validation != false) 
        {
			if( ($('#'+formId+' .select2').length) > 0 )
			{
				//alert($('#'+formId+' .select2').length);
				var result = true;
				$('#'+formId+' .select2').each(function(i, obj) {
					  if($(this).prop('required')){
						  
							if( $(this).val() == '' || $(this).val() == '0')
							{
								$(this).parent().find('.highlight').html('This value is required');
								$(this).parent().find('.highlight').attr('style','color:#f46a6a; font-size:12px;float:right;');
								$(this).addClass('parsley-error');
								result= false;
							}
						  else
						  {
							  $(this).parent().find('.highlight').html('');
						  }
						} 
				});
				
				if(!result)
					return result;
			}
        $('#' + formId).parsley().validate();
        if (!$('#' + formId).parsley().isValid()) 
            {
            return false;
            }
        }

        if( dataTable == 'bulkDataTable' ) {
            $("#excelUploadSpinner").show();
        }
        if( dataTable == 'productsDataTable' ) {
            $('#productUploadSpinner').show();
        }
    // $('#').DataTable().ajax.reload();
		
	$('.saveButton a').addClass('disabled');
		
    var formData = new FormData($('#' + formId)[0]);
    $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            success: function(data) 
                {
                if( dataTable == 'bulkDataTable' ) {
                    $("#excelUploadSpinner").hide();
                }
                if( dataTable == 'productsDataTable' ) {
                    $('#productUploadSpinner').hide();
                }
                console.log(data);
                if (data.status == 1) 
                {
                    if(dataTable != '') 
                    {
                        $('#' + dataTable).DataTable().ajax.reload();
                    }
                    if( modalId == 'update-acctype')
                    {
                        $('.updatesButton').hide();
                        $('.savesButton').show();
                        $('.cancelsButton').show();
                    }
						
                    Command: toastr["success"](data.msg)
						//console.log('status 1')
						toastr.options = {
						 
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
						
                    if (modalId == 'update-modal') 
                    {
                        $('#' + formId)[0].reset();
                        $('.select2').val(null).trigger("change");
                        /* $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();
                        $.ajax({
                                type: 'GET',
                                url: url_courseDivision,
                                data: { id: $("select[name='division_name']").val() },
                            }).done(function(data) 
                                {
                                $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();
                                $("select[name='course_division']")
                                    .append($("<option></option>")
                                    .attr("value", '')
                                    .text('--Select Division--'));
                                $.each(data, function(key, value) 
                                    {
                                    $("select[name='course_division']")
                                        .append($("<option></option>")
                                        .attr("value", key)
                                        .text(value));
                                    $("select[name='course_division']").trigger('change');
                                    });
                                }).fail(function(jqXHR, textStatus) 
                                    {
                                    console.log(jqXHR);
                                    });*/
                        if (dataTable == '') 
                        {
                            setTimeout(function () {
                                window.location.href = redirectUrl;
                                }, 3500);
                        }
                        $('#' + dataTable).DataTable().ajax.reload();
                        $('.updatesButton').hide();
                        $('.savesButton').show();
                        $('.cancelsButton').show();
                    } 
                    else if (modalId == 'course-modal') 
                    {
                        $('#' + formId).find('input:text').val('');
                        $('#' + formId).parsley().reset();
                        $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();
                        $.ajax({
                                type: 'GET',
                                url: url_courseDivision,
                                data: { id: $("select[name='division_name']").val() },

                        }).done(function(data) {

                        $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();

                        $("select[name='course_division']")

                            .append($("<option></option>")

                                .attr("value", '')

                                .text('--Select Division--'));

                        $.each(data, function(key, value) {

                            $("select[name='course_division']")

                                .append($("<option></option>")

                                    .attr("value", key)

                                    .text(value));

                            $("select[name='course_division']").trigger('change');

                        });

                    }).fail(function(jqXHR, textStatus) {

                        console.log(jqXHR);

                    });

                } else if (modalId == 'add-enq-modal') {

                    $('#' + dataTable).DataTable().ajax.reload();

                    $('#' + formId)[0].reset();

                    $('.select2').val(null).trigger("change");

                    $('#' + formId).parsley().reset();

                    $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();

                    $.ajax({

                        type: 'GET',

                        url: url_courseDivision,

                        data: { id: $("select[name='division_name']").val() },

                    }).done(function(data) {

                        $.each(data, function(key, value) {



                            $("select[name='course_division']")

                                .append($("<option></option>")

                                    .attr("value", key)

                                    .text(value));

                            $("select[name='course_division']").trigger('change');

                        });

                    }).fail(function(jqXHR, textStatus) {

                        console.log(jqXHR);

                    });

                } else if (modalId == 'add-assign-modal') {

                    $('#' + formId)[0].reset();

                    $('.select2').val(null).trigger("change");

                    $('#' + formId).parsley().reset();

                    $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();

                    $.ajax({

                        type: 'GET',

                        url: url_courseDivision,

                        data: { id: $("select[name='division_name']").val() },

                    }).done(function(data) {

                        $("select[name='course_division']").prop('disabled', false).find('option[value]').remove();

                        $("select[name='course_division']")

                            .append($("<option></option>")

                                .attr("value", '')

                                .text('--Select Division--'));

                        $.each(data, function(key, value) {



                            $("select[name='course_division']")

                                .append($("<option></option>")

                                    .attr("value", key)

                                    .text(value));

                            $("select[name='course_division']").trigger('change');

                        });

                    }).fail(function(jqXHR, textStatus) {

                        console.log(jqXHR);

                    });

                    $('#' + dataTable).DataTable().ajax.reload();

                } else if (modalId == 'add-head-modal') {

                    $('#' + formId)[0].reset();

                    $('.select2').val(null).trigger("change");

                    $('#' + formId).parsley().reset();

                    $('#' + dataTable).DataTable().ajax.reload();

                    $('.savesButton').show();

                    $('.updatesButton').hide();

                } else {

                    if (dataTable == '') {
                       setTimeout(function () {
                            window.location.href = redirectUrl;
                            }, 3500);

                    }

                    if (dataTable == 'assignDataTable') {

                        var div = null;

                        divisionChange(div);

                    }

                    $('#' + dataTable).DataTable().ajax.reload();

                    $('#' + formId)[0].reset();

                    $('.select2').val(null).trigger("change");

                    $('#' + formId).parsley().reset();
                }

				    $('.editButton').hide();
                    $('.saveButton').show();
                    $('.cancelButton').show();
           		   
            } else if (data.status == 0) {
			
				Command: toastr["error"](data.msg)
					toastr.options = {
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
            }
			
			  $('.saveButton .disabled').removeClass('disabled');

            if (redirectUrl != '') {
                setTimeout(function () {
                window.location.href = redirectUrl;
                }, 3500);
            }
        },

        error: function() {

            $('#' + modalId).modal('hide');
            if( dataTable == 'bulkDataTable' ) {
                    $("#excelUploadSpinner").hide();
            }
             if( dataTable == 'productsDataTable' ) {
                $('#productUploadSpinner').hide();
             }
        }
    });

    return false;
}


/****************** insertEntry Start ******************/ 
function insertEntry(url,formId,modalId,dataTable,redirectUrl,validation,next)
{    
    if (validation != false) 
    {
        $('#' + formId).parsley().validate();
        if (!$('#' + formId).parsley().isValid()) 
        {
            return false;
        }
    }
	
	var formData = new FormData($('#' + formId)[0]);
		
    $.ajax({
        type: 'POST',
        data: formData,
        url: url,
		async: true,
        cache: false,
        contentType: false,
		enctype: 'multipart/form-data',
        processData: false,
        success: function (data) 
		{
            if(data.status == 1) 
			{
				if(dataTable != '') 
				{
					$('#' + dataTable).DataTable().ajax.reload();
				}
				
                Command: toastr["success"](data.msg)
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
				
					if (modalId == 'update-modal') 
					{
						$('#' + formId)[0].reset();
                             
						if (dataTable == '') 
						{
							setTimeout(function () {
								window.location.href = redirectUrl;
								}, 2000);
						}
						
						$('#' + dataTable).DataTable().ajax.reload();
                        $('.updatesButton').hide();
						$('.savesButton').show();
						$('.cancelsButton').show();
					} 
					else
					{
						if (dataTable == '') 
						{  
							setTimeout(function () {
								window.location.href = redirectUrl;
							}, 2000);
						}
						if(next != '' && next != 'end')
						{
							$('.edit_id').val(data.id);
							tabToggle(next);
							$('.saveButton').hide();
							$('#gallerysave').show();
							$('html, body').animate({ scrollTop: 0 }, 'fast');
						}
						else if(next == 'end' )
						{
							$('.edit_id').val(data.id);
							$('.saveButton').show();
							$('#gallerysave').hide();
						}
						else
						{
							$('#' + formId)[0].reset();
							$('#' + formId).parsley().reset();
						}
					}
						
				//$('#inspectionReportDataTable').DataTable().ajax.reload();
			} 
			else if (data.status == 0) 
			{
				Command: toastr["success"](data.msg)
					toastr.options = {
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
            }
			
			$('.editButton').hide();
            //$('.saveButton').hide();
            $('.cancelButton').show();

            if (redirectUrl != '') 
			{
                setTimeout(function () 
				{
					window.location.href = redirectUrl;
                }, 2000);
            }
 
        },
		
		error: function() 
		{
            $('#' + modalId).modal('hide');
        }
			 
    })
	return false;
} 
/****************** insertEntry End ******************/


/************************************/
function NewListcreateOrUpdate(url, formId, modalId, dataTable, redirectUrl, validation,next) 
{     
    if (validation != false) 
    {
        $('#' + formId).parsley().validate();
        if (!$('#' + formId).parsley().isValid()) 
        {
            return false;
        }
    }
	
       var array=[];
       $('.chk_prolist').each(function () 
	   {
		var sval = $(this).closest('tr').find('.chk_protitle').val();
      
        if( $(this).is(':checked') )
        {
            console.log(sval+'-on');
           //array.push(sval+'-1');
        }
        else
        {
            // console.log(sval+'-off');
             array.push(sval+'-0');
        }
    });
    
    $('#chk_prolist').val(array);
    
     var formData = new FormData($('#' + formId)[0]);
      $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            success: function(data) 
                {
					if (data.status == 1) 
					{   
                        if(dataTable != '') 
						{
							$('#' + dataTable).DataTable().ajax.reload();
                        }
                        if( modalId == 'update-acctype')
						{
                            $('.updatesButton').hide();
                            $('.savesButton').show();
                            $('.cancelsButton').show();
						}
                      
							Command: toastr["success"](data.msg)
							toastr.options = {
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

                        if (modalId == 'update-modal') 
                            {
                                $('#' + formId)[0].reset();
                             
                                if (dataTable == '') 
                                    {
                                       setTimeout(function () {
                                        window.location.href = redirectUrl;
                                        }, 2000);
                                    }
                                $('#' + dataTable).DataTable().ajax.reload();
                                $('.updatesButton').hide();
                                $('.savesButton').show();
                                $('.cancelsButton').show();
                            } 
                        else{
                            if (dataTable == '') {
                               setTimeout(function () {
                                    window.location.href = redirectUrl;
                                    }, 2000);
                            }
                            if(next != '' && next != 'end')
                            {
                                $('.edit_id').val(data.id);
                                tabToggle(next);
                                $('.saveButton').hide();
                                $('#gallerysave').show();
                                
                            }
                            else if(next == 'end' )
                            {
                                $('.edit_id').val(data.id);
                                $('.saveButton').show();
                                $('#gallerysave').hide();
                            }
                            else
                            {
                                 $('#' + formId)[0].reset();
                                 $('#' + formId).parsley().reset();
                            }
                            
                            $('#' + dataTable).DataTable().ajax.reload();
      
                            $('.select2').val(null).trigger("change");

                        }
            } 
            else if (data.status == 0) 
			{
				Command: toastr["success"](data.msg)
							toastr.options = {
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
            }

            $('.editButton').hide();
            //$('.saveButton').hide();
            $('.cancelButton').show();
             
            if (redirectUrl != '') 
			{
                setTimeout(function () 
				{
					window.location.href = redirectUrl;
                }, 2000);
            }
        },

        error: function() 
		{
            $('#' + modalId).modal('hide');
        }

    });  
	
	// e.preventDefault();
    return false;
    
}
/************************************/

function toggleIcon(e) 
{
    $(e.target)

        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa-plus-square-o fa-minus-square-o');
}

$('.panel-group').on('hidden.bs.collapse', toggleIcon);
$('.panel-group').on('shown.bs.collapse', toggleIcon);


function clearForm() 
{
    $('#createForm')[0].reset();
    $('#createForm').parsley().reset();
    $('.select2').val(null).trigger("change");
    $(".error-span").html('');
}

$("span .selection").css("width", "308px");

function toggleIcon(e) 
{
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa-plus-square-o fa-minus-square-o');
}

$('.panel-group').on('hidden.bs.collapse', toggleIcon);

$('.panel-group').on('shown.bs.collapse', toggleIcon);


$("#username, #userpassword").keyup(function() 
{
	if($('#remember_me').is(':checked')){
	$('#remember_me').click();
	}
});


$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });

$('.select2 ').select2({
  sorter: function(results) {
    var query = $('.select2-search__field').val().toLowerCase();
    return results.sort(function(a, b) {
      return a.text.toLowerCase().indexOf(query) -
        b.text.toLowerCase().indexOf(query);
    });
  }
});
