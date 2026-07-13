var token = $('meta[name="csrf-token"]').attr('content');
$('#punchDataTable').DataTable(
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
      "url": url_PunchDatatable,
      "type": "post",
      "data": function (data) 
        {
        data._token = token;
        return data;
        }
      }, 
    "AutoWidth": true,
    "columns": [
              {"data": "in_date", "name": "in_date"},
              {"data": "in_date", "name": "in_date"},
              {"data": "in_time", "name": "in_time"},
              {"data": "out_date", "name": "out_date"},
              {"data": "out_time", "name": "out_time"},
              {"data": "automatic", "name": "automatic"},
              {"data": "retailer_name", "name": "retailer_name"},
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
      $('td:eq(3)', nRow).addClass('center');
      $('td:eq(4)', nRow).addClass('center');
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