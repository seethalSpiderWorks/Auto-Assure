
    $(document).ready(function()
    {
		var export_option = 0; 
        $('select').select2();
        
        if(export_option==1)
        {
            var exporting=1;
        }
        else
        {
            var exporting=0;
        }
        
        var searchable = [];
        var selectable = []; 
        if(exporting==1)
        {
            var btns=[
                {
                    "extend": 'csv',
                    "className": 'btn btn-secondary',
                    "title": 'Leads',
                    "header": true,
                    "footer": true,
                    "exportOptions": {
                    "columns": [ 0, 1, 2, 3, 4, 5, 6],                      
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Followup',
                    header: true,
                    footer: true,
                    exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6],                      
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Leads',
                    pageSize: 'A4',
                    header: true,
                    footer: true,
                    exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6],                       
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Leads',
                    // orientation:'landscape',
                    pageSize: 'A4',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        columns: function (idx, data, node) {
                                    if (node.innerHTML == "Action")
                                        return false;
                                        return true;
                                    },
                        stripHtml: false
                    }
                }
            
                ];
        }
        else
        {
            var btns=[];
        }

$('#lead_table').DataTable(
{
	"responsive": true,
	"serverSide": true,
	"ordering": true,
	"searching": true,
	"filter": true,
	"bLengthChange": true,
	"bAutoWidth": false,
	"info":     false,
	"order": [
            [0, 'desc']
          ],
	"columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,8]
                }
                ],
	"displayLength":25,
	"ajax": 
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			
        "url": url_datatable_followup,
        "type": "post",
        "data": function (data) 
          {
			    var token = '';
                data._token = token;
		        //data.filter_staff = $('#filter_staff').val();
                return data;
          }
        }, 
	"AutoWidth": false,
            columns: [
                {data: 'followup_id', name: 'followup_id'},
				{data:'next_followup_date', name: 'next_followup_date'},
				{data:'breg_mob', name: 'breg_mob'},
                {data:'breg_fname', name: 'breg_fname'},                
                {data:'source_name', name: 'source_name'},
				//{data:'track_variable', name: 'track_variable'},
				{data:'followup_remarks', name: 'followup_remarks'},
                {data:'name', name: 'name'},
                {data:'followup_current_status', name: 'followup_current_status'},
                {data:'followup_id', name: 'followup_id'},
            ],
			//"dom": 'Blfrtip',
			//"aButtons" : btns,
    "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
		var info   = this.dataTable().api().page.info();
		var page   = info.page;
		var length = info.length;
		
		var response = $('#lead_table').DataTable().ajax.json();
		//var option =response.option.opset_options;
		
		var index  = (page * length + (iDataIndex + 1));
		$('td:eq(0)', nRow).html(index).addClass('center');
		
		var dateMod =new Date(aData.next_followup_date);
		dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
		$('td:eq(1)', nRow).html(dateMod).addClass('text-center');
	    $('td:eq(2)', nRow).html(aData.breg_mob).addClass('text-center');
	   
			var badges = '';   
			var status;
			
			if(aData.followup_current_status)
			{
				status = aData.followup_assigned_users_id;
			}
			else
			{
				status = '0';
			} 
		
		var badges = '';     
        if(aData.followup_current_status == 'Assign')
        {
            badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Assigned</span>';
        }
		else if(aData.followup_current_status == 'Reassign')
        {
            badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: auto !important;">Reassigned</span>';
        }
		else if(aData.lead_assigned_status == 'Followup')
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Followup</span>';
        }
		else if(aData.followup_current_status == 'Registered')
        {
            badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Registered</span>';
        }
		else if(aData.followup_current_status == 'Plan / Shedule')
		{ 
			badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span>';
		}
		else if(aData.followup_current_status == 'Reshedule')
		{
			badges = '<span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span>';
		}
		else if(aData.followup_current_status == 'Inspection Completed')
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> Inspection Completed </span>';
        }
		else if(aData.followup_current_status == 'Approved')
		{
			badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span>';
		}
		else if(aData.followup_current_status == 'Rejected')
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Rejected</span>';
        }
		else if(aData.followup_current_status == 'Closed')
        {
            badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Closed</span>';
        }
		
		else if(aData.followup_current_status == 'Inspection')
        {
            badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:orange;cursor: auto !important;">Inspection</span>';
        }
        else
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> New</span>';
        }

		var deleteFunction = "javascript:confirmDelete('"+aData.breg_id+"','"+aData.lead_unq_id+"');";
		var viewFunction = "javascript:viewFollowuppopup('"+aData.lead_id+"','"+status+"');";
		var action = '';

		//action += '<a href='+ viewFunction +' title="View"><i class="fa fa-plus" style="color:#007bff"></i></a>';
		//data-target="#view_modal"
		action += '<a title="View" href="'+public_path+'/leads/followup?id='+aData.lead_id+'"    ><i class="fa fa-plus" style="color:#007bff"></i> </a>';
	  
		action += '&nbsp; <a href='+ deleteFunction +' style="font-size:14px"><i class="far fa-trash-alt" style="color:red"></i></a>';

		$('td:eq(7)', nRow).html(badges).addClass('center');
		$('td:eq(8)', nRow).html(action).addClass('center');
    }
  });

});

