var token = $('meta[name="csrf-token"]').attr('content');
$('#fixed_depositDataTable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": true,
  "bLengthChange": false,
  "info":     false,
  "order": [[0, 'desc']],
  "columnDefs": [
                  {
                  "targets": [0, 7],
                  "orderable": false
                  },
                  {
                  "className": "text-center", 
                  "targets": [0,7]
                  }
                ],
  "displayLength":15,
  "ajax": {
          "url": url_fixed_depositDataTable,
            "type": "post",
            "data": function (data) 
              {
              data._token = token;
              return data;
              }
          }, 
  "AutoWidth": false,
  "columns": [
              {"data": "fd_name", "name": "fd_name"},
              {"data": "fd_name", "name": "fd_name"},
              {"data": "fd_code", "name": "fd_code"},
              {"data": "fd_amount", "name": "fd_amount"},
              {"data": "fd_interestlockin", "name": "fd_interestlockin"},
              {"data": "fd_interest", "name": "fd_interest"},
              {"data": "fd_interest_payout", "name": "fd_interest_payout"},
              {"data": "fd_interest_payout", "name": "fd_interest_payout"}
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
    var info = this.dataTable().api().page.info();
    var page = info.page;
    var length = info.length;
    var index = (page * length + (iDataIndex + 1));
    $('td:eq(0)', nRow).html(index).addClass('center');
    var mnth = aData.fd_interestlockin+" Months";
    $('td:eq(4)', nRow).html(mnth);
    var deleteFunction = "javascript:fixed_depositDelete('" + aData.fd_id + "')" ;
    var url_fixed_depositEdit = "javascript:editfixed_deposit('" + aData.fd_id + "')"
    var url_viewfixed_deposit = "javascript:viewfixed_deposit('" + aData.fd_id + "')";
    var action = '';
    action = '<a href='+ url_fixed_depositEdit +' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
    action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
    action += '&nbsp <a href='+url_viewfixed_deposit+' data-target="#view_modal"><i class="fa fa-file-text-o" title="View" aria-hidden="true"></i></a>';
    $('td:eq(7)', nRow).html(action).addClass('center');
    $('td:eq(4)', nRow).addClass('center');
    $('td:eq(1)', nRow).addClass('center');
    $('td:eq(2)', nRow).addClass('center');
    $('td:eq(3)', nRow).addClass('center');
    $('td:eq(5)', nRow).addClass('center');
    $('td:eq(6)', nRow).addClass('center');
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
function fixed_depositDelete(id) 
  {
  $('#delete_entry').modal('show');
  $('#del_id').val(id);
  }
function deleteEntry()
  {
  var id = $('#del_id').val();
  $.ajax({
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_deletefixed_deposit,
          success: function (result) 
            {
            if (result.status == 1) 
              {
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
              $('#fixed_depositDataTable').DataTable().ajax.reload();
              } 
            else 
              {
              }
          }
        })
  }
function editfixed_deposit(id)
  {
  $.ajax({
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_editfixed_deposit,
          success: function (result) 
            {
            $('.updatesButton').show();
            $('.savesButton').hide();
            $('#edit_id').val(result.data.fd_id);
            $('#scheme_name').val(result.data.fd_name);
            $('#scheme_code').val(result.data.fd_code);
            $('#fd_amount').val(result.data.fd_amount);
            $('#fd_lock').val(result.data.fd_lockin);
            $('#interest_rate').val(result.data.fd_interest);
            $('#lock_period').val(result.data.fd_interestlockin);
            $('#tenure').val(result.data.fb_tenure);
            $('#payout').val(result.data.fd_interest_payout);
            $('#cancel_charge').val(result.data.fd_cancel_charge);
            $('#penal_charge').val(result.data.fd_penal_charge);
            $('#status').val(result.data.fd_activestatus);
            }
        });
  }
function clearForm()
  {
  $('#createForm').parsley().reset();
  $('#createForm')[0].reset();
  }
function viewfixed_deposit(id) 
  {
  $.ajax({
          type: 'get',
          data: {'_token':token, 'id': id},
          url: url_viewfixed_deposit,
          success: function (result) 
            {
            $('#view-modal-body').html(result);
            $('#view_modal').modal('show');
            }
      });
  }