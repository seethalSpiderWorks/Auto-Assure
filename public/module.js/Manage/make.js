
var token = $('meta[name="csrf-token"]').attr('content');

	$('#make_table').DataTable({
        "responsive": true,
        "serverSide": true,
        "ordering": true,
        "searching": true,
        "bLengthChange": true,
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
        "displayLength":25,
        "ajax": {
            "url": url_DivisionDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
					{"data":'make_id', 'name': 'make_id'},
                    {"data":'make_name', "name": 'make_name'},
                    //{"data":'make_id', "name": 'make_id'},
				    {"data":'name', "name": 'name'},
				    {"data":'make_id', "name": 'make_id'},
                    {"data":'make_id', "name": 'make_id'}
                
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
            var viewFunction = "javascript:viewEnquery('" + aData.make_id + "')" ;
            var viewFunction_app = "javascript:viewEnquery_app('" + aData.make_id + "')" ;
            var deleteFunction = "javascript:branchDelete('" + aData.make_id + "')" ;
            var url_branchEdit = "javascript:editCurrency('" + aData.make_id + "')";
            var url_active = "javascript:status('" + aData.make_id + "')";
            var action = '';
            var action1 = '';
		 
            var status='';
            if (aData.make_publish_status == 1) 
    		{
    		    status += '<div class="form-check form-switch form-switch-lg"   onclick='+url_active+'><a href='+url_active+' ><input type="checkbox" class="form-check-input" id="customSwitchsizelg" checked></a></div>'
    		} 
    		else 
    		{
                status += '<div class="form-check form-switch form-switch-lg" onclick='+url_active+'><a href='+url_active+' ><input type="checkbox" class="form-check-input" id="customSwitchsizelg"></a></div>'  
            }
            
			action += '<a href="#" onclick='+ url_branchEdit +' title="Edit"><i class="far fa-edit " style="color:green"></i></a>';
            action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            
            $('td:eq(3)', nRow).html(status).addClass('center');
			$('td:eq(4)', nRow).html(action).addClass('center');
	}
    
});


function editCurrency(make_id)
{  
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'make_id': make_id},
        url: url_editdivision,
        success: function (result) 
        {  
          $('.editButton').show();
          $('.saveButton').hide();
          $('.cancelButton').hide();
		  $('#make_id').val(result.data.make_id)
          $('#make_name').val(result.data.make_name);
        }
    });
 }
  
function clearForm()
{
    $('#createForm').parsley().reset();
    $('#createForm')[0].reset();
}

function branchDelete(make_id) 
{
  $('#delete_entry').modal('show');
  $('#make_id').val(make_id);
    
}
  function deleteEntry()
  {
    var make_id = $('#make_id').val();
    //alert(make_id);
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'make_id': make_id},
        url: url_deletedivision,
        success: function (result) 
		{
            if (result.status == 1) 
			{
				Command: toastr["success"](result.msg)
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
				
				$('#delete_entry').modal('hide');
				$('#make_table').DataTable().ajax.reload();
			} 
			else 
			{
            }
        }
    })
  }


function status(id) 
{
    $.ajax({
        type: 'POST',
        data: { '_token': token, 'id': id },
        url: url_status,
        success: function(result) {
            if (result.status == 1) 
			{
				Command: toastr["success"](result.msg)
					toastr.options = {
						  "heading": "result.msg",
						  "text": "result.msg",
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
                $('#make_table').DataTable().ajax.reload();
            } else {}
        }
    });
}
