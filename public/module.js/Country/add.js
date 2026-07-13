$('#country_name').select2({
    'placeholder': 'Select Country',
    ajax: {
        url: url_countryGet,
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
                        text: item.country_name,
                        slug: item.country_name,
                        id: item.id
                    }
                })
            };
        }
    }
}).change(function () {
    $(this).parsley().validate();
});

$("#equalent_amount" ).on('keyup', function(){    
   var currencyCode = $('#currency_code').val();
   var amount = $("#equalent_amount" ).val();
   $('#amount_text').val('1 Point = '+ amount+' '+currencyCode.toUpperCase())
}); 

function clearForm(){
    $('#createForm').parsley().reset();
     $('#createForm')[0].reset();
     $('#country_name').empty();
}