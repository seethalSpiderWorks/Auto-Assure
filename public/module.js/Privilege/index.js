var token = $('meta[name="csrf-token"]').attr('content');


/*   $('#privilegeDataTable').DataTable({
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
        "displayLength":50,
        "ajax": {
            "url": url_privilegeDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
                 console.log(data);
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
            {"data": "privilege_id", "name": "privilege_id"},
            {"data": "privilege_name", "name": "privilege_name"},
            {"data": "privilege_code", "name": "privilege_code"},
            {"data": "privilege_id", "name": "privilege_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
            var deleteFunction = "javascript:privilegeDelete('" + aData.privilege_id + "')" ;
            var url_privilegeEdit = "javascript:editCurrency('" + aData.privilege_id + "')"
            var action = '';
            action = '<a href='+ url_privilegeEdit +' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
             action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            $('td:eq(3)', nRow).html(action).addClass('center');
  }
    
}); */

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
function privilegeDelete(id) {

  $('#delete_entry').modal('show');
  $('#privilege_id').val(id);
    
  }
  function deleteEntry(){
    var id = $('#privilege_id').val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_deleteprivilege,
        success: function (result) {
            if (result.status == 1) {
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

		      	 window.location.href = '';
		       
           } else {

            }
        }
    })
  }
  function editCurrency(id){
  $.ajax({
       type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_editprivilege,
        success: function (result) {
          $('.editButton').show();
          $('.saveButton').hide();
          $('.cancelButton').hide();
          $('#privilege_id').val(result.data.privilege_id)
          $('#privilege_name').val(result.data.privilege_name);
           $('#short_code').val(result.data.privilege_code);
         
             }
    });
  }
  function clearForm(){
    $('#createForm').parsley().reset();
     $('#createForm')[0].reset();
    
}

function scroll_up()
{
    $(".myform").scrollTop(0);

}

