var token = $('meta[name="csrf-token"]').attr('content');

$('#privilege_name').select2({
    'placeholder': 'Select Privilege',
    'width': '93%',
    ajax: {
        url: url_privilegeGet,
        dataType: 'json',
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page
            };
        },
        processResults: function (data) {
            return {
                results: $.map(data.items, function (item) {
                    return {
                        text: item.privilege_name,
                        slug: item.privilege_name,
                        id: item.id
                    }
                })
            };
        }
    }
}).change(function () {
    $(this).parsley().validate();
});

function menuPrivilege(){
    var privilege = $('#privilege_name').val();
    if(privilege != null){
        $.ajax({
       type: 'POST',
        data: {'_token':token, 'privilege_id': privilege},
        url: url_getmenuprivilege,
        success: function (data) {
         $('.set_menu_privilege').html(data.html);
         $('#privilege_id').val(privilege);
         
         $('.checkboxLi').each(function (index, value) {
        var parentPermission = $(value).attr('id');
        var selectedPermission = 0;
        $('#' + parentPermission + ' .childP').each(function (indexChild, valueChild) {
            selectedPermission = selectedPermission + parseInt($(valueChild).children('input.permission[type=checkbox]:checked').length);
        });
        if ($('#' + parentPermission + ' .childP').length == selectedPermission) {
            $('input.parentP[type=checkbox]').trigger('click');
        }
    });
    $('.set_menu_privilege').removeClass('ui-widget-content');
  $('.set_menu_privilege').css('color','#333333');
          $('#privillege_set').show();
             }
    });
    }else{
        $('#createForm').parsley().validate();
    }
    
   
}