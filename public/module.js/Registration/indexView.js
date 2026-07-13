
$('#viewRegDatatable').DataTable(
{
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": true,
  "bLengthChange": true,
  "bAutoWidth": false,
  "info": false,
  "order": [
            [0, 'desc']
          ],
  "columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,5],
				 "type": "natural",	
				 "sortable": true
                }
                ],
  "displayLength":25,
  "ajax": 
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			
        "url": url_datatable,
        "type": "post",
        "data": function (data) 
          {
			var token = '';
			data._token = token;
			data.filter_staff = $('#filter_staff').val();
			return data;
          }
        }, 
	
  "AutoWidth": false,
  "columns": 
            [
				{"data": "reg_id", "name": "reg_id"},
				{"data": "reg_date", "name": "reg_date"},
				{"data": "reg_fname", "name": "reg_fname"},
				{"data": "reg_mob", "name": "reg_mob"},
				{"data": "reg_email", "name": "reg_email"},
				{"data": "reg_id", "name": "reg_id"},
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
		var info   = this.dataTable().api().page.info();
		var page   = info.page;
		var length = info.length;
    
		var response = $('#viewRegDatatable').DataTable().ajax.json();
		var option = response.option.opset_options;
 
		var index  = (page * length + (iDataIndex + 1));
		$('td:eq(0)', nRow).html(index).addClass('text-center');
	  
		var dateMod = new Date(aData.reg_date);
		dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
		$('td:eq(1)', nRow).html(dateMod).addClass('center');
 
    	var deleteFunction = "javascript:confirmDelete('"+aData.reg_id+"');" ;
 
    	var action = '';
 
		if(option.indexOf("3") !== -1)
		{
			action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="far fa-trash-alt" style="color:red"></i></a>';
		}
 
    	$('td:eq(5)', nRow).html(action).addClass('center');
    }
});
  
function confirmDelete(id,reg_id)
{
    $('#delete_modal').modal('show');
    $("#del_reg_id").val(id);
    $("#delete_reg").text(reg_id);
}
  
function deleteLead(id)
{
	$.ajax({
		type: 'GET',
		url: url_deleteReg,
		dataType:'JSON',
		data:{'id':id},
        success: function (data) 
		{
			if (data.status == 1) 
            {
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
			 
				$('#delete_modal').modal('hide');
				$('#viewRegDatatable').DataTable().ajax.reload();
				//document.getElementById("createForm").reset();
			} 
			else 
            {
            }

        }
    }) 
}
 