/*********************************************************************/

$('#lead_table_all').DataTable(
{
	"responsive": true,
	"serverSide": true,
	"ordering": true,
	"searching": true,
	"filter": true,
	"bLengthChange": true,
	"bAutoWidth": false,
	"info":     false,
	"order": [
            [1, 'desc']
          ],
	"columnDefs": [
                {
                "className": "text-center", 
                "targets": [0,8]
                }
                ],
	"displayLength":25,
	"ajax": 
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			
        "url": url_datatable_followup_all,
        "type": "post",
        "data": function (data) 
          {
			var token = '';
            data._token = token;
		    //data.filter_staff = $('#filter_staff').val();
            return data;
          }
        }, 
	"AutoWidth": false,
		
            columns: [
                {data: 'followup_id', name: 'followup_id'},
				{data:'next_followup_date', name: 'next_followup_date'},
				{data:'breg_mob', name: 'breg_mob'},
                {data:'breg_fname', name: 'breg_fname'},                
                {data:'source_name', name: 'source_name'},
				{data:'followup_remarks', name: 'followup_remarks'},
                {data:'name', name: 'name'},
                {data:'followup_current_status', name: 'followup_current_status'},
                {data:'followup_id', name: 'followup_id'},

            ],
			//"dom": 'Blfrtip',
			//"aButtons" : btns,
    "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
		var info   = this.dataTable().api().page.info();
		var page   = info.page;
		var length = info.length;
		
		var response = $('#lead_table_all').DataTable().ajax.json();
		//var option =response.option.opset_options;
		
		var index  = (page * length + (iDataIndex + 1));
		$('td:eq(0)', nRow).html(index).addClass('center');
		
		var dateMod =new Date(aData.next_followup_date);
		dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
		$('td:eq(1)', nRow).html(dateMod).addClass('text-center');
	    $('td:eq(2)', nRow).html(aData.breg_mob).addClass('text-center');
	   
		var badges = '';   
		var status;
			
			/*if(aData.followup_current_status)
			{
				badges = '<span class="btn btn-success" style="padding: 1px; min-width: 50px;">'+aData.followup_current_status+'</span>';
			}
			else
			{
				badges = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px;"> Unassigned</span>';
			} */
			
			if(aData.followup_current_status)
			{
				status = aData.followup_current_status;
			}
			else
			{
				status = '0';
			}
		
		var badges = '';     
		if(aData.followup_current_status == 'Assign')
        {
            badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Assigned</span>';
        }
		else if(aData.followup_current_status == 'Reassign')
        {
            badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: auto !important;">Reassigned</span>';
        }
		else if(aData.followup_current_status == 'Followup')
        {
            badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Followup</span>';
        }
		else if(aData.followup_current_status == 'Registered')
        {
            badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Registered</span>';
        }
		else if(aData.followup_current_status == 'Plan / Shedule')
		{
			badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span>';
		}
		else if(aData.followup_current_status == 'Reshedule')
		{
			badges = '<span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span>';
		}
		else if(aData.followup_current_status == 'Inspection completed')
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> Inspection completed </span>';
        }
		else if(aData.followup_current_status == 'Approved')
		{
			badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span>';
		}
		else if(aData.followup_current_status == 'Rejected')
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Rejected</span>';
        }
		else if(aData.followup_current_status == 'Closed')
        {
            badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Closed</span>';
        }
        else
        {
            badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> New</span>';
        }
 
		var deleteFunction = "javascript:confirmDelete('"+aData.breg_id+"','"+aData.lead_unq_id+"');";
		var viewFunction = "javascript:viewFollowuppopup('"+aData.lead_id+"','"+status+"');";
		var action = '';

		//action += '<a href='+ viewFunction +' title="View"><i class="fa fa-plus" style="color:#007bff"></i></a>';
		//data-target="#view_modal"
		action += '<a title="View" href="'+public_path+'/leads/followup?id='+aData.lead_id+'"    ><i class="fa fa-plus" style="color:#007bff"></i> </a>';
		
		action += '&nbsp; <a href='+ deleteFunction +' style="font-size:14px"><i class="far fa-trash-alt" style="color:red"></i></a>';

		$('td:eq(7)', nRow).html(badges).addClass('center');
		$('td:eq(8)', nRow).html(action).addClass('center');
    }
});

