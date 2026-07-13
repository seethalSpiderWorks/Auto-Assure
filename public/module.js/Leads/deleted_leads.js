var token = $('meta[name="csrf-token"]').attr('content');

$('#del_lead_table').DataTable(
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
                "targets": [0,8]
                }
                ],
  "displayLength":25,
  "ajax":
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        "url": url_get_deleted_data,
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
            {"data": "lead_id", "name": "lead_id"},
            {"data": "date", "name": "date"},
			{"data": "breg_fname", "name": "breg_fname"},
			{"data": "breg_mob", "name": "breg_mob"},
			{"data": "breg_email", "name": "breg_email"},
			{"data": "source_name", "name": "source_name"},
            {"data": "track_variable", "name": "track_variable"},
			{"data": "lead_assigned_status", "name": "lead_assigned_status"},
            {"data": "name", "name": "name"},	
            ],
  "fnCreatedRow": function (nRow, aData, iDataIndex)
    {
    var info   = this.dataTable().api().page.info();
    var page   = info.page;
    var length = info.length;
    var index  = (page * length + (iDataIndex + 1));
    
    var response = $('#courseDataTable').DataTable().ajax.json();
    //var option =response.option.opset_options;
   
    $('td:eq(0)', nRow).html(index).addClass('center');

    if(aData.lead_assigned_status == 'Assign')
    {
        badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Assigned</span>';
    }
    else if(aData.lead_assigned_status == 'Reassign')
    {
        badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor:auto !important;">Reassigned</span>';
    }
    else if(aData.lead_assigned_status == 'Followup')
    {
        badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Followup</span>';
    }	
    else if(aData.lead_assigned_status == 'Registered')
    {
        badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Registered</span>';
    }
    else if(aData.lead_assigned_status == 'Rejected')
    {
        badges = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">Rejected</span>';
    }
    else if(aData.lead_assigned_status == 'Closed')
    {
        badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto!important;">Closed</span>';
    }
    else
    {
        badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor:auto!important;color:#f5f6f8;"> New</span>';
    }
	
	$('td:eq(7)', nRow).html(badges).addClass('center');	

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
                $("#del_lead_table").DataTable().ajax.reload();
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
                $("#del_lead_table").DataTable().ajax.reload();
            }
            
        }
    });

});

$("#source").change(function(){
   
    var source = $("#source").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'source': source},
        url: url_sourceDatefilter,
        success: function (result) {
            if(result.status==1)
            {
                $("#del_lead_table").DataTable().ajax.reload();
            }
            
        }
    });

});


$("#campaign").change(function(){
   
    var campaign = $("#campaign").val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'campaign': campaign},
        url: url_campaignDatefilter,
        success: function (result) {
            if(result.status==1)
            {
                $("#del_lead_table").DataTable().ajax.reload();
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
                $("#del_lead_table").DataTable().ajax.reload();
                $('#from_date').val('');
                $('#to_date').val('');
                $('#source').val('').trigger('change');
                $('#campaign').val('').trigger('change');
            }
            
        }
    });

});




