var token = $('meta[name="csrf-token"]').attr('content');

$('#viewReportDataTable').DataTable({
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
                "targets": [0,7],
                "orderable": false
            },
            {"className": "text-center", "targets": [0,7]}
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
					{"data": "report_id", "name": "report_id"},
					{"data": "report_reference_no", "name": "report_reference_no"},
					{"data": "report_client_name", "name": "report_client_name"},
 					{"data": "report_date_of_inspection", "name": "report_date_of_inspection"},
 					{"data": "report_vehicle_plate_no", "name": "report_vehicle_plate_no"},
					//{"data": "name", "name": "name"},
					{"data": "report_id", "name": "report_id"},
					{"data": "report_expired_status", "name": "report_expired_status"},
 					{"data": "report_id", "name": "report_id"}
        ], 
		"fnCreatedRow": function (nRow, aData, iDataIndex) 
		{ 
			var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index  = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
            $('td:eq(1)', nRow).html(aData.report_reference_no).addClass('text-center');
			
			var action  = '';
			var viewFunction     = "javascript:viewReport('" + aData.report_id + "')" ;
			//var deleteFunction = "javascript:branchDelete('" + aData.report_id + "')" ;
			//var url_branchEdit = "javascript:editCurrency('" + aData.report_id + "')";

			var dateMod = new Date(aData.report_date_of_inspection);
			dateMod = dateMod.getDate().toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
			$('td:eq(3)', nRow).html(dateMod).addClass('text-center');
			
			var followup_name = aData.lead_assigned_status; // status name 
			var followup_type = aData.lead_followup_type;  // followup status id
			
			//console.log(followup_type);
			
			var CurrStatuc = '';
			
			if(followup_type == 14) // Inspection
			{
 				CurrStatuc = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">'+followup_name+'</span>';
			}
			if(followup_type == 18) // Inspection Completed
			{
 				CurrStatuc = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">'+followup_name+'</span>';
			}
			if(followup_type == 17) // Approved
			{
			    CurrStatuc = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">'+followup_name+'</span>';
			}
			else if(followup_type == 5) // Rejected
            {
                CurrStatuc = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">'+followup_name+'</span>';
            }
    		else if(followup_type == 6) // Closed
            {
                CurrStatuc = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">'+followup_name+'</span>';
            }
			 
			$('td:eq(5)', nRow).html(CurrStatuc).addClass('text-center');
		  
			action += '<a href='+ viewFunction+' data-target="#view_modal"><i class="far fa-eye" title="View" aria-hidden="true" style="color:blue"></i></a>&nbsp;'; 
			
			//action += '<a href='+ url_branchEdit +' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
			//action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
			$('td:eq(6)', nRow).html(aData.report_expired_status).addClass('text-center').css('color','red');
			$('td:eq(7)', nRow).html(action).addClass('center');
    }  
});
 
function branchDelete(report_id) 
{
	$('#delete_entry').modal('show');
	$('#report_id').val(report_id);  
}

function deleteEntry()
{
    var report_id = $('#report_id').val();
    $.ajax({
        type: 'POST',
        data: {'_token':token, 'report_id': report_id},
        url: url_deleteReport,
        success: function (result) 
		{
            if(result.status == 1) 
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
				$('#viewReportDataTable').DataTable().ajax.reload();
			} 
			else {}
        }
    });
}

function clearForm()
{
    $('#createForm').parsley().reset();
    $('#createForm')[0].reset();   
}

function viewReport(report_id)
{
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'report_id': report_id},
        url: url_viewReport,
        success: function (result) 
		{
			$('#view-modal-body').html(result);
            $('#view_modal').modal('show');     
        }
    });
}
 