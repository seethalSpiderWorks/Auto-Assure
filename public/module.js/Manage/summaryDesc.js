var token = $('meta[name="csrf-token"]').attr('content');

	$('#SummaryDesc_table').DataTable({
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
                "targets": [0,4],
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
					{"data":'sum_desc_id', "name": "sum_desc_id"},
                    {"data":'summary_type_name', "name": 'summary_type_name'},
                    {"data":'sum_desc_name', "name": 'sum_desc_name'},
				    {"data":'name', "name": 'name'},
				    //{"data":'sum_desc_id', "name": 'sum_desc_id'},
                    {"data":'sum_desc_id', "name": 'sum_desc_id'}
        ], 
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index  = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
            var deleteFunction = "javascript:branchDelete('" + aData.sum_desc_id + "')" ;
            var url_branchEdit = "javascript:editCurrency('" + aData.sum_desc_id + "')";
            var action = '';
            var status='';
			
            /*if (aData.model_publish_status == 1){
    		status += '<div class="form-check form-switch form-switch-lg"   onclick='+url_active+'><a href='+url_active+' ><input type="checkbox" class="form-check-input" id="customSwitchsizelg" checked></a></div>';
    		} else{
            status += '<div class="form-check form-switch form-switch-lg" onclick='+url_active+'><a href='+url_active+' ><input type="checkbox" class="form-check-input" id="customSwitchsizelg"></a></div>';  
            } $('td:eq(4)', nRow).html(status).addClass('center'); */
            
            action += '<a href="#" onclick='+ url_branchEdit +' title="Edit"><i class="far fa-edit " style="color:green"></i></a>';
            action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            $('td:eq(4)', nRow).html(action).addClass('center');
		}
});

function editCurrency(sum_desc_id)
{  
	$('.highlight').html('');
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'sum_desc_id': sum_desc_id},
        url: url_editdivision,
        success: function (result) 
        {  
            $('.editButton').show();
            $('.saveButton').hide();
            $('.cancelButton').hide();
		    $('#sum_desc_id').val(result.data.sum_desc_id);
            $('#sum_desc_name').val(result.data.sum_desc_name);
            $("#sum_desc_type").val(result.data.sum_desc_type).trigger('change.select2');
            //$('#sum_desc_type').val(result.data.sum_desc_type);
        }
    });
 }
  
function clearForm()
{
    $('#createForm').parsley().reset();
    $('#createForm')[0].reset();
    
    $("#sum_desc_type").select2({
        placeholder: "Select Make",
        allowClear: true
    });
}

function branchDelete(sum_desc_id) 
{
	$('#delete_entry').modal('show');
	$('#sum_desc_id').val(sum_desc_id);
}

function deleteEntry()
{
    var sum_desc_id = $('#sum_desc_id').val();
    
    $.ajax({
        type: 'POST',
        data: {'_token':token, 'sum_desc_id': sum_desc_id},
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
				$('#SummaryDesc_table').DataTable().ajax.reload();
			} 
			else{}
        }
    })
}