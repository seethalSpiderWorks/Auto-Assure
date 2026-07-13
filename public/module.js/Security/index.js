var token = $('meta[name="csrf-token"]').attr('content');

$('#ipaddressDataTable').DataTable({
         "responsive": true,
        "serverSide": true,
        "ordering": true,
	     "searching": true,
        "order": [
           [0, 'desc']
        ],
        "columnDefs": [{
                "targets": [0, 3],
                "orderable": false
            },
            {"className": "text-center"}
        ],
        "displayLength":25,
        "ajax": {
            "url": url_ipaddressDataTable,
            "type": "post",
            "data": function (data) {
				 data.ip_address = $('#ip_address').val();
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns":
            [
            	{"data": "ip_id", "name": "ip_id"},
            	//{"data": "date", "name": "date"},
				//{"data": "time", "name": "time"},
				{"data": "user_login_on", "name": "user_login_on"},
            	{"data": "user_login_ip", "name": "user_login_ip"},
           		{"data": "ip_id", "name": "ip_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('text-center');
			$('td:eq(1)', nRow).html(aData.user_login_on).addClass('text-center');
			
			//$('td:eq(1)', nRow).html(aData.date).addClass('text-center');
			//$('td:eq(2)', nRow).html(aData.time).addClass('text-center');
			
            var url_ipUnblock = 'javascript:unblockIp("' + aData.ip_id + '","' + aData.user_login_ip + '")';
            var action = '';
            action = '<a href='+ url_ipUnblock +' title="Unblock" ><i class="fa fa-lock" style="color:red"></i></a>';
           $('td:eq(3)', nRow).html(action).addClass('text-center');
  }
    
});

  function SearchIp(){
  
  	$('#ipaddressDataTable').DataTable().ajax.reload();
  }

  function clearForm(){
    $('#ip_address').val('')
$('#ipaddressDataTable').DataTable().ajax.reload();

  }
  function unblockIp(id,ip){
    $('#unblockip').html('Are you sure,You want to Unblock '+ ip +'?');
    $('#ip_id').val(id);
    $('#unblock_ip').modal('show');
   }
   function unlockIp(){
    var ipId = $('#ip_id').val();
  $.ajax({
       type: 'POST',
        data: {'_token':token, 'id': ipId},
        url: url_unblockIp,
        success: function (result) {
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
            $('#unblock_ip').modal('hide');
    $('#ipaddressDataTable').DataTable().ajax.reload();
			 window.location.reload();
         
         }
    });
  }

