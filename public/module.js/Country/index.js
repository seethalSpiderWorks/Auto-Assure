var token = $('meta[name="csrf-token"]').attr('content');


  $('#countryDataTable').DataTable({
        "responsive": true,
        "serverSide": true,
        "ordering": true,
        "searching": false,
        "bLengthChange": false,
        "info":     false,
        "order": [
            [0, 'desc']
        ],
        "columnDefs": [{
                "targets": [0, 4],
                "orderable": false
            },
            {"className": "text-center", "targets": [0,4]}
        ],
        "displayLength":100,
        "ajax": {
            "url": url_countryDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
            {"data": "point_id", "name": "point_id"},
            {"data": "country_name", "name": "country_name"},
            {"data": "currency_code", "name": "currency_code"},
            {"data": "point", "name": "point"},
           {"data": "point_id", "name": "ipoint_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
           	var deleteFunction = "javascript:currencyDelete('" + aData.point_id + "')" ;
            var url_currencyEdit = "javascript:editCurrency('" + aData.point_id + "')"
            var action = '';
            action = '<a href='+ url_currencyEdit +' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
             action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            $('td:eq(4)', nRow).html(action).addClass('center');
  }
    
});

/*
 * @function editRedirect
 * editRedirect
 * 
 * @param 
 * 
 * 
 * @return 
 * string
 * 
 */
function currencyDelete(id) {

	$('#delete_entry').modal('show');
	$('#currency_id').val(id);
  	
  }
  function deleteEntry(){
  	var id = $('#currency_id').val();
  	$.ajax({
  		 type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_deleteCurrency,
        success: function (result) {
            if (result.status == 1) {
                 $.toast({
                    heading: result.heading,
                    text: result.msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 1500,
                    stack: 6
                });
         $('#delete_entry').modal('hide');
		$('#countryDataTable').DataTable().ajax.reload();
           } else {

            }
        }
  	})
  }
  function editCurrency(id){
    
  $.ajax({
       type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_editCurrency,
        success: function (result) {
          $('.editButton').show();
          $('.saveButton').hide();
          $('.cancelButton').hide();
          $('#currency_code').val(result.data.currency_code);
           $('#currency_id').val(result.data.id);
          $('#equalent_amount').val(result.data.point);
          $('#country_name').empty().append('<option value="' + result.data.country_id + '">' + result.data.country_name + '</option>').val(result.data.country_id).trigger('change');

          var currencyCode = $('#currency_code').val();
          var amount = $("#equalent_amount" ).val();
          $('#amount_text').val('1 Point = '+ amount+' '+currencyCode.toUpperCase())
            }
    });
  }

