/*
 * function createOrUpdateWithFile
 * 
 * send data to server for create or update  
 * 
 * @param url, formId, modalId  , dataTable , redirectUrl
 * @return null
 * 
 */
function createOrUpdate(url, formId, modalId, dataTable, redirectUrl, validation) {
    if (validation != false) {
        $('#' + formId).parsley().validate();
        if (!$('#' + formId).parsley().isValid()) {
            return false;
        }
    }
    // $('#').DataTable().ajax.reload();
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
        success: function(data) {
            console.log(data);
            if (data.status == 1) {
              $('#' + dataTable).DataTable().ajax.reload();
              if( modalId == 'update-acctype'){
                $('.updatesButton').hide();
                $('.savesButton').show();
                $('.cancelsButton').show();
              }
              console.log('status 1')
                $.toast({
                    heading: data.heading,
                    text: data.msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 1500,
                    stack: 6
                });
                if (modalId == 'update-modal') {
                    $('#' + formId)[0].reset();
                    $('.select2').val(null).trigger("change");
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
                    if (dataTable == '') {
                        window.location.href = redirectUrl;
                    }
                    $('#' + dataTable).DataTable().ajax.reload();
                    $('.updatesButton').hide();
                    $('.savesButton').show();
                    $('.cancelsButton').show();
                } else if (modalId == 'course-modal') {
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
                        window.location.href = redirectUrl;
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


            } else if (data.status == 0) {
                $.toast({
                    heading: 'Warning',
                    text: data.msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 1500
                });
            }


            $('.editButton').hide();
            $('.saveButton').show();
            $('.cancelButton').show();
            if (redirectUrl != '') {
                window.location.href = redirectUrl;
            }


        },
        error: function() {
            $('#' + modalId).modal('hide');
        }
    });
    return false;

}

function clearForm() {
    $('#createForm')[0].reset();
    $('#createForm').parsley().reset();
    $('.select2').val(null).trigger("change");
}
$("span .selection").css("width", "308px");





function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa-plus-square-o fa-minus-square-o');
}
$('.panel-group').on('hidden.bs.collapse', toggleIcon);
$('.panel-group').on('shown.bs.collapse', toggleIcon);