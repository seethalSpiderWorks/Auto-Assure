var token = $('meta[name="csrf-token"]').attr('content');

$('#notification_table').DataTable(
  {
  "responsive": true,
  "serverSide": true,
 // "ordering": true,
  "searching": true,
  "filter": true,
  "bLengthChange": true,
  "bAutoWidth":false,
  "info":     false,
  "order": [
            [0, 'desc']
          ],
  "columnDefs": [
                {
                //"className": "text-center",
                "targets": [0,4]
                }
                ],
  "displayLength":25,
  "ajax":
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        "url": url_get_notification_data,
        "type": "post",
        "data": function (data)
          {
          data._token = token;
          return data;
          }
        },
  "AutoWidth": false,
  "columns":
            [
            {"data": "notification_id", "name": "notification_id"},
            {"data": "formatted_date", "name": "formatted_date"},
			{"data": "formatted_time", "name": "formatted_time"},
			{"data": "notification_ip", "name": "notification_ip"},
			{"data": "notification_msg", "name": "notification_msg"},
            ],
  "fnCreatedRow": function (nRow, aData, iDataIndex)
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    var response = $('#notification_table').DataTable().ajax.json();
    //var option =response.option.opset_options;
   
    $('td:eq(0)', nRow).html(index).addClass('center');
	}
  
  });


$("#from_date").change(function(){
   
    var fromdate = $("#from_date").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'fromdate': fromdate},
        url: url_setFromDatefilter,
        success: function (result) {
            if(result.status==1)
            {
                $("#notification_table").DataTable().ajax.reload();
            }
            
        }
    });

});

$("#to_date").change(function(){
   
    var to_date = $("#to_date").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'to_date': to_date},
        url: url_setToDatefilter,
        success: function (result) {
            if(result.status==1)
            {
                $("#notification_table").DataTable().ajax.reload();
            }
            
        }
    });

});


$("#btn_unsetFilter").click(function(){ 
    $.ajax({
       type: 'POST',
        data: {'_token':token},
        url: url_unsetFilter,
        success: function (result) {
            if(result.status==1)
            {
                $("#notification_table").DataTable().ajax.reload();
                $('#from_date').val('');
                $('#to_date').val('');
            }
            
        }
    });

});