/****************************** followup table in *****************************************/
function followup_in(id)
{ 	
	$('#followup_table_in').DataTable(
	{
		"responsive": true,
		"serverSide": true,
		"ordering": true,
		"searching": true,
		"bLengthChange": false,
		"info":     false,
		"bDestroy": true,
		"order": [
				[1, 'desc']
			  ],
		"columnDefs": [
					{
					"className": "text-center", 
					"targets": [0,6]
					}
					],
		"displayLength":25,
		"ajax": 
        {
			"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			
			"url": url_get_followup,
			"type": "post",
			"data": function (data) 
			{
				var token = '';
				data._token = token;
				data.id = id;	  
				return data;
		  		//data.filter_staff = $('#filter_staff').val();
			}
        }, 
		"AutoWidth": false,
		
            columns: [
                {data: 'followup_id', name: 'followup_id'},
				{data:'followup_date', name: 'followup_date'},
				//{data:'followup_priority', name: 'followup_priority'},
				{data:'next_followup_date', name: 'next_followup_date'},
				// {data:'joining_date', name: 'joining_date'},
                {data:'followup_remarks', name: 'followup_remarks'},
                {data:'name1', name: 'name1'},
                {data:'name2', name: 'name2'},
				{data:'followup_current_status', name: 'followup_current_status'},

            ],
        "fnCreatedRow": function (nRow, aData, iDataIndex) 
		{
			var info   = this.dataTable().api().page.info();
			var page   = info.page;
			var length = info.length;
    
			var response = $('#followup_table_in').DataTable().ajax.json();
			//var option =response.option.opset_options;
			var index  = (page * length + (iDataIndex + 1));
			$('td:eq(0)', nRow).html(index).addClass('center');
			//var td_index = data.DT_RowIndex;
			//$('td', row).eq(0).html(index+1);
		
			var dateMod = new Date(aData.followup_date);
			dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
			$('td:eq(1)', nRow).html(dateMod).addClass('center');
			
			if(aData.next_followup_date != null)
			{
				var dateMod = new Date(aData.next_followup_date);
				dateMod1  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
			}
			else
			{
				var dateMod1 = '';
			}
		
			$('td:eq(2)', nRow).html(dateMod1).addClass('center');
		
			var badges = '';     
			if(aData.followup_current_status == 'Assign')
			{
				badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Assigned</span>';
			}
			else if(aData.followup_current_status == 'Reassign')
			{
				badges = '<span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: auto !important;">Reassigned</span>';
			}
			else if(aData.followup_current_status == 'Followup')
			{
				badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Followup</span>';
			}
			else if(aData.followup_current_status == 'Registered')
			{
				badges = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Registered</span>';
			}
			else if(aData.followup_current_status == 'Plan / Shedule')
			{
				badges = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span>';
			}
			else if(aData.followup_current_status == 'Reshedule')
			{
				badges = '<span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span>';
			}
			else if(aData.followup_current_status == 'Inspection completed')
			{
				badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> Inspection completed </span>';
			}
			else if(aData.followup_current_status == 'Approved')
			{
				badges = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span>';
			}
			else if(aData.followup_current_status == 'Rejected')
			{
				badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Rejected</span>';
			}
			else if(aData.followup_current_status == 'Closed')
			{
				badges = '<span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Closed</span>';
			}
			else
			{
				badges = '<span class="btn bg-danger" style="padding: 1px; min-width: 50px;cursor: auto !important;color:#f5f6f8;"> New</span>';
			}
		
			$('td:eq(6)', nRow).html(badges).addClass('center');
            //$('td', nRow).eq(1).html(aData.dateMod);
            $('td', nRow).eq(1).attr('style','text-align:center');
            $('td', nRow).eq(2).attr('style','text-align:center');
			$('td', nRow).eq(3).attr('style','text-align:center');
			$('td', nRow).eq(4).attr('style','text-align:center');
            $('td', nRow).eq(5).attr('style','text-align:left');
            $('td', nRow).eq(6).attr('style','text-align:left');
		}
	});
} 