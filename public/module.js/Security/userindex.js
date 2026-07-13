var token = $('meta[name="csrf-token"]').attr('content');


  $('#userlistDataTable').DataTable({
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
                "targets": [0, 3],
                "orderable": false
            },
            {"className": "text-center", "targets": [0,3]}
        ],
        "displayLength":100,
        "ajax": {
            "url": url_userGetDatatable,
            "type": "post",
            "data": function (data) {
              data.user_id = $('#user_id').val();
               data.user_name = $('#user_name').val();
                data.status = $('#status').val();
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
            {"data": "emp_id", "name": "emp_id"},
            {"data": "user_id", "name": "user_id"},
            {"data": "emp_name", "name": "emp_name"},
           {"data": "emp_id", "name": "emp_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
            var url_userlock = 'javascript:userStatusChange("' + aData.emp_id + '","' + aData.emp_name + '","'+aData.status+'")';
            var action = '';
            if(aData.status == '0'){
                action = '<a href='+ url_userlock +' title="Unblock"><i class="fa fa-lock" style="color:red"></i></a>';
          }else{
        action = '<a href='+ url_userlock +' title="Block"><i class="fa fa-unlock-alt" style="color:green"></i></a>';
            }
           $('td:eq(3)', nRow).html(action).addClass('center');
  }
    
});
$('#status').select2({
  'width': '93%'
}).change(function () {
    $(this).parsley().validate();
});
/*
 * @function searchUser
 * 
 * 
 * @param 
 * 
 * 
 * @return 
 * data
 * 
 */

  function searchUser(){
  
  	$('#userlistDataTable').DataTable().ajax.reload();
  }

  function clearForm(){
//   $('#user_id').val('');
   $('#user_name').val('');
 $("#status").val("all").trigger("change");
$('#userlistDataTable').DataTable().ajax.reload();

  }
  function userStatusChange(id,name,status){
    if(status == '0'){
      $('#user_lock').html('Are you sure,You want to Unblock '+ name + '?');
      $('#user_lock').css('color','green');
       }else{
        $('#user_lock').html('Are you sure,You want to Block '+ name + '?');
       $('#user_lock').css('color','red');
       }
    $('#lock_id').val(id);
    $('#lock_status').val(status);
    $('#user_unblock').modal('show');
   }

   function statusChange(){
     var userId = $('#lock_id').val();
     var status = $('#lock_status').val();
  $.ajax({
       type: 'POST',
        data: {'_token':token, 'id': userId ,'status':status},
        url: url_unblockuser,
        success: function (result) {
          $.toast({
                    heading: result.heading,
                    text: result.msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 1500,
                    stack: 6
                });
         $('#user_unblock').modal('hide');
    $('#userlistDataTable').DataTable().ajax.reload();
         }
    });
  }
  
