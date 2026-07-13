var token = $('meta[name="csrf-token"]').attr('content');
$('#callDataTable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": false,
  "searching": true,
  "bLengthChange": true,
  "info":     false,
  "order": [[0, 'desc']],
  "columnDefs": [],
  "displayLength":50,
  "ajax": 
      {
      "url": url_CallDatatable,
      "type": "post",
      "data": function (data) 
        {
        data._token = token;
        return data;
        }
      }, 
    "AutoWidth": true,
    "columns": [
              {"data": "calling_date", "name": "calling_date"},
              {"data": "calling_date", "name": "calling_date"},
              {"data": "calling_time", "name": "calling_time"},
              {"data": "calling_number", "name": "calling_number"},
              {"data": "type", "name": "type"},
              {"data": "duration", "name": "duration"},
              {"data": "name", "name": "name"}
            ], 
    "fnCreatedRow": function (nRow, aData, iDataIndex) 
      {
      var info = this.dataTable().api().page.info();
      var page = info.page;
      var length = info.length;
      var index = (page * length + (iDataIndex + 1));
      $('td:eq(0)', nRow).html(index).addClass('center');
      $('td:eq(1)', nRow).addClass('center');
      $('td:eq(2)', nRow).addClass('center');
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