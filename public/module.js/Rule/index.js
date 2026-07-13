var token = $('meta[name="csrf-token"]').attr('content');
function LoadResources(url, formId, modalId, dataTable, redirectUrl, validation)
{
    if (validation != false) {
        $('#' + formId).parsley().validate();
        if (!$('#' + formId).parsley().isValid()) {
            return false;
        }
    }
   
    var formData = new FormData($('#' + formId)[0]);
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function (data) {
		   $('#dataarea').empty();		   
		   $('#dataarea').html(data);		   
        }, error: function () {
            $('#' + modalId).modal('hide');
        }
    });
    return false;

}

