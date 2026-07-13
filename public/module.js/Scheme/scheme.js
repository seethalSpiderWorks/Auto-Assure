var token = $('meta[name="csrf-token"]').attr('content');
$('#schemeDataTable').DataTable(
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
                  "targets": [0, 6],
                  "orderable": false
                  },
                  {
                  "className": "text-center", 
                  "targets": [0,6]
                  }
                ],
  "displayLength":15,
  "ajax": {
          "url": url_schemeDataTable,
            "type": "post",
            "data": function (data) 
              {
              data._token = token;
              return data;
              }
          }, 
  "AutoWidth": false,
  "columns": [
              {"data": "scheme_name", "name": "scheme_name"},
              {"data": "scheme_name", "name": "scheme_name"},
              {"data": "scheme_code", "name": "scheme_code"},
              {"data": "scheme_amount", "name": "scheme_amount"},
              {"data": "scheme_days", "name": "scheme_days"},
              {"data": "scheme_intrest_rate", "name": "scheme_intrest_rate"},
              {"data": "scheme_intrest_type", "name": "scheme_intrest_type"},
              {"data": "scheme_intrest_type", "name": "scheme_intrest_type"}
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
    var info = this.dataTable().api().page.info();
    var page = info.page;
    var length = info.length;
    var index = (page * length + (iDataIndex + 1));
    $('td:eq(0)', nRow).html(index).addClass('center');
    var deleteFunction = "javascript:schemeDelete('" + aData.scheme_id + "')" ;
    var url_schemeEdit = "javascript:editscheme('" + aData.scheme_id + "')"
    var url_viewscheme = "javascript:viewscheme('" + aData.scheme_id + "')";
    var action = '';
    action = '<a href='+ url_schemeEdit +' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
    action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
    action += '&nbsp <a href='+url_viewscheme+' data-target="#view_modal"><i class="fa fa-file-text-o" title="View" aria-hidden="true"></i></a>';
    $('td:eq(7)', nRow).html(action).addClass('center');
    $('td:eq(4)', nRow).addClass('center');
    $('td:eq(1)', nRow).addClass('center');
    $('td:eq(2)', nRow).addClass('center');
    $('td:eq(3)', nRow).addClass('center');
    $('td:eq(5)', nRow).addClass('center');
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
function schemeDelete(id) 
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
          url: url_deletescheme,
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
              $('#schemeDataTable').DataTable().ajax.reload();
              } 
            else 
              {
              }
          }
        })
  }
function editscheme(id)
  {
  $.ajax({
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_editscheme,
          success: function (result) 
            {
            $('#hide_on_edit').html(result);
            $('.updatesButton').show();
            $('.savesButton').hide();
            }
        });
  }
function clearForm()
  {
  $('#createForm').parsley().reset();
  $('#createForm')[0].reset();
  }
function viewscheme(id) 
  {
  $.ajax({
          type: 'get',
          data: {'_token':token, 'id': id},
          url: url_viewscheme,
          success: function (result) 
            {
            $('#view-modal-body').html(result);
            $('#view_modal').modal('show');
            }
      });
  }