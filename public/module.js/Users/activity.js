var token = $('meta[name="csrf-token"]').attr('content');

$('#actDataTable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  "ordering": true,
  "searching": true,
  "bLengthChange": true,
  "bAutoWidth": false,
  "info":     false,
  "order": [
            [0, 'desc']
          ],
  "columnDefs": [
                {
                "className": "text-center",
                "targets": [0,4]
                }
                ],
  "displayLength":10,
  "ajax":
        {
        "url": url_activity,
        "type": "post",
        "data": function (data)
          {
          data._token = token;
		  data.id= $('#user_id').val();
          return data;
          }
        },
  "AutoWidth": false,
  "columns":
            [
            {"data": "id", "name": "id"},
            {"data": "date", "name": "date"},
            {"data": "time", "name": "time"},
            {"data": "activity_ip", "name": "activity_ip"},
			{"data": "activity_desc", "name": "activity_desc"},
            ],
  "fnCreatedRow": function (nRow, aData, iDataIndex)
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    $('td:eq(0)', nRow).html(index).addClass('center');
	var table = $('#actDataTable').DataTable();
     table.column(2).nodes().to$().css('width', '70px!important');
	$('td:eq(4)', nRow).removeClass('text-center');
    }
  });