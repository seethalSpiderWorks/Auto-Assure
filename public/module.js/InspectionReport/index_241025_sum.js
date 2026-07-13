var token = $('meta[name="csrf-token"]').attr('content');
var followUpStatusList = []; // store follow-up statuses globally 

// Step 1: Load follow-up statuses once before initializing DataTable
$.ajax({
    type: 'POST',
    url: url_statusData,
    data: {
        _token: token,
        branch_id: $('#branch_name_data').val()
    },
    success: function (response) {
        followUpStatusList = response.result;

        // Step 2: Initialize DataTable AFTER fetching statuses
        $('#inspectionReportDataTable').DataTable({
            "responsive": true,
            "serverSide": true,
            "ordering": true,
            "searching": true,
            "bLengthChange": true,
            "info": false,
            "order": [[0, 'desc']],
            "columnDefs": [{
                "targets": [0, 9],
                "orderable": false
            }, {
                "className": "text-center", "targets": [0, 9]
            }],
            "displayLength": 10,
            "ajax": {
                "url": url_DivisionDataTable,
                "type": "POST",
                "data": function (data) {
                    data._token = token;
                    return data;
                }
            },
            "autoWidth": false,
            "columns": [
                {"data": "report_id", "name": "report_id"},
                {"data": "report_reference_no", "name": "report_reference_no"},
                {"data": "report_client_name", "name": "report_client_name"},
                {"data": "report_date_of_inspection", "name": "report_date_of_inspection"},
                {"data": "report_vehicle_plate_no", "name": "report_vehicle_plate_no"},
                {"data": "report_id", "name": "report_id"},
                {"data": "report_id", "name": "report_id"},
                {"data": "report_expired_status", "name": "report_expired_status"},
                {"data": "name", "name": "name"},
                {"data": "report_id", "name": "report_id"}
            ],
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                var info = this.api().page.info();
                var page = info.page;
                var length = info.length;
                var index  = (page * length + (iDataIndex + 1));
                $('td:eq(0)', nRow).html(index).addClass('center');

                var viewFunction   = "javascript:viewReport('" + aData.report_id + "')";
                var deleteFunction = "javascript:branchDelete('" + aData.report_id + "')";
                var url_branchEdit = "javascript:editCurrency('" + aData.report_id + "')";
                var addFollowUp    = "javascript:addFollowUp('" + aData.lead_id + "')";

                var dateMod = new Date(aData.report_date_of_inspection);
                var formattedDate = dateMod.getDate().toString().padStart(2, '0') + '-' +
                    (dateMod.getMonth() + 1).toString().padStart(2, '0') + '-' +
                    dateMod.getFullYear();
                $('td:eq(3)', nRow).html(formattedDate).addClass('text-center');

                // Current status badge
                var followup_name = aData.lead_assigned_status;
                var followup_type = aData.lead_followup_type;
                var CurrStatuc = '';
                if (followup_type == 14) {
                    CurrStatuc = '<span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">' + followup_name + '</span>';
                }
                if (followup_type == 18) {
                    CurrStatuc = '<span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">' + followup_name + '</span>';
                }
                if (followup_type == 17) {
                    CurrStatuc = '<span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">' + followup_name + '</span>';
                }
                if (followup_type == 5) {
                    CurrStatuc = '<span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor:auto !important;">' + followup_name + '</span>';
                }
                $('td:eq(5)', nRow).html(CurrStatuc).addClass('text-center');

                // Build status dropdown from preloaded list
                var status = '<select class="change_status form-control form-select select2" data-enq_id="' + aData.lead_id + '" style="border: 1px solid #08406330;color: #101010;cursor:pointer; padding-top:2px;padding-bottom:2px;width:110px" id="cStatus" onchange="' + addFollowUp + '">';
                status += '<option value="">Select Status</option>';
                $.each(followUpStatusList, function (index, item) {
                    var selected = (item.followup_type_id == followup_type) ? 'selected' : '';
                    status += '<option value="' + item.followup_type_id + '" ' + selected + '>' + item.followup_type_name + '</option>';
                });
                status += '</select>';
                $('td:eq(6)', nRow).html(status).addClass('text-center');

                $('td:eq(7)', nRow).html(aData.report_expired_status).addClass('text-center').css('color', 'red');

                // Actions
                var action = '';
                action += '<a href=' + viewFunction + ' data-target="#view_modal"><i class="far fa-eye" title="View" aria-hidden="true" style="color:blue"></i></a>&nbsp;';
                action += '<a href="#" onclick=' + url_branchEdit + ' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
                action += '&nbsp <a href=' + deleteFunction + ' title="Delete"><i class="fa fa-trash" style="color:red"></i></a>';
                $('td:eq(9)', nRow).html(action).addClass('text-center');
            }
        });
    },
    error: function (xhr) {
        console.error("Failed to load status list:", xhr.responseText);
        alert("Error loading follow-up statuses. Please try again.");
    }
});
 
function branchDelete(report_id) 
{
	$('#delete_entry').modal('show');
	$('#report_id').val(report_id);  
}

function deleteEntry()
{
    var report_id = $('#report_id').val();
    $.ajax({
        type: 'POST',
        data: {'_token':token, 'report_id': report_id},
        url: url_deleteReport,
        success: function (result) 
		{
            if(result.status == 1) 
			{
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
				$('#inspectionReportDataTable').DataTable().ajax.reload();
			} 
			else {}
        }
    })
}

function editCurrency(report_id)
{    
	$.ajax({
       type: 'POST',
        data: {'_token':token, 'report_id': report_id},
        url: url_editdivision,
        success: function (result) 
		{
			$('.editButton').show();
			$('.saveButton').hide();
			$('.cancelButton').hide();
	 
			$('.edit_id').val(result.report.report_id);   
			$('.update_id').val(result.report.report_id);
			$('#report_id').val(result.report.report_id);   
			
			$('#report_reference_no').val(result.report.report_reference_no);
			$('#report_client_name').val(result.report.report_client_name);
			$('#report_client_name_ar').val(result.report.report_client_name_ar);
			$('#report_date_of_inspection').val(result.report.report_date_of_inspection);         
		    $('#report_vehicle_plate_no').val(result.report.report_vehicle_plate_no);      
		     
			$('#vehicle_info_title').val(result.vehicle.vehicle_info_title);  
			$('#vehicle_info_model_year').val(result.vehicle.vehicle_info_model_year); 
			$('#vehicle_info_manuf_year').val(result.vehicle.vehicle_info_manuf_year); 
			$('#vehicle_info_chassis_no').val(result.vehicle.vehicle_info_chassis_no); 
			$('#vehicle_info_odometer').val(result.vehicle.vehicle_info_odometer); 
			$('#vehicle_info_condition').val(result.vehicle.vehicle_info_condition); 
	 
			$('#add_spec_region').val(result.addspec.add_spec_region); 
			$('#add_spec_exterior_color').val(result.addspec.add_spec_exterior_color); 
			$('#add_spec_interior_color').val(result.addspec.add_spec_interior_color); 
			$('#add_spec_gearbox').val(result.addspec.add_spec_gearbox); 
			$('#add_spec_fuel_type').val(result.addspec.add_spec_fuel_type); 
			$('#add_spec_steering_side').val(result.addspec.add_spec_steering_side); 
			$('#add_spec_cylinders').val(result.addspec.add_spec_cylinders); 
			$('#add_spec_engine_size').val(result.addspec.add_spec_engine_size); 
			$('#add_spec_keys').val(result.addspec.add_spec_keys); 
			$('#add_spec_doors').val(result.addspec.add_spec_doors); 
			$('#add_spec_seats').val(result.addspec.add_spec_seats); 
			
			$('#war_service_history').val(result.warranty.war_service_history); 
			$('#war_service_last').val(result.warranty.war_service_last); 
			$('#war_service_next').val(result.warranty.war_service_next); 
		 
			if(result.overview) {
                $('#overview_english').val(result.overview.overview_english);
                $('#overview_arabic').val(result.overview.overview_arabic);
            } else {
                // If no overview found, clear or set empty values
                $('#overview_english').val('');
                $('#overview_arabic').val('');
            }
			//$('#video_url').val(result.video.video_url); 
			
			/*********** Vehicle Specifications Start ***********/  			
			/*######## Performance ########*/
			
			/**** Air Suspension ****/
			if(result.dataOne.air_suspension == 1)
			{
				$('#air_suspension1').prop('checked', true);
			}
			else if(result.dataOne.air_suspension == 2)
			{
				$('#air_suspension2').prop('checked', true);
				$('#air_suspension_cmnt').show();
				$('#air_suspension_cmnt').val(result.dataOne.air_suspension_cmnt);
			}
			else 
			{
				$('#air_suspension3').prop('checked', true);
			}
			
			/**** Adaptive Air Suspension ****/
			if(result.dataOne.adaptive_air_suspension == 1)
			{
				$('#adaptive_air_suspension1').prop('checked', true);
			}
			else if(result.dataOne.adaptive_air_suspension == 2)
			{
				$('#adaptive_air_suspension2').prop('checked', true);
				$('#adaptive_air_suspension_cmnt').show();
				$('#adaptive_air_suspension_cmnt').val(result.dataOne.adaptive_air_suspension_cmnt);
			}
			else 
			{
				$('#adaptive_air_suspension3').prop('checked', true);
			}
			
			/**** Differential lock ****/
			if(result.dataOne.differential_lock == 1)
			{
				$('#differential_lock1').prop('checked', true);
			}
			else if(result.dataOne.differential_lock == 2)
			{
				$('#differential_lock2').prop('checked', true);
				$('#differential_lock_cmnt').show();
				$('#differential_lock_cmnt').val(result.dataOne.differential_lock_cmnt);
			}
			else 
			{
				$('#differential_lock3').prop('checked', true);
			}
			
			/**** Paddle shifters ****/
			if(result.dataOne.paddle_shifters == 1)
			{
				$('#paddle_shifters1').prop('checked', true);
			}
			else if(result.dataOne.paddle_shifters == 2)
			{
				$('#paddle_shifters2').prop('checked', true);
				$('#paddle_shifters_cmnt').show();
				$('#paddle_shifters_cmnt').val(result.dataOne.paddle_shifters_cmnt);
			}
			else 
			{
				$('#paddle_shifters3').prop('checked', true);
			}
			
			/**** Tiptronic ****/
			if(result.dataOne.tiptronic == 1)
			{
				$('#tiptronic1').prop('checked', true);
			}
			else if(result.dataOne.tiptronic == 2)
			{
				$('#tiptronic2').prop('checked', true);
				$('#tiptronic_cmnt').show();
				$('#tiptronic_cmnt').val(result.dataOne.tiptronic_cmnt);
			}
			else 
			{
				$('#tiptronic3').prop('checked', true);
			}
			
			/**** Hill descent assist ****/
			if(result.dataOne.hill_descent_assist == 1)
			{
				$('#hill_descent_assist1').prop('checked', true);
			}
			else if(result.dataOne.hill_descent_assist == 2)
			{
				$('#hill_descent_assist2').prop('checked', true);
				$('#hill_descent_assist_cmnt').show();
				$('#hill_descent_assist_cmnt').val(result.dataOne.hill_descent_assist_cmnt);
			}
			else 
			{
				$('#hill_descent_assist3').prop('checked', true);
			}
			
			/**** Hill start assist ****/
			if(result.dataOne.hill_start_assist == 1)
			{
				$('#hill_start_assist1').prop('checked', true);
			}
			else if(result.dataOne.hill_start_assist == 2)
			{
				$('#hill_start_assist2').prop('checked', true);
				$('#hill_start_assist_cmnt').show();
				$('#hill_start_assist_cmnt').val(result.dataOne.hill_start_assist_cmnt);
			}
			else 
			{
				$('#hill_start_assist3').prop('checked', true);
			}
			
			/**** Auto hold ****/
			if(result.dataOne.auto_hold == 1)
			{
				$('#auto_hold1').prop('checked', true);
			}
			else if(result.dataOne.auto_hold == 2)
			{
				$('#auto_hold2').prop('checked', true);
				$('#auto_hold_cmnt').show();
				$('#auto_hold_cmnt').val(result.dataOne.auto_hold_cmnt);
			}
			else 
			{
				$('#auto_hold3').prop('checked', true);
			}
			
			/**** Comfort Seats ****/
			if(result.dataOne.comfort_seats == 1)
			{
				$('#comfort_seats1').prop('checked', true);
			}
			else if(result.dataOne.comfort_seats == 2)
			{
				$('#comfort_seats2').prop('checked', true);
				$('#comfort_seats_cmnt').show();
				$('#comfort_seats_cmnt').val(result.dataOne.comfort_seats_cmnt);
			}
			else 
			{
				$('#comfort_seats3').prop('checked', true);
			}
			
			/**** sport seats ****/
			if(result.dataOne.sport_seats == 1)
			{
				$('#sport_seats1').prop('checked', true);
			}
			else if(result.dataOne.sport_seats == 2)
			{
				$('#sport_seats2').prop('checked', true);
				$('#sport_seats_cmnt').show();
				$('#sport_seats_cmnt').val(result.dataOne.sport_seats_cmnt);
			}
			else 
			{
				$('#sport_seats3').prop('checked', true);
			}
			
			/**** sport brakes ****/
			if(result.dataOne.sport_brakes == 1)
			{
				$('#sport_brakes1').prop('checked', true);
			}
			else if(result.dataOne.sport_brakes == 2)
			{
				$('#sport_brakes2').prop('checked', true);
				$('#sport_brakes_cmnt').show();
				$('#sport_brakes_cmnt').val(result.dataOne.sport_brakes_cmnt);
			}
			else 
			{
				$('#sport_brakes3').prop('checked', true);
			}
			
			/**** sport suspension ****/
			if(result.dataOne.sport_suspension == 1)
			{
				$('#sport_suspension1').prop('checked', true);
			}
			else if(result.dataOne.sport_suspension == 2)
			{
				$('#sport_suspension2').prop('checked', true);
				$('#sport_suspension_cmnt').show();
				$('#sport_suspension_cmnt').val(result.dataOne.sport_suspension_cmnt);
			}
			else 
			{
				$('#sport_suspension3').prop('checked', true);
			}
			
			/**** sport exhaust ****/
			if(result.dataOne.sport_exhaust == 1)
			{
				$('#sport_exhaust1').prop('checked', true);
			}
			else if(result.dataOne.sport_exhaust == 2)
			{
				$('#sport_exhaust2').prop('checked', true);
				$('#sport_exhaust_cmnt').show();
				$('#sport_exhaust_cmnt').val(result.dataOne.sport_exhaust_cmnt);
			}
			else 
			{
				$('#sport_exhaust3').prop('checked', true);
			}
			
			/**** lane change ****/
			if(result.dataOne.lane_change == 1)
			{
				$('#lane_change1').prop('checked', true);
			}
			else if(result.dataOne.lane_change == 2)
			{
				$('#lane_change2').prop('checked', true);
				$('#lane_change_cmnt').show();
				$('#lane_change_cmnt').val(result.dataOne.lane_change_cmnt);
			}
			else 
			{
				$('#lane_change3').prop('checked', true);
			}
			
			/**** Launch control ****/
			if(result.dataOne.launch_control == 1)
			{
				$('#launch_control1').prop('checked', true);
			}
			else if(result.dataOne.launch_control == 2)
			{
				$('#launch_control2').prop('checked', true);
				$('#launch_control_cmnt').show();
				$('#launch_control_cmnt').val(result.dataOne.launch_control_cmnt);
			}
			else 
			{
				$('#launch_control3').prop('checked', true);
			}
			
			/*######## Safety ########*/  
			/* Child Safety Seats */  
			if(result.dataOne.child_safety_seats == 1)
			{
				$('#child_safety_seats1').prop('checked', true);
			}
			else if(result.dataOne.child_safety_seats == 2)
			{
				$('#child_safety_seats2').prop('checked', true);
				$('#child_safety_seats_cmnt').show();
				$('#child_safety_seats_cmnt').val(result.dataOne.child_safety_seats_cmnt);
			}
			else 
			{
				$('#child_safety_seats3').prop('checked', true);
			}
			
			/* front_view_camera */  
			if(result.dataOne.front_view_camera == 1)
			{
				$('#front_view_camera1').prop('checked', true);
			}
			else if(result.dataOne.front_view_camera == 2)
			{
				$('#front_view_camera2').prop('checked', true);
				$('#front_view_camera_cmnt').show();
				$('#front_view_camera_cmnt').val(result.dataOne.front_view_camera_cmnt);
			}
			else 
			{
				$('#front_view_camera3').prop('checked', true);
			}
			
			/* rear_view_camera */  
			if(result.dataOne.rear_view_camera == 1)
			{
				$('#rear_view_camera1').prop('checked', true);
			}
			else if(result.dataOne.rear_view_camera == 2)
			{
				$('#rear_view_camera2').prop('checked', true);
				$('#rear_view_camera_cmnt').show();
				$('#rear_view_camera_cmnt').val(result.dataOne.rear_view_camera_cmnt);
			}
			else 
			{
				$('#rear_view_camera3').prop('checked', true);
			}
			
			/* degree_camera */  
			if(result.dataOne.degree_camera == 1)
			{
				$('#degree_camera1').prop('checked', true);
			}
			else if(result.dataOne.degree_camera == 2)
			{
				$('#degree_camera2').prop('checked', true);
				$('#degree_camera_cmnt').show();
				$('#degree_camera_cmnt').val(result.dataOne.degree_camera_cmnt);
			}
			else 
			{
				$('#degree_camera3').prop('checked', true);
			}
			
			/* front_parking_sensors */  
			if(result.dataOne.front_parking_sensors == 1)
			{
				$('#front_parking_sensors1').prop('checked', true);
			}
			else if(result.dataOne.front_parking_sensors == 2)
			{
				$('#front_parking_sensors2').prop('checked', true);
				$('#front_parking_sensors_cmnt').show();
				$('#front_parking_sensors_cmnt').val(result.dataOne.front_parking_sensors_cmnt);
			}
			else 
			{
				$('#front_parking_sensors3').prop('checked', true);
			}
 			
			/* Rear Parking Sensors */ 
			if(result.dataOne.rear_parking_sensors == 1)
			{
				$('#rear_parking_sensors1').prop('checked', true);
			}
			else if(result.dataOne.rear_parking_sensors == 2)
			{
				$('#rear_parking_sensors2').prop('checked', true);
				$('#rear_parking_sensors_cmnt').show();
				$('#rear_parking_sensors_cmnt').val(result.dataOne.rear_parking_sensors_cmnt);
			}
			else 
			{
				$('#rear_parking_sensors3').prop('checked', true);
			}
			
			/* Lane Departure */ 
			if(result.dataOne.lane_departure == 1)
			{
				$('#lane_departure1').prop('checked', true);
			}
			else if(result.dataOne.lane_departure == 2)
			{
				$('#lane_departure2').prop('checked', true);
				$('#lane_departure_cmnt').show();
				$('#lane_departure_cmnt').val(result.dataOne.lane_departure_cmnt);
			}
			else 
			{
				$('#lane_departure3').prop('checked', true);
			}
			
			/* Anti-Lock Brakes */ 
			if(result.dataOne.anti_lock_brakes == 1)
			{
				$('#anti_lock_brakes1').prop('checked', true);
			}
			else if(result.dataOne.anti_lock_brakes == 2)
			{
				$('#anti_lock_brakes2').prop('checked', true);
				$('#anti_lock_brakes_cmnt').show();
				$('#anti_lock_brakes_cmnt').val(result.dataOne.anti_lock_brakes_cmnt);
			}
			else 
			{
				$('#anti_lock_brakes3').prop('checked', true);
			}
			
			/* EBD */ 
			if(result.dataOne.ebd == 1)
			{
				$('#ebd1').prop('checked', true);
			}
			else if(result.dataOne.ebd == 2)
			{
				$('#ebd2').prop('checked', true);
				$('#ebd_cmnt').show();
				$('#ebd_cmnt').val(result.dataOne.ebd_cmnt);
			}
			else 
			{
				$('#ebd3').prop('checked', true);
			}
			
			/* Alarm */ 
			if(result.dataOne.alarm == 1)
			{
				$('#alarm1').prop('checked', true);
			}
			else if(result.dataOne.alarm == 2)
			{
				$('#alarm2').prop('checked', true);
				$('#alarm_cmnt').show();
				$('#alarm_cmnt').val(result.dataOne.alarm_cmnt);
			}
			else 
			{
				$('#alarm3').prop('checked', true);
			}
			
			/* Front Airbags */ 
			if(result.dataOne.front_airbags == 1)
			{
				$('#front_airbags1').prop('checked', true);
			}
			else if(result.dataOne.front_airbags == 2)
			{
				$('#front_airbags2').prop('checked', true);
				$('#front_airbags_cmnt').show();
				$('#front_airbags_cmnt').val(result.dataOne.front_airbags_cmnt);
			}
			else 
			{
				$('#front_airbags3').prop('checked', true);
			}
			
			/* Side Airbags */ 
			if(result.dataOne.side_airbags == 1)
			{
				$('#side_airbags1').prop('checked', true);
			}
			else if(result.dataOne.side_airbags == 2)
			{
				$('#side_airbags2').prop('checked', true);
				$('#side_airbags_cmnt').show();
				$('#side_airbags_cmnt').val(result.dataOne.side_airbags_cmnt);
			}
			else 
			{
				$('#side_airbags3').prop('checked', true);
			}
			
			/* Traction Control Sys */ 
			if(result.dataOne.traction_control_sys == 1)
			{
				$('#traction_control_sys1').prop('checked', true);
			}
			else if(result.dataOne.traction_control_sys == 2)
			{
				$('#traction_control_sys2').prop('checked', true);
				$('#traction_control_sys_cmnt').show();
				$('#traction_control_sys_cmnt').val(result.dataOne.traction_control_sys_cmnt);
			}
			else 
			{
				$('#traction_control_sys3').prop('checked', true);
			}
			
			/* park assist */ 
			if(result.dataOne.park_assist == 1)
			{
				$('#park_assist1').prop('checked', true);
			}
			else if(result.dataOne.park_assist == 2)
			{
				$('#park_assist2').prop('checked', true);
				$('#park_assist_cmnt').show();
				$('#park_assist_cmnt').val(result.dataOne.park_assist_cmnt);
			}
			else 
			{
				$('#park_assist3').prop('checked', true);
			}
			
			/* blind spot monitor */ 
			if(result.dataOne.blind_spot_monitor == 1)
			{
				$('#blind_spot_monitor1').prop('checked', true);
			}
			else if(result.dataOne.blind_spot_monitor == 2)
			{
				$('#blind_spot_monitor2').prop('checked', true);
				$('#blind_spot_monitor_cmnt').show();
				$('#blind_spot_monitor_cmnt').val(result.dataOne.blind_spot_monitor_cmnt);
			}
			else 
			{
				$('#blind_spot_monitor3').prop('checked', true);
			}
			
			/* Anti Glare Rear View Mirror */ 
			if(result.dataOne.anti_glare_rear_view == 1)
			{
				$('#anti_glare_rear_view1').prop('checked', true);
			}
			else if(result.dataOne.anti_glare_rear_view == 2)
			{
				$('#anti_glare_rear_view2').prop('checked', true);
				$('#anti_glare_rear_view_cmnt').show();
				$('#anti_glare_rear_view_cmnt').val(result.dataOne.anti_glare_rear_view_cmnt);
			}
			else 
			{
				$('#anti_glare_rear_view3').prop('checked', true);
			}
			
			/* Tire Pressure Monitor */ 
			if(result.dataOne.tire_pressure_monitor == 1)
			{
				$('#tire_pressure_monitor1').prop('checked', true);
			}
			else if(result.dataOne.tire_pressure_monitor == 2)
			{
				$('#tire_pressure_monitor2').prop('checked', true);
				$('#tire_pressure_monitor_cmnt').show();
				$('#tire_pressure_monitor_cmnt').val(result.dataOne.tire_pressure_monitor_cmnt);
			}
			else 
			{
				$('#tire_pressure_monitor3').prop('checked', true);
			}
			
			
			/*######## Interior - Entertainment ########*/
			/* Digital_driver_display */ 
			if(result.dataTwo.digital_driver_display == 1)
			{
				$('#digital_driver_display1').prop('checked', true);
			}
			else if(result.dataTwo.digital_driver_display == 2)
			{
				$('#digital_driver_display2').prop('checked', true);
				$('#digital_driver_display_cmnt').show();
				$('#digital_driver_display_cmnt').val(result.dataTwo.digital_driver_display_cmnt);
			}
			else 
			{
				$('#digital_driver_display3').prop('checked', true);
			}
			
			/* CD Player */ 
			if(result.dataTwo.cd_player == 1)
			{
				$('#cd_player1').prop('checked', true);
			}
			else if(result.dataTwo.cd_player == 2)
			{
				$('#cd_player2').prop('checked', true);
				$('#cd_player_cmnt').show();
				$('#cd_player_cmnt').val(result.dataTwo.cd_player_cmnt);
			}
			else 
			{
				$('#cd_player3').prop('checked', true);
			}
			
			/* DVD Player */ 
			if(result.dataTwo.dvd_player == 1)
			{
				$('#dvd_player1').prop('checked', true);
			}
			else if(result.dataTwo.dvd_player == 2)
			{
				$('#dvd_player2').prop('checked', true);
				$('#dvd_player_cmnt').show();
				$('#dvd_player_cmnt').val(result.dataTwo.dvd_player_cmnt);
			}
			else 
			{
				$('#dvd_player3').prop('checked', true);
			}
			
			/* MP3 Player */ 
			if(result.dataTwo.mp_player == 1)
			{
				$('#mp_player1').prop('checked', true);
			}
			else if(result.dataTwo.mp_player == 2)
			{
				$('#mp_player2').prop('checked', true);
				$('#mp_player_cmnt').show();
				$('#mp_player_cmnt').val(result.dataTwo.mp_player_cmnt);
			}
			else 
			{
				$('#mp_player3').prop('checked', true);
			}
			
			/* SD Card Player */ 
			if(result.dataTwo.sd_card_player == 1)
			{
				$('#sd_card_player1').prop('checked', true);
			}
			else if(result.dataTwo.sd_card_player == 2)
			{
				$('#sd_card_player2').prop('checked', true);
				$('#sd_card_player_cmnt').show();
				$('#sd_card_player_cmnt').val(result.dataTwo.sd_card_player_cmnt);
			}
			else 
			{
				$('#sd_card_player3').prop('checked', true);
			}
			
			/* bluetooth_interface */ 
			if(result.dataTwo.bluetooth_interface == 1)
			{
				$('#bluetooth_interface1').prop('checked', true);
			}
			else if(result.dataTwo.bluetooth_interface == 2)
			{
				$('#bluetooth_interface2').prop('checked', true);
				$('#bluetooth_interface_cmnt').show();
				$('#bluetooth_interface_cmnt').val(result.dataTwo.bluetooth_interface_cmnt);
			}
			else 
			{
				$('#bluetooth_interface3').prop('checked', true);
			}
			
			/* premium_sound_system */ 
			if(result.dataTwo.premium_sound_system == 1)
			{
				$('#premium_sound_system1').prop('checked', true);
			}
			else if(result.dataTwo.premium_sound_system == 2)
			{
				$('#premium_sound_system2').prop('checked', true);
				$('#premium_sound_system_cmnt').show();
				$('#premium_sound_system_cmnt').val(result.dataTwo.premium_sound_system_cmnt);
			}
			else 
			{
				$('#premium_sound_system3').prop('checked', true);
			}
			
			/* AUX Audio System */ 
			if(result.dataTwo.aux_audio_system == 1)
			{
				$('#aux_audio_system1').prop('checked', true);
			}
			else if(result.dataTwo.aux_audio_system == 2)
			{
				$('#aux_audio_system2').prop('checked', true);
				$('#aux_audio_system_cmnt').show();
				$('#aux_audio_system_cmnt').val(result.dataTwo.aux_audio_system_cmnt);
			}
			else 
			{
				$('#aux_audio_system3').prop('checked', true);
			}
			
			/* USB */ 
			if(result.dataTwo.usb == 1)
			{
				$('#usb1').prop('checked', true);
			}
			else if(result.dataTwo.usb == 2)
			{
				$('#usb2').prop('checked', true);
				$('#usb_cmnt').show();
				$('#usb_cmnt').val(result.dataTwo.usb_cmnt);
			}
			else 
			{
				$('#usb3').prop('checked', true);
			}
			
			/* USB-C */
			if(result.dataTwo.usb_c == 1)
			{
				$('#usb_c1').prop('checked', true);
			}
			else if(result.dataTwo.usb_c == 2)
			{
				$('#usb_c2').prop('checked', true);
				$('#usb_c_cmnt').show();
				$('#usb_c_cmnt').val(result.dataTwo.usb_c_cmnt);
			}
			else 
			{
				$('#usb_c3').prop('checked', true);
			}
			
			/* Touch screen */
			if(result.dataTwo.touch_screen == 1)
			{
				$('#touch_screen1').prop('checked', true);
			}
			else if(result.dataTwo.touch_screen == 2)
			{
				$('#touch_screen2').prop('checked', true);
				$('#touch_screen_cmnt').show();
				$('#touch_screen_cmnt').val(result.dataTwo.touch_screen_cmnt);
			}
			else 
			{
				$('#touch_screen3').prop('checked', true);
			}
			
			/* Rear Seat Entertainment System */ 
			if(result.dataTwo.rear_seat_enter_sys == 1)
			{
				$('#rear_seat_enter_sys1').prop('checked', true);
			}
			else if(result.dataTwo.rear_seat_enter_sys == 2)
			{
				$('#rear_seat_enter_sys2').prop('checked', true);
				$('#rear_seat_enter_sys_cmnt').show();
				$('#rear_seat_enter_sys_cmnt').val(result.dataTwo.rear_seat_enter_sys_cmnt);
			}
			else 
			{
				$('#rear_seat_enter_sys3').prop('checked', true);
			} 
			 
			/** wireless **/
			if(result.dataTwo.wireless == 1)
			{
				$('#wireless1').prop('checked', true);
			}
			else if(result.dataTwo.wireless == 2)
			{
				$('#wireless2').prop('checked', true);
				$('#wireless_cmnt').show();
				$('#wireless_cmnt').val(result.dataTwo.wireless_cmnt);
			}
			else 
			{
				$('#wireless3').prop('checked', true);
			}
			
			/** ambient_lighting **/
			if(result.dataTwo.ambient_lighting == 1)
			{
				$('#ambient_lighting1').prop('checked', true);
			}
			else if(result.dataTwo.ambient_lighting == 2)
			{
				$('#ambient_lighting2').prop('checked', true);
				$('#ambient_lighting_cmnt').show();
				$('#ambient_lighting_cmnt').val(result.dataTwo.ambient_lighting_cmnt);
			}
			else 
			{
				$('#ambient_lighting3').prop('checked', true);
			}
			
			/** apple_carplay **/
			if(result.dataTwo.apple_carplay == 1)
			{
				$('#apple_carplay1').prop('checked', true);
			}
			else if(result.dataTwo.apple_carplay == 2)
			{
				$('#apple_carplay2').prop('checked', true);
				$('#apple_carplay_cmnt').show();
				$('#apple_carplay_cmnt').val(result.dataTwo.apple_carplay_cmnt);
			}
			else 
			{
				$('#apple_carplay3').prop('checked', true);
			}
			
			/** navigation **/
			if(result.dataTwo.navigation == 1)
			{
				$('#navigation1').prop('checked', true);
			}
			else if(result.dataTwo.navigation == 2)
			{
				$('#navigation2').prop('checked', true);
				$('#navigation_cmnt').show();
				$('#navigation_cmnt').val(result.dataTwo.navigation_cmnt);
			}
			else 
			{
				$('#navigation3').prop('checked', true);
			}
			
			/** standard_ac **/
			if(result.dataTwo.standard_ac == 1)
			{
				$('#standard_ac1').prop('checked', true);
			}
			else if(result.dataTwo.standard_ac == 2)
			{
				$('#standard_ac2').prop('checked', true);
				$('#standard_ac_cmnt').show();
				$('#standard_ac_cmnt').val(result.dataTwo.standard_ac_cmnt);
			}
			else 
			{
				$('#standard_ac3').prop('checked', true);
			}

			/** dual_climcont_ac **/
			if(result.dataTwo.dual_climcont_ac == 1)
			{
				$('#dual_climcont_ac1').prop('checked', true);
			}
			else if(result.dataTwo.dual_climcont_ac == 2)
			{
				$('#dual_climcont_ac2').prop('checked', true);
				$('#dual_climcont_ac_cmnt').show();
				$('#dual_climcont_ac_cmnt').val(result.dataTwo.dual_climcont_ac_cmnt);
			}
			else 
			{
				$('#dual_climcont_ac3').prop('checked', true);
			}

			/** multi_climcont_ac **/
			if(result.dataTwo.multi_climcont_ac == 1)
			{
				$('#multi_climcont_ac1').prop('checked', true);
			}
			else if(result.dataTwo.multi_climcont_ac == 2)
			{
				$('#multi_climcont_ac2').prop('checked', true);
				$('#multi_climcont_ac_cmnt').show();
				$('#multi_climcont_ac_cmnt').val(result.dataTwo.multi_climcont_ac_cmnt);
			}
			else 
			{
				$('#multi_climcont_ac3').prop('checked', true);
			}

			/** keyless_entry **/
			if(result.dataTwo.keyless_entry == 1)
			{
				$('#keyless_entry1').prop('checked', true);
			}
			else if(result.dataTwo.keyless_entry == 2)
			{
				$('#keyless_entry2').prop('checked', true);
				$('#keyless_entry_cmnt').show();
				$('#keyless_entry_cmnt').val(result.dataTwo.keyless_entry_cmnt);
			}
			else 
			{
				$('#keyless_entry3').prop('checked', true);
			}
			
			/** keyless_start **/
			if(result.dataTwo.keyless_start == 1)
			{
				$('#keyless_start1').prop('checked', true);
			}
			else if(result.dataTwo.keyless_start == 2)
			{
				$('#keyless_start2').prop('checked', true);
				$('#keyless_start_cmnt').show();
				$('#keyless_start_cmnt').val(result.dataTwo.keyless_start_cmnt);
			}
			else 
			{
				$('#keyless_start3').prop('checked', true);
			}
			
			/** power_steering **/
			if(result.dataTwo.power_steering == 1)
			{
				$('#power_steering1').prop('checked', true);
			}
			else if(result.dataTwo.power_steering == 2)
			{
				$('#power_steering2').prop('checked', true);
				$('#power_steering_cmnt').show();
				$('#power_steering_cmnt').val(result.dataTwo.power_steering_cmnt);
			}
			else 
			{
				$('#power_steering3').prop('checked', true);
			}
			
			/** heads_up_display **/
			if(result.dataTwo.heads_up_display == 1)
			{
				$('#heads_up_display1').prop('checked', true);
			}
			else if(result.dataTwo.heads_up_display == 2)
			{
				$('#heads_up_display2').prop('checked', true);
				$('#heads_up_display_cmnt').show();
				$('#heads_up_display_cmnt').val(result.dataTwo.heads_up_display_cmnt);
			}
			else 
			{
				$('#heads_up_display3').prop('checked', true);
			}

			/** cruise_control **/
			if(result.dataTwo.cruise_control == 1)
			{
				$('#cruise_control1').prop('checked', true);
			}
			else if(result.dataTwo.cruise_control == 2)
			{
				$('#cruise_control2').prop('checked', true);
				$('#cruise_control_cmnt').show();
				$('#cruise_control_cmnt').val(result.dataTwo.cruise_control_cmnt);
			}
			else 
			{
				$('#cruise_control3').prop('checked', true);
			}
			
			/** adaptive_cruise_control **/
			if(result.dataTwo.adaptive_cruise_control == 1)
			{
				$('#adaptive_cruise_control1').prop('checked', true);
			}
			else if(result.dataTwo.adaptive_cruise_control == 2)
			{
				$('#adaptive_cruise_control2').prop('checked', true);
				$('#adaptive_cruise_control_cmnt').show();
				$('#adaptive_cruise_control_cmnt').val(result.dataTwo.adaptive_cruise_control_cmnt);
			}
			else 
			{
				$('#adaptive_cruise_control3').prop('checked', true);
			}

			/** seat_cooling_front **/
			if(result.dataTwo.seat_cooling_front == 1)
			{
				$('#seat_cooling_front1').prop('checked', true);
			}
			else if(result.dataTwo.seat_cooling_front == 2)
			{
				$('#seat_cooling_front2').prop('checked', true);
				$('#seat_cooling_front_cmnt').show();
				$('#seat_cooling_front_cmnt').val(result.dataTwo.seat_cooling_front_cmnt);
			}
			else 
			{
				$('#seat_cooling_front3').prop('checked', true);
			}
			
			/** seat_cooling_rear **/
			if(result.dataTwo.seat_cooling_rear == 1)
			{
				$('#seat_cooling_rear1').prop('checked', true);
			}
			else if(result.dataTwo.seat_cooling_rear == 2)
			{
				$('#seat_cooling_rear2').prop('checked', true);
				$('#seat_cooling_rear_cmnt').show();
				$('#seat_cooling_rear_cmnt').val(result.dataTwo.seat_cooling_rear_cmnt);
			}
			else 
			{
				$('#seat_cooling_rear3').prop('checked', true);
			}
			
			/** seat_massage_front **/
			if(result.dataTwo.seat_massage_front == 1)
			{
				$('#seat_massage_front1').prop('checked', true);
			}
			else if(result.dataTwo.seat_massage_front == 2)
			{
				$('#seat_massage_front2').prop('checked', true);
				$('#seat_massage_front_cmnt').show();
				$('#seat_massage_front_cmnt').val(result.dataTwo.seat_massage_front_cmnt);
			}
			else 
			{
				$('#seat_massage_front3').prop('checked', true);
			}
			
			/** seat_massage_rear **/
			if(result.dataTwo.seat_massage_rear == 1)
			{
				$('#seat_massage_rear1').prop('checked', true);
			}
			else if(result.dataTwo.seat_massage_rear == 2)
			{
				$('#seat_massage_rear2').prop('checked', true);
				$('#seat_massage_rear_cmnt').show();
				$('#seat_massage_rear_cmnt').val(result.dataTwo.seat_massage_rear_cmnt);
			}
			else 
			{
				$('#seat_massage_rear3').prop('checked', true);
			}
			
			/** driver_memory **/
			if(result.dataTwo.driver_memory == 1)
			{
				$('#driver_memory_seat1').prop('checked', true);
			}
			else if(result.dataTwo.driver_memory_seat == 2)
			{
				$('#driver_memory_seat2').prop('checked', true);
				$('#driver_memory_seat_cmnt').show();
				$('#driver_memory_seat_cmnt').val(result.dataTwo.driver_memory_seat_cmnt);
			}
			else 
			{
				$('#driver_memory3').prop('checked', true);
			}
			
			/** passenger_memory **/
			if(result.dataTwo.passenger_memory_seat == 1)
			{
				$('#passenger_memory_seat1').prop('checked', true);
			}
			else if(result.dataTwo.passenger_memory_seat == 2)
			{
				$('#passenger_memory_seat2').prop('checked', true);
				$('#passenger_memory_seat_cmnt').show();
				$('#passenger_memory_seat_cmnt').val(result.dataTwo.passenger_memory_seat_cmnt);
			}
			else 
			{
				$('#passenger_memory3').prop('checked', true);
			}

			/** power_driver_seats **/
			if(result.dataTwo.power_driver_seats == 1)
			{
				$('#power_driver_seats1').prop('checked', true);
			}
			else if(result.dataTwo.power_driver_seats == 2)
			{
				$('#power_driver_seats2').prop('checked', true);
				$('#power_driver_seats_cmnt').show();
				$('#power_driver_seats_cmnt').val(result.dataTwo.power_driver_seats_cmnt);
			}
			else 
			{
				$('#power_driver_seats3').prop('checked', true);
			}

			/** power_passenger_seats **/
			if(result.dataTwo.power_passenger_seats == 1)
			{
				$('#power_passenger_seats1').prop('checked', true);
			}
			else if(result.dataTwo.power_passenger_seats == 2)
			{
				$('#power_passenger_seats2').prop('checked', true);
				$('#power_passenger_seats_cmnt').show();
				$('#power_passenger_seats_cmnt').val(result.dataTwo.power_passenger_seats_cmnt);
			}
			else 
			{
				$('#power_passenger_seats3').prop('checked', true);
			}

			/** power_rear_seats **/
			if(result.dataTwo.power_rear_seats == 1)
			{
				$('#power_rear_seats1').prop('checked', true);
			}
			else if(result.dataTwo.power_rear_seats == 2)
			{
				$('#power_rear_seats2').prop('checked', true);
				$('#power_rear_seats_cmnt').show();
				$('#power_rear_seats_cmnt').val(result.dataTwo.power_rear_seats_cmnt);
			}
			else 
			{
				$('#power_rear_seats3').prop('checked', true);
			}
			
			/** power_front_windows **/
			if(result.dataTwo.power_front_windows == 1)
			{
				$('#power_front_windows1').prop('checked', true);
			}
			else if(result.dataTwo.power_front_windows == 2)
			{
				$('#power_front_windows2').prop('checked', true);
				$('#power_front_windows_cmnt').show();
				$('#power_front_windows_cmnt').val(result.dataTwo.power_front_windows_cmnt);
			}
			else 
			{
				$('#power_front_windows3').prop('checked', true);
			}
			
			/** power_rear_windows **/
			if(result.dataTwo.power_rear_windows == 1)
			{
				$('#power_rear_windows1').prop('checked', true);
			}
			else if(result.dataTwo.power_rear_windows == 2)
			{
				$('#power_rear_windows2').prop('checked', true);
				$('#power_rear_windows_cmnt').show();
				$('#power_rear_windows_cmnt').val(result.dataTwo.power_rear_windows_cmnt);
			}
			else 
			{
				$('#power_rear_windows3').prop('checked', true);
			}
			
			/** power_trunk **/
			if(result.dataTwo.power_trunk == 1)
			{
				$('#power_trunk1').prop('checked', true);
			}
			else if(result.dataTwo.power_trunk == 2)
			{
				$('#power_trunk2').prop('checked', true);
				$('#power_trunk_cmnt').show();
				$('#power_trunk_cmnt').val(result.dataTwo.power_trunk_cmnt);
			}
			else 
			{
				$('#power_trunk3').prop('checked', true);
			}

			/** power_locks **/
			if(result.dataTwo.power_locks == 1)
			{
				$('#power_locks1').prop('checked', true);
			}
			else if(result.dataTwo.power_locks == 2)
			{
				$('#power_locks2').prop('checked', true);
				$('#power_locks_cmnt').show();
				$('#power_locks_cmnt').val(result.dataTwo.power_locks_cmnt);
			}
			else 
			{
				$('#power_locks3').prop('checked', true);
			}
			
			/** power_mirrors **/
			if(result.dataTwo.power_mirrors == 1)
			{
				$('#power_mirrors1').prop('checked', true);
			}
			else if(result.dataTwo.power_mirrors == 2)
			{
				$('#power_mirrors2').prop('checked', true);
				$('#power_mirrors_cmnt').show();
				$('#power_mirrors_cmnt').val(result.dataTwo.power_mirrors_cmnt);
			}
			else 
			{
				$('#power_mirrors3').prop('checked', true);
			}
			
			/** power_folding_mirrors **/
			if(result.dataTwo.power_folding_mirrors == 1)
			{
				$('#power_folding_mirrors1').prop('checked', true);
			}
			else if(result.dataTwo.power_folding_mirrors == 2)
			{
				$('#power_folding_mirrors2').prop('checked', true);
				$('#power_folding_mirrors_cmnt').show();
				$('#power_folding_mirrors_cmnt').val(result.dataTwo.power_folding_mirrors_cmnt);
			}
			else 
			{
				$('#power_folding_mirrors3').prop('checked', true);
			}
			
			/** sun_roof **/
			if(result.dataTwo.sun_roof == 1)
			{
				$('#sun_roof1').prop('checked', true);
			}
			else if(result.dataTwo.sun_roof == 2)
			{
				$('#sun_roof2').prop('checked', true);
				$('#sun_roof_cmnt').show();
				$('#sun_roof_cmnt').val(result.dataTwo.sun_roof_cmnt);
			}
			else 
			{
				$('#sun_roof3').prop('checked', true);
			}
			
			/** panoramic_roof **/
			if(result.dataTwo.panoramic_roof == 1)
			{
				$('#panoramic_roof1').prop('checked', true);
			}
			else if(result.dataTwo.panoramic_roof == 2)
			{
				$('#panoramic_roof2').prop('checked', true);
				$('#panoramic_roof_cmnt').show();
				$('#panoramic_roof_cmnt').val(result.dataTwo.panoramic_roof_cmnt);
			}
			else 
			{
				$('#panoramic_roof3').prop('checked', true);
			}
			
			/** cool_box **/
			if(result.dataTwo.cool_box == 1)
			{
				$('#cool_box1').prop('checked', true);
			}
			else if(result.dataTwo.cool_box == 2)
			{
				$('#cool_box2').prop('checked', true);
				$('#cool_box_cmnt').show();
				$('#cool_box_cmnt').val(result.dataTwo.cool_box_cmnt);
			}
			else 
			{
				$('#cool_box3').prop('checked', true);
			}
			
			/** seat_heated_front **/
			if(result.dataTwo.seat_heated_front == 1)
			{
				$('#seat_heated_front1').prop('checked', true);
			}
			else if(result.dataTwo.seat_heated_front == 2)
			{
				$('#seat_heated_front2').prop('checked', true);
				$('#seat_heated_front_cmnt').show();
				$('#seat_heated_front_cmnt').val(result.dataTwo.seat_heated_front_cmnt);
			}
			else 
			{
				$('#seat_heated_front3').prop('checked', true);
			}
			
			/** auto_park **/
			if(result.dataTwo.auto_park == 1)
			{
				$('#auto_park1').prop('checked', true);
			}
			else if(result.dataTwo.auto_park == 2)
			{
				$('#auto_park2').prop('checked', true);
				$('#auto_park_cmnt').show();
				$('#auto_park_cmnt').val(result.dataTwo.auto_park_cmnt);
			}
			else 
			{
				$('#auto_park3').prop('checked', true);
			}
			
			/** remote_start_engine **/
			if(result.dataTwo.remote_start_engine == 1)
			{
				$('#remote_start_engine1').prop('checked', true);
			}
			else if(result.dataTwo.remote_start_engine == 2)
			{
				$('#remote_start_engine2').prop('checked', true);
				$('#remote_start_engine_cmnt').show();
				$('#remote_start_engine_cmnt').val(result.dataTwo.remote_start_engine_cmnt);
			}
			else 
			{
				$('#remote_start_engine3').prop('checked', true);
			}

			/** soft_close_doors **/
			if(result.dataTwo.soft_close_doors == 1)
			{
				$('#soft_close_doors1').prop('checked', true);
			}
			else if(result.dataTwo.soft_close_doors == 2)
			{
				$('#soft_close_doors2').prop('checked', true);
				$('#soft_close_doors_cmnt').show();
				$('#soft_close_doors_cmnt').val(result.dataTwo.soft_close_doors_cmnt);
			}
			else 
			{
				$('#soft_close_doors3').prop('checked', true);
			}
			
			/** adaptive_lights **/
			if(result.dataTwo.adaptive_lights == 1)
			{
				$('#adaptive_lights1').prop('checked', true);
			}
			else if(result.dataTwo.adaptive_lights == 2)
			{
				$('#adaptive_lights2').prop('checked', true);
				$('#adaptive_lights_cmnt').show();
				$('#adaptive_lights_cmnt').val(result.dataTwo.adaptive_lights_cmnt);
			}
			else 
			{
				$('#adaptive_lights3').prop('checked', true);
			}
			
			/** night_vision **/
			if(result.dataTwo.night_vision == 1)
			{
				$('#night_vision1').prop('checked', true);
			}
			else if(result.dataTwo.night_vision == 2)
			{
				$('#night_vision2').prop('checked', true);
				$('#night_vision_cmnt').show();
				$('#night_vision_cmnt').val(result.dataTwo.night_vision_cmnt);
			}
			else 
			{
				$('#night_vision3').prop('checked', true);
			}
			
			/** captain_rear_seats **/
			if(result.dataTwo.captain_rear_seats == 1)
			{
				$('#captain_rear_seats1').prop('checked', true);
			}
			else if(result.dataTwo.captain_rear_seats == 2)
			{
				$('#captain_rear_seats2').prop('checked', true);
				$('#captain_rear_seats_cmnt').show();
				$('#captain_rear_seats_cmnt').val(result.dataTwo.captain_rear_seats_cmnt);
			}
			else 
			{
				$('#captain_rear_seats3').prop('checked', true);
			}

			/** leather_seats **/
			if(result.dataTwo.leather_seats == 1)
			{
				$('#leather_seats1').prop('checked', true);
			}
			else if(result.dataTwo.leather_seats == 2)
			{
				$('#leather_seats2').prop('checked', true);
				$('#leather_seats_cmnt').show();
				$('#leather_seats_cmnt').val(result.dataTwo.leather_seats_cmnt);
			}
			else 
			{
				$('#leather_seats3').prop('checked', true);
			}
			
			/** leather_fabric **/
			if(result.dataTwo.leather_fabric == 1)
			{
				$('#leather_fabric1').prop('checked', true);
			}
			else if(result.dataTwo.leather_fabric == 2)
			{
				$('#leather_fabric2').prop('checked', true);
				$('#leather_fabric_cmnt').show();
				$('#leather_fabric_cmnt').val(result.dataTwo.leather_fabric_cmnt);
			}
			else 
			{
				$('#leather_fabric3').prop('checked', true);
			}
			
			/** body_kit **/
			if(result.dataTwo.body_kit == 1)
			{
				$('#body_kit1').prop('checked', true);
			}
			else if(result.dataTwo.body_kit == 2)
			{
				$('#body_kit2').prop('checked', true);
				$('#body_kit_cmnt').show();
				$('#body_kit_cmnt').val(result.dataTwo.body_kit_cmnt);
			}
			else 
			{
				$('#body_kit3').prop('checked', true);
			}

			/** lift_kit **/
			if(result.dataTwo.lift_kit == 1)
			{
				$('#lift_kit1').prop('checked', true);
			}
			else if(result.dataTwo.lift_kit == 2)
			{
				$('#lift_kit2').prop('checked', true);
				$('#lift_kit_cmnt').show();
				$('#lift_kit_cmnt').val(result.dataTwo.lift_kit_cmnt);
			}
			else 
			{
				$('#lift_kit3').prop('checked', true);
			}
			
			/** front_spoiler **/
			if(result.dataTwo.front_spoiler == 1)
			{
				$('#front_spoiler1').prop('checked', true);
			}
			else if(result.dataTwo.front_spoiler == 2)
			{
				$('#front_spoiler2').prop('checked', true);
				$('#front_spoiler_cmnt').show();
				$('#front_spoiler_cmnt').val(result.dataTwo.front_spoiler_cmnt);
			}
			else 
			{
				$('#front_spoiler3').prop('checked', true);
			}
			
			/** rear_spoiler **/
			if(result.dataTwo.rear_spoiler == 1)
			{
				$('#rear_spoiler1').prop('checked', true);
			}
			else if(result.dataTwo.rear_spoiler == 2)
			{
				$('#rear_spoiler2').prop('checked', true);
				$('#rear_spoiler_cmnt').show();
				$('#rear_spoiler_cmnt').val(result.dataTwo.rear_spoiler_cmnt);
			}
			else 
			{
				$('#rear_spoiler3').prop('checked', true);
			}
			
			/** fog_light_front **/
			if(result.dataTwo.fog_light_front == 1)
			{
				$('#fog_light_front1').prop('checked', true);
			}
			else if(result.dataTwo.fog_light_front == 2)
			{
				$('#fog_light_front2').prop('checked', true);
				$('#fog_light_front_cmnt').show();
				$('#fog_light_front_cmnt').val(result.dataTwo.fog_light_front_cmnt);
			}
			else 
			{
				$('#fog_light_front3').prop('checked', true);
			}
			
			/** roof_carrier **/
			if(result.dataTwo.roof_carrier == 1)
			{
				$('#roof_carrier1').prop('checked', true);
			}
			else if(result.dataTwo.roof_carrier == 2)
			{
				$('#roof_carrier2').prop('checked', true);
				$('#roof_carrier_cmnt').show();
				$('#roof_carrier_cmnt').val(result.dataTwo.roof_carrier_cmnt);
			}
			else 
			{
				$('#roof_carrier3').prop('checked', true);
			}
			
			/** halogen_headlight **/
			if(result.dataTwo.halogen_headlight == 1)
			{
				$('#halogen_headlight1').prop('checked', true);
			}
			else if(result.dataTwo.halogen_headlight == 2)
			{
				$('#halogen_headlight2').prop('checked', true);
				$('#halogen_headlight_cmnt').show();
				$('#halogen_headlight_cmnt').val(result.dataTwo.halogen_headlight_cmnt);
			}
			else 
			{
				$('#halogen_headlight3').prop('checked', true);
			}
			
			/** led_headlight **/
			if(result.dataTwo.led_headlight == 1)
			{
				$('#led_headlight1').prop('checked', true);
			}
			else if(result.dataTwo.led_headlight == 2)
			{
				$('#led_headlight2').prop('checked', true);
				$('#led_headlight_cmnt').show();
				$('#led_headlight_cmnt').val(result.dataTwo.led_headlight_cmnt);
			}
			else 
			{
				$('#led_headlight3').prop('checked', true);
			}
			
			/** xenon_headlight **/
			if(result.dataTwo.xenon_headlight == 1)
			{
				$('#xenon_headlight1').prop('checked', true);
			}
			else if(result.dataTwo.xenon_headlight == 2)
			{
				$('#xenon_headlight2').prop('checked', true);
				$('#xenon_headlight_cmnt').show();
				$('#xenon_headlight_cmnt').val(result.dataTwo.xenon_headlight_cmnt);
			}
			else 
			{
				$('#xenon_headlight3').prop('checked', true);
			}
			
			/** trailer_hook_coupling **/
			if(result.dataTwo.trailer_hook_coupling == 1)
			{
				$('#trailer_hook_coupling1').prop('checked', true);
			}
			else if(result.dataTwo.trailer_hook_coupling == 2)
			{
				$('#trailer_hook_coupling2').prop('checked', true);
				$('#trailer_hook_coupling_cmnt').show();
				$('#trailer_hook_coupling_cmnt').val(result.dataTwo.trailer_hook_coupling_cmnt);
			}
			else 
			{
				$('#trailer_hook_coupling3').prop('checked', true);
			}

			/* Aftermarket Added Accessories */ 
			if(result.dataOne.winch == 1)
			{
				$('#winch1').prop('checked', true);
			}
			else if(result.dataOne.winch == 2)
			{
				$('#winch2').prop('checked', true);
				$('#winch_cmnt').show();
				$('#winch_cmnt').val(result.dataOne.winch_cmnt);
			}
			else 
			{
				$('#winch3').prop('checked', true);
			}
			
			/* Body Kit */ 
			if(result.dataOne.body_kit_aaa == 1)
			{
				$('#body_kit_aaa1').prop('checked', true);
			}
			else if(result.dataOne.body_kit_aaa == 2)
			{
				$('#body_kit_aaa2').prop('checked', true);
				$('#body_kit_aaa_cmnt').show();
				$('#body_kit_aaa_cmnt').val(result.dataOne.body_kit_aaa_cmnt);
			}
			else 
			{
				$('#body_kit_aaa3').prop('checked', true);
			}
			
			/* Lift Kit */ 
			if(result.dataOne.lift_kit_aaa == 1)
			{
				$('#lift_kit_aaa1').prop('checked', true);
			}
			else if(result.dataOne.lift_kit_aaa == 2)
			{
				$('#lift_kit_aaa2').prop('checked', true);
				$('#lift_kit_aaa_cmnt').show();
				$('#lift_kit_aaa_cmnt').val(result.dataOne.lift_kit_aaa_cmnt);
			}
			else 
			{
				$('#lift_kit_aaa3').prop('checked', true);
			}
			
			/* Leather Seats */ 
			if(result.dataOne.leather_seats_aaa == 1)
			{
				$('#leather_seats_aaa1').prop('checked', true);
			}
			else if(result.dataOne.leather_seats_aaa == 2)
			{
				$('#leather_seats_aaa2').prop('checked', true);
				$('#leather_seats_aaa_cmnt').show();
				$('#leather_seats_aaa_cmnt').val(result.dataOne.leather_seats_aaa_cmnt);
			}
			else 
			{
				$('#leather_seats_aaa3').prop('checked', true);
			}
			
			/* Rear Seat Entertainment System */ 
			if(result.dataOne.rear_seat_enter_sys_aaa == 1)
			{
				$('#rear_seat_enter_sys_aaa1').prop('checked', true);
			}
			else if(result.dataOne.rear_seat_enter_sys_aaa == 2)
			{
				$('#rear_seat_enter_sys_aaa2').prop('checked', true);
				$('#rear_seat_enter_sys_aaa_cmnt').show();
				$('#rear_seat_enter_sys_aaa_cmnt').val(result.dataOne.rear_seat_enter_sys_aaa_cmnt);
			}
			else 
			{
				$('#rear_seat_enter_sys_aaa3').prop('checked', true);
			}
			
			/* Parking Sensors */ 
			if(result.dataOne.parking_sensors == 1)
			{
				$('#parking_sensors1').prop('checked', true);
			}
			else if(result.dataOne.parking_sensors == 2)
			{
				$('#parking_sensors2').prop('checked', true);
				$('#parking_sensors_cmnt').show();
				$('#parking_sensors_cmnt').val(result.dataOne.parking_sensors_cmnt);
			}
			else 
			{
				$('#parking_sensors3').prop('checked', true);
			}
			
			/* Rear View Camera */ 
			if(result.dataOne.rear_view_camera_aaa == 1)
			{
				$('#rear_view_camera_aaa1').prop('checked', true);
			}
			else if(result.dataOne.rear_view_camera_aaa == 2)
			{
				$('#rear_view_camera_aaa2').prop('checked', true);
				$('#rear_view_camera_aaa_cmnt').show();
				$('#rear_view_camera_aaa_cmnt').val(result.dataOne.rear_view_camera_aaa_cmnt);
			}
			else 
			{
				$('#rear_view_camera_aaa3').prop('checked', true);
			}
			
			/* Navigation */ 
			if(result.dataOne.navigation_aaa == 1)
			{
				$('#navigation_aaa1').prop('checked', true);
			}
			else if(result.dataOne.navigation_aaa == 2)
			{
				$('#navigation_aaa2').prop('checked', true);
				$('#navigation_aaa_cmnt').show();
				$('#navigation_aaa_cmnt').val(result.dataOne.navigation_aaa_cmnt);
			}
			else 
			{
				$('#navigation_aaa3').prop('checked', true);
			}
			
			/* Fire Extinguisher */ 
			if(result.dataOne.fire_extinguisher == 1)
			{
				$('#fire_extinguisher1').prop('checked', true);
			}
			else if(result.dataOne.fire_extinguisher == 2)
			{
				$('#fire_extinguisher2').prop('checked', true);
				$('#fire_extinguisher_cmnt').show();
				$('#fire_extinguisher_cmnt').val(result.dataOne.fire_extinguisher_cmnt);
			}
			else 
			{
				$('#fire_extinguisher3').prop('checked', true);
			}
			
			/*********** Vehicle Specifications End ***********/  
			
			/*********** Inspection Checklist Start ***********/  
			
			/*######## Exterior ########*/
			/* Door locks / operation */ 
			if(result.checklistOne.door_locks_operation == 1)
			{
				$('#door_locks_operation1').prop('checked', true);
			}
			else if(result.checklistOne.door_locks_operation == 2)
			{
				$('#door_locks_operation2').prop('checked', true);
				$('#door_locks_operation_cmnt').show();
				$('#door_locks_operation_cmnt').val(result.checklistOne.door_locks_operation_cmnt);
			}
			else 
			{
				$('#door_locks_operation3').prop('checked', true);
			}
			
			/* Fuel Filler Cover Petrol */ 
			if(result.checklistOne.fuel_filler_cover_petrol == 1)
			{
				$('#fuel_filler_cover_petrol1').prop('checked', true);
			}
			else if(result.checklistOne.fuel_filler_cover_petrol == 2)
			{
				$('#fuel_filler_cover_petrol2').prop('checked', true);
				$('#fuel_filler_cover_petrol_cmnt').show();
				$('#fuel_filler_cover_petrol_cmnt').val(result.checklistOne.fuel_filler_cover_petrol_cmnt);
			}
			else 
			{
				$('#fuel_filler_cover_petrol3').prop('checked', true);
			}
			
			/* Glass */  
			if(result.checklistOne.glass == 1)
			{
				$('#glass1').prop('checked', true);
			}
			else if(result.checklistOne.glass == 2)
			{
				$('#glass2').prop('checked', true);
				$('#glass_cmnt').show();
				$('#glass_cmnt').val(result.checklistOne.glass_cmnt);
			}
			else 
			{
				$('#glass3').prop('checked', true);
			}
			
			/* molding */  
			if(result.checklistOne.molding == 1)
			{
				$('#molding1').prop('checked', true);
			}
			else if(result.checklistOne.molding == 2)
			{
				$('#molding2').prop('checked', true);
				$('#molding_cmnt').show();
				$('#molding_cmnt').val(result.checklistOne.molding_cmnt);
			}
			else 
			{
				$('#molding3').prop('checked', true);
			}
			
			/* bumper_grills */  
			if(result.checklistOne.bumper_grills == 1)
			{
				$('#bumper_grills1').prop('checked', true);
			}
			else if(result.checklistOne.bumper_grills == 2)
			{
				$('#bumper_grills2').prop('checked', true);
				$('#bumper_grills_cmnt').show();
				$('#bumper_grills_cmnt').val(result.checklistOne.bumper_grills_cmnt);
			}
			else 
			{
				$('#bumper_grills3').prop('checked', true);
			}
			
			/* front_bumper */  
			if(result.checklistOne.front_bumper == 1)
			{
				$('#front_bumper1').prop('checked', true);
			}
			else if(result.checklistOne.front_bumper == 2)
			{
				$('#front_bumper2').prop('checked', true);
				$('#front_bumper_cmnt').show();
				$('#front_bumper_cmnt').val(result.checklistOne.front_bumper_cmnt);
			}
			else 
			{
				$('#front_bumper3').prop('checked', true);
			}
			
			/* rear_bumper */  
			if(result.checklistOne.rear_bumper == 1)
			{
				$('#rear_bumper1').prop('checked', true);
			}
			else if(result.checklistOne.rear_bumper == 2)
			{
				$('#rear_bumper2').prop('checked', true);
				$('#rear_bumper_cmnt').show();
				$('#rear_bumper_cmnt').val(result.checklistOne.rear_bumper_cmnt);
			}
			else 
			{
				$('#rear_bumper3').prop('checked', true);
			}
			
			/* front_left_headlights */  
			if(result.checklistOne.front_left_headlights == 1)
			{
				$('#front_left_headlights1').prop('checked', true);
			}
			else if(result.checklistOne.front_left_headlights == 2)
			{
				$('#front_left_headlights2').prop('checked', true);
				$('#front_left_headlights_cmnt').show();
				$('#front_left_headlights_cmnt').val(result.checklistOne.front_left_headlights_cmnt);
			}
			else 
			{
				$('#front_left_headlights3').prop('checked', true);
			}
			
			/* front_right_headlights */  
			if(result.checklistOne.front_right_headlights == 1)
			{
				$('#front_right_headlights1').prop('checked', true);
			}
			else if(result.checklistOne.front_right_headlights == 2)
			{
				$('#front_right_headlights2').prop('checked', true);
				$('#front_right_headlights_cmnt').show();
				$('#front_right_headlights_cmnt').val(result.checklistOne.front_right_headlights_cmnt);
			}
			else 
			{
				$('#front_right_headlights3').prop('checked', true);
			}
			
			/* rear_left_tail_lights */  
			if(result.checklistOne.rear_left_tail_lights == 1)
			{
				$('#rear_left_tail_lights1').prop('checked', true);
			}
			else if(result.checklistOne.rear_left_tail_lights == 2)
			{
				$('#rear_left_tail_lights2').prop('checked', true);
				$('#rear_left_tail_lights_cmnt').show();
				$('#rear_left_tail_lights_cmnt').val(result.checklistOne.rear_left_tail_lights_cmnt);
			}
			else 
			{
				$('#rear_left_tail_lights3').prop('checked', true);
			}
			
			/* rear_right_tail_lights */  
			if(result.checklistOne.rear_right_tail_lights == 1)
			{
				$('#rear_right_tail_lights1').prop('checked', true);
			}
			else if(result.checklistOne.rear_right_tail_lights == 2)
			{
				$('#rear_right_tail_lights2').prop('checked', true);
				$('#rear_right_tail_lights_cmnt').show();
				$('#rear_right_tail_lights_cmnt').val(result.checklistOne.rear_right_tail_lights_cmnt);
			}
			else 
			{
				$('#rear_right_tail_lights3').prop('checked', true);
			}
			
			/* General Body Condition */ 
			if(result.checklistOne.general_body_condition == 1)
			{
				$('#general_body_condition1').prop('checked', true);
			}
			else if(result.checklistOne.general_body_condition == 2)
			{
				$('#general_body_condition2').prop('checked', true);
				$('#general_body_condition_cmnt').show();
				$('#general_body_condition_cmnt').val(result.checklistOne.general_body_condition_cmnt);
			}
			else 
			{
				$('#general_body_condition3').prop('checked', true);
			}
			
			/* Seat belts */ 
			if(result.checklistOne.seat_belts == 1)
			{
				$('#seat_belts1').prop('checked', true);
			}
			else if(result.checklistOne.seat_belts == 2)
			{
				$('#seat_belts2').prop('checked', true);
				$('#seat_belts_cmnt').show();
				$('#seat_belts_cmnt').val(result.checklistOne.seat_belts_cmnt);
			}
			else 
			{
				$('#seat_belts3').prop('checked', true);
			}
			
			/* Headliner */ 
			if(result.checklistOne.headliner == 1)
			{
				$('#headliner1').prop('checked', true);
			}
			else if(result.checklistOne.headliner == 2)
			{
				$('#headliner2').prop('checked', true);
				$('#headliner_cmnt').show();
				$('#headliner_cmnt').val(result.checklistOne.headliner_cmnt);
			}
			else 
			{
				$('#headliner3').prop('checked', true);
			}
			
			/* Rearview mirror */ 
			if(result.checklistOne.rearview_mirror == 1)
			{
				$('#rearview_mirror1').prop('checked', true);
			}
			else if(result.checklistOne.rearview_mirror == 2)
			{
				$('#rearview_mirror2').prop('checked', true);
				$('#rearview_mirror_cmnt').show();
				$('#rearview_mirror_cmnt').val(result.checklistOne.rearview_mirror_cmnt);
			}
			else 
			{
				$('#rearview_mirror3').prop('checked', true);
			}
			
			/* Steering wheel */ 
			if(result.checklistOne.steering_wheel == 1)
			{
				$('#steering_wheel1').prop('checked', true);
			}
			else if(result.checklistOne.steering_wheel == 2)
			{
				$('#steering_wheel2').prop('checked', true);
				$('#steering_wheel_cmnt').show();
				$('#steering_wheel_cmnt').val(result.checklistOne.steering_wheel_cmnt);
			}
			else 
			{
				$('#steering_wheel3').prop('checked', true);
			}
			
			/* Gear lever */ 
			if(result.checklistOne.gear_lever == 1)
			{
				$('#gear_lever1').prop('checked', true);
			}
			else if(result.checklistOne.gear_lever == 2)
			{
				$('#gear_lever2').prop('checked', true);
				$('#gear_lever_cmnt').show();
				$('#gear_lever_cmnt').val(result.checklistOne.gear_lever_cmnt);
			}
			else 
			{
				$('#gear_lever3').prop('checked', true);
			}
			
			/* Sun visor */ 
			if(result.checklistOne.sun_visor == 1)
			{
				$('#sun_visor1').prop('checked', true);
			}
			else if(result.checklistOne.sun_visor == 2)
			{
				$('#sun_visor2').prop('checked', true);
				$('#sun_visor_cmnt').show();
				$('#sun_visor_cmnt').val(result.checklistOne.sun_visor_cmnt);
			}
			else 
			{
				$('#sun_visor3').prop('checked', true);
			}
			
			/* Pillar trim */ 
			if(result.checklistOne.pillar_trim == 1)
			{
				$('#pillar_trim1').prop('checked', true);
			}
			else if(result.checklistOne.pillar_trim == 2)
			{
				$('#pillar_trim2').prop('checked', true);
				$('#pillar_trim_cmnt').show();
				$('#pillar_trim_cmnt').val(result.checklistOne.pillar_trim_cmnt);
			}
			else 
			{
				$('#pillar_trim3').prop('checked', true);
			}
			
			/* Armrest console */ 
			if(result.checklistOne.armrest_console == 1)
			{
				$('#armrest_console1').prop('checked', true);
			}
			else if(result.checklistOne.armrest_console == 2)
			{
				$('#armrest_console2').prop('checked', true);
				$('#armrest_console_cmnt').show();
				$('#armrest_console_cmnt').val(result.checklistOne.armrest_console_cmnt);
			}
			else 
			{
				$('#armrest_console3').prop('checked', true);
			}
			
			/* floor_mats_carpets */ 
			if(result.checklistOne.floor_mats_carpets == 1)
			{
				$('#floor_mats_carpets1').prop('checked', true);
			}
			else if(result.checklistOne.floor_mats_carpets == 2)
			{
				$('#floor_mats_carpets2').prop('checked', true);
				$('#floor_mats_carpets_cmnt').show();
				$('#floor_mats_carpets_cmnt').val(result.checklistOne.floor_mats_carpets_cmnt);
			}
			else 
			{
				$('#floor_mats_carpets3').prop('checked', true);
			}
			
			/* trunk_liner */ 
			if(result.checklistOne.trunk_liner == 1)
			{
				$('#trunk_liner1').prop('checked', true);
			}
			else if(result.checklistOne.trunk_liner == 2)
			{
				$('#trunk_liner2').prop('checked', true);
				$('#trunk_liner_cmnt').show();
				$('#trunk_liner_cmnt').val(result.checklistOne.trunk_liner_cmnt);
			}
			else 
			{
				$('#trunk_liner3').prop('checked', true);
			}
			
			/* dashboard */ 
			if(result.checklistOne.dashboard == 1)
			{
				$('#dashboard1').prop('checked', true);
			}
			else if(result.checklistOne.dashboard == 2)
			{
				$('#dashboard2').prop('checked', true);
				$('#dashboard_cmnt').show();
				$('#dashboard_cmnt').val(result.checklistOne.dashboard_cmnt);
			}
			else 
			{
				$('#dashboard3').prop('checked', true);
			}
			
			/* glove_compartment */ 
			if(result.checklistOne.glove_compartment == 1)
			{
				$('#glove_compartment1').prop('checked', true);
			}
			else if(result.checklistOne.glove_compartment == 2)
			{
				$('#glove_compartment2').prop('checked', true);
				$('#glove_compartment_cmnt').show();
				$('#glove_compartment_cmnt').val(result.checklistOne.glove_compartment_cmnt);
			}
			else 
			{
				$('#glove_compartment3').prop('checked', true);
			}
			
			/* seats */ 
			if(result.checklistOne.seats == 1)
			{
				$('#seats1').prop('checked', true);
			}
			else if(result.checklistOne.seats == 2)
			{
				$('#seats2').prop('checked', true);
				$('#seats_cmnt').show();
				$('#seats_cmnt').val(result.checklistOne.seats_cmnt);
			}
			else 
			{
				$('#seats3').prop('checked', true);
			}
			
			/* door_trims */ 
			if(result.checklistOne.door_trims == 1)
			{
				$('#door_trims1').prop('checked', true);
			}
			else if(result.checklistOne.door_trims == 2)
			{
				$('#door_trims2').prop('checked', true);
				$('#door_trims_cmnt').show();
				$('#door_trims_cmnt').val(result.checklistOne.door_trims_cmnt);
			}
			else 
			{
				$('#door_trims3').prop('checked', true);
			}
			
			/* ac_grills */ 
			if(result.checklistOne.ac_grills == 1)
			{
				$('#ac_grills1').prop('checked', true);
			}
			else if(result.checklistOne.ac_grills == 2)
			{
				$('#ac_grills2').prop('checked', true);
				$('#ac_grills_cmnt').show();
				$('#ac_grills_cmnt').val(result.checklistOne.ac_grills_cmnt);
			}
			else 
			{
				$('#ac_grills3').prop('checked', true);
			}
			
			/* sunroof_shade_liner */ 
			if(result.checklistOne.sunroof_shade_liner == 1)
			{
				$('#sunroof_shade_liner1').prop('checked', true);
			}
			else if(result.checklistOne.sunroof_shade_liner == 2)
			{
				$('#sunroof_shade_liner2').prop('checked', true);
				$('#sunroof_shade_liner_cmnt').show();
				$('#sunroof_shade_liner_cmnt').val(result.checklistOne.sunroof_shade_liner_cmnt);
			}
			else 
			{
				$('#sunroof_shade_liner3').prop('checked', true);
			}
		 
			/*######## Tyre ########*/
			/* Spare Tyre */ 
			if(result.checklistOne.spare_tyre == 1)
			{
				$('#spare_tyre1').prop('checked', true);
			}
			else if(result.checklistOne.spare_tyre == 2)
			{
				$('#spare_tyre2').prop('checked', true);
				$('#spare_tyre_cmnt').show();
				$('#spare_tyre_cmnt').val(result.checklistOne.spare_tyre_cmnt);
			}
			else 
			{
				$('#spare_tyre3').prop('checked', true);
			}
			
			/* Front Left Tyre */ 
			if(result.checklistOne.front_left_tyre == 1)
			{
				$('#front_left_tyre1').prop('checked', true);
			}
			else if(result.checklistOne.front_left_tyre == 2)
			{
				$('#front_left_tyre2').prop('checked', true);
				$('#front_left_tyre_cmnt').show();
				$('#front_left_tyre_cmnt').val(result.checklistOne.front_left_tyre_cmnt);
			}
			else 
			{
				$('#front_left_tyre3').prop('checked', true);
			}
			
			/* Back Right Tyre */ 
			if(result.checklistOne.back_right_tyre == 1)
			{
				$('#back_right_tyre1').prop('checked', true);
			}
			else if(result.checklistOne.back_right_tyre == 2)
			{
				$('#back_right_tyre2').prop('checked', true);
				$('#back_right_tyre_cmnt').show();
				$('#back_right_tyre_cmnt').val(result.checklistOne.back_right_tyre_cmnt);
			}
			else 
			{
				$('#back_right_tyre3').prop('checked', true);
			}
			
			/* Front Right Tyre */ 
			if(result.checklistOne.front_right_tyre == 1)
			{
				$('#front_right_tyre1').prop('checked', true);
			}
			else if(result.checklistOne.front_right_tyre == 2)
			{
				$('#front_right_tyre2').prop('checked', true);
				$('#front_right_tyre_cmnt').show();
				$('#front_right_tyre_cmnt').val(result.checklistOne.front_right_tyre_cmnt);
			}
			else 
			{
				$('#front_right_tyre3').prop('checked', true);
			}
			
			/* Back Left Tyre */ 
			if(result.checklistOne.back_left_tyre == 1)
			{
				$('#back_left_tyre1').prop('checked', true);
			}
			else if(result.checklistOne.back_left_tyre == 2)
			{
				$('#back_left_tyre2').prop('checked', true);
				$('#back_left_tyre_cmnt').show();
				$('#back_left_tyre_cmnt').val(result.checklistOne.back_left_tyre_cmnt);
			}
			else 
			{
				$('#back_left_tyre3').prop('checked', true);
			}
			
			/*######## Engine ########*/
			/* Coolant level */ 
			if(result.checklistOne.coolant_level == 1)
			{
				$('#coolant_level1').prop('checked', true);
			}
			else if(result.checklistOne.coolant_level == 2)
			{
				$('#coolant_level2').prop('checked', true);
				$('#coolant_level_cmnt').show();
				$('#coolant_level_cmnt').val(result.checklistOne.coolant_level_cmnt);
			}
			else 
			{
				$('#coolant_level3').prop('checked', true);
			}
			
			/* Coolant leaks */ 
			if(result.checklistOne.coolant_leaks == 1)
			{
				$('#coolant_leaks1').prop('checked', true);
			}
			else if(result.checklistOne.coolant_leaks == 2)
			{
				$('#coolant_leaks2').prop('checked', true);
				$('#coolant_leaks_cmnt').show(); 
				$('#coolant_leaks_cmnt').val(result.checklistOne.coolant_leaks_cmnt);
			}
			else 
			{
				$('#coolant_leaks3').prop('checked', true);
			}
			
			/* Steering Fluid */ 
			if(result.checklistOne.steering_fluid == 1)
			{
				$('#steering_fluid1').prop('checked', true);
			}
			else if(result.checklistOne.steering_fluid == 2)
			{
				$('#steering_fluid2').prop('checked', true);
				$('#steering_fluid_cmnt').show();
				$('#steering_fluid_cmnt').val(result.checklistOne.steering_fluid_cmnt);
			}
			else 
			{
				$('#steering_fluid3').prop('checked', true);
			}
			
			/* Brake master and booster */ 
			if(result.checklistOne.brake_master_booster == 1)
			{
				$('#brake_master_booster1').prop('checked', true);
			}
			else if(result.checklistOne.brake_master_booster == 2)
			{
				$('#brake_master_booster2').prop('checked', true);
				$('#brake_master_booster_cmnt').show();
				$('#brake_master_booster_cmnt').val(result.checklistOne.brake_master_booster_cmnt);
			}
			else 
			{
				$('#brake_master_booster3').prop('checked', true);
			} 
			
			/* Evidence Overheating */ 
			if(result.checklistOne.evidence_overheating == 1)
			{
				$('#evidence_overheating1').prop('checked', true);
			}
			else if(result.checklistOne.evidence_overheating == 2)
			{
				$('#evidence_overheating2').prop('checked', true);
				$('#evidence_overheating_cmnt').show();
				$('#evidence_overheating_cmnt').val(result.checklistOne.evidence_overheating_cmnt);
			}
			else 
			{
				$('#evidence_overheating3').prop('checked', true);
			}
			
			/* Coolant Conditions */ 
			if(result.checklistOne.coolant_conditions == 1)
			{
				$('#coolant_conditions1').prop('checked', true);
			}
			else if(result.checklistOne.coolant_conditions == 2)
			{
				$('#coolant_conditions2').prop('checked', true);
				$('#coolant_conditions_cmnt').show();
				$('#coolant_conditions_cmnt').val(result.checklistOne.coolant_conditions_cmnt);
			}
			else 
			{
				$('#coolant_conditions3').prop('checked', true);
			}
			
			/* Radiator Cap */ 
			if(result.checklistOne.radiator_cap == 1)
			{
				$('#radiator_cap1').prop('checked', true);
			}
			else if(result.checklistOne.radiator_cap == 2)
			{
				$('#radiator_cap2').prop('checked', true);
				$('#radiator_cap_cmnt').show();
				$('#radiator_cap_cmnt').val(result.checklistOne.radiator_cap_cmnt);
			}
			else 
			{
				$('#radiator_cap3').prop('checked', true);
			}
			
			/* Radiator Fan */ 
			if(result.checklistOne.radiator_fan == 1)
			{
				$('#radiator_fan1').prop('checked', true);
			}
			else if(result.checklistOne.radiator_fan == 2)
			{
				$('#radiator_fan2').prop('checked', true);
				$('#radiator_fan_cmnt').show();
				$('#radiator_fan_cmnt').val(result.checklistOne.radiator_fan_cmnt);
			}
			else 
			{
				$('#radiator_fan3').prop('checked', true);
			}
			
			/* fender_liner */ 
			if(result.checklistOne.fender_liner == 1)
			{
				$('#fender_liner1').prop('checked', true);
			}
			else if(result.checklistOne.fender_liner == 2)
			{
				$('#fender_liner2').prop('checked', true);
				$('#fender_liner_cmnt').show();
				$('#fender_liner_cmnt').val(result.checklistOne.fender_liner_cmnt);
			}
			else 
			{
				$('#fender_liner3').prop('checked', true);
			}
			
			/* hoses_pipes */ 
			if(result.checklistOne.hoses_pipes == 1)
			{
				$('#hoses_pipes1').prop('checked', true);
			}
			else if(result.checklistOne.hoses_pipes == 2)
			{
				$('#hoses_pipes2').prop('checked', true);
				$('#hoses_pipes_cmnt').show();
				$('#hoses_pipes_cmnt').val(result.checklistOne.hoses_pipes_cmnt);
			}
			else 
			{
				$('#hoses_pipes3').prop('checked', true);
			}
			  
			/* cable_harnes_connector */ 
			if(result.checklistOne.cable_harnes_connector == 1)
			{
				$('#cable_harnes_connector1').prop('checked', true);
			}
			else if(result.checklistOne.cable_harnes_connector == 2)
			{
				$('#cable_harnes_connector2').prop('checked', true);
				$('#cable_harnes_connector_cmnt').show();
				$('#cable_harnes_connector_cmnt').val(result.checklistOne.cable_harnes_connector_cmnt);
			}
			else 
			{
				$('#cable_harnes_connector3').prop('checked', true);
			}
			
			/* power_steer_fluidlevel */ 
			if(result.checklistOne.power_steer_fluidlevel == 1)
			{
				$('#power_steer_fluidlevel1').prop('checked', true);
			}
			else if(result.checklistOne.power_steer_fluidlevel == 2)
			{
				$('#power_steer_fluidlevel2').prop('checked', true);
				$('#power_steer_fluidlevel_cmnt').show();
				$('#power_steer_fluidlevel_cmnt').val(result.checklistOne.power_steer_fluidlevel_cmnt);
			}
			else 
			{
				$('#power_steer_fluidlevel3').prop('checked', true);
			}
			
			/* engine_oil_level */ 
			if(result.checklistOne.engine_oil_level == 1)
			{
				$('#engine_oil_level1').prop('checked', true);
			}
			else if(result.checklistOne.engine_oil_level == 2)
			{
				$('#engine_oil_level2').prop('checked', true);
				$('#engine_oil_level_cmnt').show();
				$('#engine_oil_level_cmnt').val(result.checklistOne.engine_oil_level_cmnt);
			}
			else 
			{
				$('#engine_oil_level3').prop('checked', true);
			}
			
			/* external_engine_leaks */ 
			if(result.checklistOne.external_engine_leaks == 1)
			{
				$('#external_engine_leaks1').prop('checked', true);
			}
			else if(result.checklistOne.external_engine_leaks == 2)
			{
				$('#external_engine_leaks2').prop('checked', true);
				$('#external_engine_leaks_cmnt').show();
				$('#external_engine_leaks_cmnt').val(result.checklistOne.external_engine_leaks_cmnt);
			}
			else 
			{
				$('#external_engine_leaks3').prop('checked', true);
			}
			
			/* engine_mounts */ 
			if(result.checklistOne.engine_mounts == 1)
			{
				$('#engine_mounts1').prop('checked', true);
			}
			else if(result.checklistOne.engine_mounts == 2)
			{
				$('#engine_mounts2').prop('checked', true);
				$('#engine_mounts_cmnt').show();
				$('#engine_mounts_cmnt').val(result.checklistOne.engine_mounts_cmnt);
			}
			else 
			{
				$('#engine_mounts3').prop('checked', true);
			}
			
			/* turbo_supercharger */ 
			if(result.checklistOne.turbo_supercharger == 1)
			{
				$('#turbo_supercharger1').prop('checked', true);
			}
			else if(result.checklistOne.turbo_supercharger == 2)
			{
				$('#turbo_supercharger2').prop('checked', true);
				$('#turbo_supercharger_cmnt').show();
				$('#turbo_supercharger_cmnt').val(result.checklistOne.turbo_supercharger_cmnt);
			}
			else 
			{
				$('#turbo_supercharger3').prop('checked', true);
			}
			
			/* fuel_pump_pipes */ 
			if(result.checklistOne.fuel_pump_pipes == 1)
			{
				$('#fuel_pump_pipes1').prop('checked', true);
			}
			else if(result.checklistOne.fuel_pump_pipes == 2)
			{
				$('#fuel_pump_pipes2').prop('checked', true);
				$('#fuel_pump_pipes_cmnt').show();
				$('#fuel_pump_pipes_cmnt').val(result.checklistOne.fuel_pump_pipes_cmnt);
			}
			else 
			{
				$('#fuel_pump_pipes3').prop('checked', true);
			}
			
			/* cold_starting */ 
			if(result.checklistOne.cold_starting == 1)
			{
				$('#cold_starting1').prop('checked', true);
			}
			else if(result.checklistOne.cold_starting == 2)
			{
				$('#cold_starting2').prop('checked', true);
				$('#cold_starting_cmnt').show();
				$('#cold_starting_cmnt').val(result.checklistOne.cold_starting_cmnt);
			}
			else 
			{
				$('#cold_starting3').prop('checked', true);
			}
			
			/* fast_idle */ 
			if(result.checklistOne.fast_idle == 1)
			{
				$('#fast_idle1').prop('checked', true);
			}
			else if(result.checklistOne.fast_idle == 2)
			{
				$('#fast_idle2').prop('checked', true);
				$('#fast_idle_cmnt').show();
				$('#fast_idle_cmnt').val(result.checklistOne.fast_idle_cmnt);
			}
			else 
			{
				$('#fast_idle3').prop('checked', true);
			}
			
			/* noise_level */ 
			if(result.checklistOne.noise_level == 1)
			{
				$('#noise_level1').prop('checked', true);
			}
			else if(result.checklistOne.noise_level == 2)
			{
				$('#noise_level2').prop('checked', true);
				$('#noise_level_cmnt').show();
				$('#noise_level_cmnt').val(result.checklistOne.noise_level_cmnt);
			}
			else 
			{
				$('#noise_level3').prop('checked', true);
			}
			
			/* excess_smoke */ 
			if(result.checklistOne.excess_smoke == 1)
			{
				$('#excess_smoke1').prop('checked', true);
			}
			else if(result.checklistOne.excess_smoke == 2)
			{
				$('#excess_smoke2').prop('checked', true);
				$('#excess_smoke_cmnt').show();
				$('#excess_smoke_cmnt').val(result.checklistOne.excess_smoke_cmnt);
			}
			else 
			{
				$('#excess_smoke3').prop('checked', true);
			}
			
			/* inlet_manifold */ 
			if(result.checklistOne.inlet_manifold == 1)
			{
				$('#inlet_manifold1').prop('checked', true);
			}
			else if(result.checklistOne.inlet_manifold == 2)
			{
				$('#inlet_manifold2').prop('checked', true);
				$('#inlet_manifold_cmnt').show();
				$('#inlet_manifold_cmnt').val(result.checklistOne.inlet_manifold_cmnt);
			}
			else 
			{
				$('#inlet_manifold3').prop('checked', true);
			}
			
			/* outlet_manifold */ 
			if(result.checklistOne.outlet_manifold == 1)
			{
				$('#outlet_manifold1').prop('checked', true);
			}
			else if(result.checklistOne.outlet_manifold == 2)
			{
				$('#outlet_manifold2').prop('checked', true);
				$('#outlet_manifold_cmnt').show();
				$('#outlet_manifold_cmnt').val(result.checklistOne.outlet_manifold_cmnt);
			}
			else 
			{
				$('#outlet_manifold3').prop('checked', true);
			}
			
			/* exhaust_pipes */ 
			if(result.checklistOne.exhaust_pipes == 1)
			{
				$('#exhaust_pipes1').prop('checked', true);
			}
			else if(result.checklistOne.exhaust_pipes == 2)
			{
				$('#exhaust_pipes2').prop('checked', true);
				$('#exhaust_pipes_cmnt').show();
				$('#exhaust_pipes_cmnt').val(result.checklistOne.exhaust_pipes_cmnt);
			}
			else 
			{
				$('#exhaust_pipes3').prop('checked', true);
			}
			
			/* silencer */ 
			if(result.checklistOne.silencer == 1)
			{
				$('#silencer1').prop('checked', true);
			}
			else if(result.checklistOne.silencer == 2)
			{
				$('#silencer2').prop('checked', true);
				$('#silencer_cmnt').show();
				$('#silencer_cmnt').val(result.checklistOne.silencer_cmnt);
			}
			else 
			{
				$('#silencer3').prop('checked', true);
			}
			
			/* head_shield_mounting */ 
			if(result.checklistOne.head_shield_mounting == 1)
			{
				$('#head_shield_mounting1').prop('checked', true);
			}
			else if(result.checklistOne.head_shield_mounting == 2)
			{
				$('#head_shield_mounting2').prop('checked', true);
				$('#head_shield_mounting_cmnt').show();
				$('#head_shield_mounting_cmnt').val(result.checklistOne.head_shield_mounting_cmnt);
			}
			else 
			{
				$('#head_shield_mounting3').prop('checked', true);
			}
			
			/* joints_couplings */ 
			if(result.checklistOne.joints_couplings == 1)
			{
				$('#joints_couplings1').prop('checked', true);
			}
			else if(result.checklistOne.joints_couplings == 2)
			{
				$('#joints_couplings2').prop('checked', true);
				$('#joints_couplings_cmnt').show();
				$('#joints_couplings_cmnt').val(result.checklistOne.joints_couplings_cmnt);
			}
			else 
			{
				$('#joints_couplings3').prop('checked', true);
			}
			
			/* engine_underside_leak */ 
			if(result.checklistOne.engine_underside_leak == 1)
			{
				$('#engine_underside_leak1').prop('checked', true);
			}
			else if(result.checklistOne.engine_underside_leak == 2)
			{
				$('#engine_underside_leak2').prop('checked', true);
				$('#engine_underside_leak_cmnt').show();
				$('#engine_underside_leak_cmnt').val(result.checklistOne.engine_underside_leak_cmnt);
			}
			else 
			{
				$('#engine_underside_leak3').prop('checked', true);
			}
			
			/* catalytic_converter */ 
			if(result.checklistOne.catalytic_converter == 1)
			{
				$('#catalytic_converter1').prop('checked', true);
			}
			else if(result.checklistOne.catalytic_converter == 2)
			{
				$('#catalytic_converter2').prop('checked', true);
				$('#catalytic_converter_cmnt').show();
				$('#catalytic_converter_cmnt').val(result.checklistOne.catalytic_converter_cmnt);
			}
			else 
			{
				$('#catalytic_converter3').prop('checked', true);
			}
			
			/* engine_shield */ 
			if(result.checklistOne.engine_shield == 1)
			{
				$('#engine_shield1').prop('checked', true);
			}
			else if(result.checklistOne.engine_shield == 2)
			{
				$('#engine_shield2').prop('checked', true);
				$('#engine_shield_cmnt').show();
				$('#engine_shield_cmnt').val(result.checklistOne.engine_shield_cmnt);
			}
			else 
			{
				$('#engine_shield3').prop('checked', true);
			}
 
			/*######## Transmission ########*/ 
			/* Gear selector */ 
			if(result.checklistTwo.gear_selector == 1)
			{
				$('#gear_selector1').prop('checked', true);
			}
			else if(result.checklistTwo.gear_selector == 2)
			{
				$('#gear_selector2').prop('checked', true);
				$('#gear_selector_cmnt').show();
				$('#gear_selector_cmnt').val(result.checklistTwo.gear_selector_cmnt);
			}
			else 
			{
				$('#gear_selector3').prop('checked', true);
			}
			
			/* Gear shifting */ 
			if(result.checklistTwo.gear_shifting == 1)
			{
				$('#gear_shifting1').prop('checked', true);
			}
			else if(result.checklistTwo.gear_shifting == 2)
			{
				$('#gear_shifting2').prop('checked', true);
				$('#gear_shifting_cmnt').show();
				$('#gear_shifting_cmnt').val(result.checklistTwo.gear_shifting_cmnt);
			}
			else 
			{
				$('#gear_shifting3').prop('checked', true);
			}
			
			/* Transmission Mount */ 
			if(result.checklistTwo.transmission_mount == 1)
			{
				$('#transmission_mount1').prop('checked', true);
			}
			else if(result.checklistTwo.transmission_mount == 2)
			{
				$('#transmission_mount2').prop('checked', true);
				$('#transmission_mount_cmnt').show();
				$('#transmission_mount_cmnt').val(result.checklistTwo.transmission_mount_cmnt);
			}
			else 
			{
				$('#transmission_mount3').prop('checked', true);
			}
			
			/* Gear Nise */ 
			if(result.checklistTwo.gear_noise == 1)
			{
				$('#gear_noise1').prop('checked', true);
			}
			else if(result.checklistTwo.gear_noise == 2)
			{
				$('#gear_noise2').prop('checked', true);
				$('#gear_noise_cmnt').show();
				$('#gear_noise_cmnt').val(result.checklistTwo.gear_noise_cmnt);
			}
			else 
			{
				$('#gear_noise3').prop('checked', true);
			}
			
			/* Fluid Level Oil Leak */ 
			if(result.checklistTwo.fluid_level_oil_leak == 1)
			{
				$('#fluid_level_oil_leak1').prop('checked', true);
			}
			else if(result.checklistTwo.fluid_level_oil_leak == 2)
			{
				$('#fluid_level_oil_leak2').prop('checked', true);
				$('#fluid_level_oil_leak_cmnt').show();
				$('#fluid_level_oil_leak_cmnt').val(result.checklistTwo.fluid_level_oil_leak_cmnt);
			}
			else 
			{
				$('#fluid_level_oil_leak3').prop('checked', true);
			}
			
			/*######## Electrical ########*/  
			/* Door Locks */ 
			if(result.checklistTwo.door_locks == 1)
			{
				$('#door_locks1').prop('checked', true);
			}
			else if(result.checklistTwo.door_locks == 2)
			{
				$('#door_locks2').prop('checked', true);
				$('#door_locks_cmnt').show();
				$('#door_locks_cmnt').val(result.checklistTwo.door_locks_cmnt);
			}
			else 
			{
				$('#door_locks3').prop('checked', true);
			}
			
			/*######## Electrical ########*/  
			/* Central Locking */ 
			if(result.checklistTwo.central_locking == 1)
			{
				$('#central_locking1').prop('checked', true);
			}
			else if(result.checklistTwo.central_locking == 2)
			{
				$('#central_locking2').prop('checked', true);
				$('#central_locking_cmnt').show();
				$('#central_locking_cmnt').val(result.checklistTwo.central_locking_cmnt);
			}
			else 
			{
				$('#central_locking3').prop('checked', true);
			}
			
			/* Ignition lock / Starting system */ 
			if(result.checklistTwo.ignitionlock_startsys == 1)
			{
				$('#ignitionlock_startsys1').prop('checked', true);
			}
			else if(result.checklistTwo.ignitionlock_startsys == 2)
			{
				$('#ignitionlock_startsys2').prop('checked', true);
				$('#ignitionlock_startsys_cmnt').show();
				$('#ignitionlock_startsys_cmnt').val(result.checklistTwo.ignitionlock_startsys_cmnt);
			}
			else 
			{
				$('#ignitionlock_startsys3').prop('checked', true);
			}
			
			/* Instrument panel */ 
			if(result.checklistTwo.instrument_panel == 1)
			{
				$('#instrument_panel1').prop('checked', true);
			}
			else if(result.checklistTwo.instrument_panel == 2)
			{
				$('#instrument_panel2').prop('checked', true);
				$('#instrument_panel_cmnt').show();
				$('#instrument_panel_cmnt').val(result.checklistTwo.instrument_panel_cmnt);
			}
			else 
			{
				$('#instrument_panel3').prop('checked', true);
			}
			
			/* Headlights */ 
			if(result.checklistTwo.headlights == 1)
			{
				$('#headlights1').prop('checked', true);
			}
			else if(result.checklistTwo.headlights == 2)
			{
				$('#headlights2').prop('checked', true);
				$('#headlights_cmnt').show();
				$('#headlights_cmnt').val(result.checklistTwo.headlights_cmnt);
			}
			else 
			{
				$('#headlights3').prop('checked', true);
			}
			
			/* Sidelights / Running lights */ 
			if(result.checklistTwo.sidelights_runlights == 1)
			{
				$('#sidelights_runlights1').prop('checked', true);
			}
			else if(result.checklistTwo.sidelights_runlights == 2)
			{
				$('#sidelights_runlights2').prop('checked', true);
				$('#sidelights_runlights_cmnt').show();
				$('#sidelights_runlights_cmnt').val(result.checklistTwo.sidelights_runlights_cmnt);
			}
			else 
			{
				$('#sidelights_runlights3').prop('checked', true);
			}
			
			/* Rear lights */ 
			if(result.checklistTwo.rear_lights == 1)
			{
				$('#rear_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.rear_lights == 2)
			{
				$('#rear_lights2').prop('checked', true);
				$('#rear_lights_cmnt').show();
				$('#rear_lights_cmnt').val(result.checklistTwo.rear_lights_cmnt);
			}
			else 
			{
				$('#rear_lights3').prop('checked', true);
			}
			
			/* Indicator / Hazard lights */ 
			if(result.checklistTwo.indicator_hazardlights == 1)
			{
				$('#indicator_hazardlights1').prop('checked', true);
			}
			else if(result.checklistTwo.indicator_hazardlights == 2)
			{
				$('#indicator_hazardlights2').prop('checked', true);
				$('#indicator_hazardlights_cmnt').show();
				$('#indicator_hazardlights_cmnt').val(result.checklistTwo.indicator_hazardlights_cmnt);
			}
			else 
			{
				$('#indicator_hazardlights3').prop('checked', true);
			}
			
			/* Boot / Tailgate lock */ 
			if(result.checklistTwo.boot_tailgate_lock == 1)
			{
				$('#boot_tailgate_lock1').prop('checked', true);
			}
			else if(result.checklistTwo.boot_tailgate_lock == 2)
			{
				$('#boot_tailgate_lock2').prop('checked', true);
				$('#boot_tailgate_lock_cmnt').show();
				$('#boot_tailgate_lock_cmnt').val(result.checklistTwo.boot_tailgate_lock_cmnt);
			}
			else 
			{
				$('#boot_tailgate_lock3').prop('checked', true);
			}
			
			/* Reverse lights */ 
			if(result.checklistTwo.reverse_lights == 1)
			{
				$('#reverse_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.reverse_lights == 2)
			{
				$('#reverse_lights2').prop('checked', true);
				$('#reverse_lights_cmnt').show();
				$('#reverse_lights_cmnt').val(result.checklistTwo.reverse_lights_cmnt);
			}
			else 
			{
				$('#reverse_lights3').prop('checked', true);
			}
			
			/* Fog lights */ 
			if(result.checklistTwo.fog_lights == 1)
			{
				$('#fog_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.fog_lights == 2)
			{
				$('#fog_lights2').prop('checked', true);
				$('#fog_lights_cmnt').show();
				$('#fog_lights_cmnt').val(result.checklistTwo.fog_lights_cmnt);
			}
			else 
			{
				$('#fog_lights3').prop('checked', true);
			}
			
			/* Multimedia */ 
			if(result.checklistTwo.multimedia == 1)
			{
				$('#multimedia1').prop('checked', true);
			}
			else if(result.checklistTwo.multimedia == 2)
			{
				$('#multimedia2').prop('checked', true);
				$('#multimedia_cmnt').show();
				$('#multimedia_cmnt').val(result.checklistTwo.multimedia_cmnt);
			}
			else 
			{
				$('#multimedia3').prop('checked', true);
			}
			
			/* A/C Control & Cooling */ 
			if(result.checklistTwo.ac_control_cooling == 1)
			{
				$('#ac_control_cooling1').prop('checked', true);
			}
			else if(result.checklistTwo.ac_control_cooling == 2)
			{
				$('#ac_control_cooling2').prop('checked', true);
				$('#ac_control_cooling_cmnt').show();
				$('#ac_control_cooling_cmnt').val(result.checklistTwo.ac_control_cooling_cmnt);
			}
			else 
			{
				$('#ac_control_cooling3').prop('checked', true);
			}
			
			/* side_mirror */ 
			if(result.checklistTwo.side_mirror == 1)
			{
				$('#side_mirror1').prop('checked', true);
			}
			else if(result.checklistTwo.side_mirror == 2)
			{
				$('#side_mirror2').prop('checked', true);
				$('#side_mirror_cmnt').show();
				$('#side_mirror_cmnt').val(result.checklistTwo.side_mirror_cmnt);
			}
			else 
			{
				$('#side_mirror3').prop('checked', true);
			}
			
			/* auxiliary_lights */ 
			if(result.checklistTwo.auxiliary_lights == 1)
			{
				$('#auxiliary_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.auxiliary_lights == 2)
			{
				$('#auxiliary_lights2').prop('checked', true);
				$('#auxiliary_lights_cmnt').show();
				$('#auxiliary_lights_cmnt').val(result.checklistTwo.auxiliary_lights_cmnt);
			}
			else 
			{
				$('#auxiliary_lights3').prop('checked', true);
			}
			  
			/* panel_lights */ 
			if(result.checklistTwo.panel_lights == 1)
			{
				$('#panel_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.panel_lights == 2)
			{
				$('#panel_lights2').prop('checked', true);
				$('#panel_lights_cmnt').show();
				$('#panel_lights_cmnt').val(result.checklistTwo.panel_lights_cmnt);
			}
			else 
			{
				$('#panel_lights3').prop('checked', true);
			}
			
			/* horn */ 
			if(result.checklistTwo.horn == 1)
			{
				$('#horn1').prop('checked', true);
			}
			else if(result.checklistTwo.horn == 2)
			{
				$('#horn2').prop('checked', true);
				$('#horn_cmnt').show();
				$('#horn_cmnt').val(result.checklistTwo.horn_cmnt);
			}
			else 
			{
				$('#horn3').prop('checked', true);
			}
			
			/* window_operation */ 
			if(result.checklistTwo.window_operation == 1)
			{
				$('#window_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.window_operation == 2)
			{
				$('#window_operation2').prop('checked', true);
				$('#window_operation_cmnt').show();
				$('#window_operation_cmnt').val(result.checklistTwo.window_operation_cmnt);
			}
			else 
			{
				$('#window_operation3').prop('checked', true);
			}
 
			/* sunroof_operation */ 
			if(result.checklistTwo.sunroof_operation == 1)
			{
				$('#sunroof_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.sunroof_operation == 2)
			{
				$('#sunroof_operation2').prop('checked', true);
				$('#sunroof_operation_cmnt').show();
				$('#sunroof_operation_cmnt').val(result.checklistTwo.sunroof_operation_cmnt);
			}
			else 
			{
				$('#sunroof_operation3').prop('checked', true);
			}
			
			/* wipers_jet_washers */ 
			if(result.checklistTwo.wipers_jet_washers == 1)
			{
				$('#wipers_jet_washers1').prop('checked', true);
			}
			else if(result.checklistTwo.wipers_jet_washers == 2)
			{
				$('#wipers_jet_washers2').prop('checked', true);
				$('#wipers_jet_washers_cmnt').show();
				$('#wipers_jet_washers_cmnt').val(result.checklistTwo.wipers_jet_washers_cmnt);
			}
			else 
			{
				$('#wipers_jet_washers3').prop('checked', true);
			}
			
			/* keys_remote_controls */ 
			if(result.checklistTwo.keys_remote_controls == 1)
			{
				$('#keys_remote_controls1').prop('checked', true);
			}
			else if(result.checklistTwo.keys_remote_controls == 2)
			{
				$('#keys_remote_controls2').prop('checked', true);
				$('#keys_remote_controls_cmnt').show();
				$('#keys_remote_controls_cmnt').val(result.checklistTwo.keys_remote_controls_cmnt);
			}
			else 
			{
				$('#keys_remote_controls3').prop('checked', true);
			}
 
			/* warning_lights */ 
			if(result.checklistTwo.warning_lights == 1)
			{
				$('#warning_lights1').prop('checked', true);
			}
			else if(result.checklistTwo.warning_lights == 2)
			{
				$('#warning_lights2').prop('checked', true);
				$('#warning_lights_cmnt').show();
				$('#warning_lights_cmnt').val(result.checklistTwo.warning_lights_cmnt);
			}
			else 
			{
				$('#warning_lights3').prop('checked', true);
			}
			
			/* number_plate_light */ 
			if(result.checklistTwo.number_plate_light == 1)
			{
				$('#number_plate_light1').prop('checked', true);
			}
			else if(result.checklistTwo.number_plate_light == 2)
			{
				$('#number_plate_light2').prop('checked', true);
				$('#number_plate_light_cmnt').show();
				$('#number_plate_light_cmnt').val(result.checklistTwo.number_plate_light_cmnt);
			}
			else 
			{
				$('#number_plate_light3').prop('checked', true);
			}
 
  
			/*######## Underbody ########*/   
			/* Steering joints and ball joints */ 
			if(result.checklistTwo.steering_ball_joints == 1)
			{
				$('#steering_ball_joints1').prop('checked', true);
			}
			else if(result.checklistTwo.steering_ball_joints == 2)
			{
				$('#steering_ball_joints2').prop('checked', true);
				$('#steering_ball_joints_cmnt').show();
				$('#steering_ball_joints_cmnt').val(result.checklistTwo.steering_ball_joints_cmnt);
			}
			else 
			{
				$('#steering_ball_joints3').prop('checked', true);
			}
			
			/* Brakes lines */ 
			if(result.checklistTwo.brakes_lines == 1)
			{
				$('#brakes_lines1').prop('checked', true);
			}
			else if(result.checklistTwo.brakes_lines == 2)
			{
				$('#brakes_lines2').prop('checked', true);
				$('#brakes_lines_cmnt').show();
				$('#brakes_lines_cmnt').val(result.checklistTwo.brakes_lines_cmnt);
			}
			else 
			{
				$('#brakes_lines3').prop('checked', true);
			}
			
			/* Subframe */ 
			if(result.checklistTwo.subframe == 1)
			{
				$('#subframe1').prop('checked', true);
			}
			else if(result.checklistTwo.subframe == 2)
			{
				$('#subframe2').prop('checked', true);
				$('#subframe_cmnt').show();
				$('#subframe_cmnt').val(result.checklistTwo.subframe_cmnt);
			}
			else 
			{
				$('#subframe3').prop('checked', true);
			}
			
			/* Power steering/ Steering rack */ 
			if(result.checklistTwo.power_steering_rack == 1)
			{
				$('#power_steering_rack1').prop('checked', true);
			}
			else if(result.checklistTwo.power_steering_rack == 2)
			{
				$('#power_steering_rack2').prop('checked', true);
				$('#power_steering_rack_cmnt').show();
				$('#power_steering_rack_cmnt').val(result.checklistTwo.power_steering_rack_cmnt);
			}
			else 
			{
				$('#power_steering_rack3').prop('checked', true);
			}
			
			/* Wheels, hubs, and bearings */ 
			if(result.checklistTwo.wheels_hubs_bearings == 1)
			{
				$('#wheels_hubs_bearings1').prop('checked', true);
			}
			else if(result.checklistTwo.wheels_hubs_bearings == 2)
			{
				$('#wheels_hubs_bearings2').prop('checked', true);
				$('#wheels_hubs_bearings_cmnt').show();
				$('#wheels_hubs_bearings_cmnt').val(result.checklistTwo.wheels_hubs_bearings_cmnt);
			}
			else 
			{
				$('#wheels_hubs_bearings3').prop('checked', true);
			}
			
			/* Dampers and bushes */ 
			if(result.checklistTwo.dampers_bushes == 1)
			{
				$('#dampers_bushes1').prop('checked', true);
			}
			else if(result.checklistTwo.dampers_bushes == 2)
			{
				$('#dampers_bushes2').prop('checked', true);
				$('#dampers_bushes_cmnt').show();
				$('#dampers_bushes_cmnt').val(result.checklistTwo.dampers_bushes_cmnt);
			}
			else 
			{
				$('#dampers_bushes3').prop('checked', true);
			}
			
			/* Evidence of floor/chassis corrosion */ 
			if(result.checklistTwo.evidencefloor_chassis == 1)
			{
				$('#evidencefloor_chassis1').prop('checked', true);
			}
			else if(result.checklistTwo.evidencefloor_chassis == 2)
			{
				$('#evidencefloor_chassis2').prop('checked', true);
				$('#evidencefloor_chassis_cmnt').show();
				$('#evidencefloor_chassis_cmnt').val(result.checklistTwo.evidencefloor_chassis_cmnt);
			}
			else 
			{
				$('#evidencefloor_chassis3').prop('checked', true);
			}
			
			/*######## Test Drive ########*/   
			/* Engine - Performance */ 
			if(result.checklistTwo.engine_performance == 1)
			{
				$('#engine_performance1').prop('checked', true);
			}
			else if(result.checklistTwo.engine_performance == 2)
			{
				$('#engine_performance2').prop('checked', true);
				$('#engine_performance_cmnt').show();
				$('#engine_performance_cmnt').val(result.checklistTwo.engine_performance_cmnt);
			}
			else 
			{
				$('#engine_performance3').prop('checked', true);
			}
			
			/* Gearbox Operation */ 
			if(result.checklistTwo.gearbox_operation == 1)
			{
				$('#gearbox_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.gearbox_operation == 2)
			{
				$('#gearbox_operation2').prop('checked', true);
				$('#gearbox_operation_cmnt').show();
				$('#gearbox_operation_cmnt').val(result.checklistTwo.gearbox_operation_cmnt);
			}
			else 
			{
				$('#gearbox_operation3').prop('checked', true);
			}
			
			/* Clutch Operation */ 
			if(result.checklistTwo.clutch_operation == 1)
			{
				$('#clutch_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.clutch_operation == 2)
			{
				$('#clutch_operation2').prop('checked', true);
				$('#clutch_operation_cmnt').show();
				$('#clutch_operation_cmnt').val(result.checklistTwo.clutch_operation_cmnt);
			}
			else 
			{
				$('#clutch_operation3').prop('checked', true);
			}
			
			/* Steering Operation */ 
			if(result.checklistTwo.steering_operation == 1)
			{
				$('#steering_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.steering_operation == 2)
			{
				$('#steering_operation2').prop('checked', true);
				$('#steering_operation_cmnt').show();
				$('#steering_operation_cmnt').val(result.checklistTwo.steering_operation_cmnt);
			}
			else 
			{
				$('#steering_operation3').prop('checked', true);
			}
			
			/* Brake Operation */ 
			if(result.checklistTwo.brake_operation == 1)
			{
				$('#brake_operation1').prop('checked', true);
			}
			else if(result.checklistTwo.brake_operation == 2)
			{
				$('#brake_operation2').prop('checked', true);
				$('#brake_operation_cmnt').show();
				$('#brake_operation_cmnt').val(result.checklistTwo.brake_operation_cmnt);
			}
			else 
			{
				$('#brake_operation3').prop('checked', true);
			}
			
			/* Hand brake/ Parking brake */ 
			if(result.checklistTwo.hand_parking_brake == 1)
			{
				$('#hand_parking_brake1').prop('checked', true);
			}
			else if(result.checklistTwo.hand_parking_brake == 2)
			{
				$('#hand_parking_brake2').prop('checked', true);
				$('#hand_parking_brake_cmnt').show();
				$('#hand_parking_brake_cmnt').val(result.checklistTwo.hand_parking_brake_cmnt);
			}
			else 
			{
				$('#hand_parking_brake3').prop('checked', true);
			}
			
			/* Drive Train (4WD,2WD, AWD) */ 
			if(result.checklistTwo.drive_train == 1)
			{
				$('#drive_train1').prop('checked', true);
			}
			else if(result.checklistTwo.drive_train == 2)
			{
				$('#drive_train2').prop('checked', true);
				$('#drive_train_cmnt').show();
				$('#drive_train_cmnt').val(result.checklistTwo.drive_train_cmnt);
			}
			else 
			{
				$('#drive_train3').prop('checked', true);
			}
			
			/* Instruments and controls functioning */ 
			if(result.checklistTwo.instru_control_func == 1)
			{
				$('#instru_control_func1').prop('checked', true);
			}
			else if(result.checklistTwo.instru_control_func == 2)
			{
				$('#instru_control_func2').prop('checked', true);
				$('#instru_control_func_cmnt').show();
				$('#instru_control_func_cmnt').val(result.checklistTwo.instru_control_func_cmnt);
			}
			else 
			{
				$('#instru_control_func3').prop('checked', true);
			}
			
			/* suspension_noise */ 
			if(result.checklistTwo.suspension_noise == 1)
			{
				$('#suspension_noise1').prop('checked', true);
			}
			else if(result.checklistTwo.suspension_noise == 2)
			{
				$('#suspension_noise2').prop('checked', true);
				$('#suspension_noise_cmnt').show();
				$('#suspension_noise_cmnt').val(result.checklistTwo.suspension_noise_cmnt);
			}
			else 
			{
				$('#suspension_noise3').prop('checked', true);
			}
			
			/* shock_absorber */ 
			if(result.checklistTwo.shock_absorber == 1)
			{
				$('#shock_absorber1').prop('checked', true);
			}
			else if(result.checklistTwo.shock_absorber == 2)
			{
				$('#shock_absorber2').prop('checked', true);
				$('#shock_absorber_cmnt').show();
				$('#shock_absorber_cmnt').val(result.checklistTwo.shock_absorber_cmnt);
			}
			else 
			{
				$('#shock_absorber3').prop('checked', true);
			}  
			
			/* road_holding_stability */ 
			if(result.checklistTwo.road_holding_stability == 1)
			{
				$('#road_holding_stability1').prop('checked', true);
			}
			else if(result.checklistTwo.road_holding_stability == 2)
			{
				$('#road_holding_stability2').prop('checked', true);
				$('#road_holding_stability_cmnt').show();
				$('#road_holding_stability_cmnt').val(result.checklistTwo.road_holding_stability_cmnt);
			}
			else 
			{
				$('#road_holding_stability3').prop('checked', true);
			} 

			/* nois */ 
			if(result.checklistTwo.nois == 1)
			{
				$('#nois1').prop('checked', true);
			}
			else if(result.checklistTwo.nois == 2)
			{
				$('#nois2').prop('checked', true);
				$('#nois_cmnt').show();
				$('#nois_cmnt').val(result.checklistTwo.nois_cmnt);
			}
			else 
			{
				$('#nois3').prop('checked', true);
			} 			
  
			/*********** Inspection Checklist End ***********/ 
			/*********** Inspection Checklist Comment ***********/ 

			/*********** Inspection Summary Start ***********/
			$('#interest_tbody_summary').html('');
			var int_count = result.summary.length;
			var mval  = "<option value=''>-- Summary Type --</option>";
			var mval2 = "<option value=''>-- Summary Desc --</option>";
 
			for(var i=0; i<int_count; i++)
			{
				var new_row_data = i+1;
				
				$('#interest_tbody_summary').append('<tr id="interest_tr_3' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_type" data-input_id="' + new_row_data + '" name="extra_type[]" id="extra_type_' + new_row_data + '" onchange="SummaryDesEdit(this.value,' + new_row_data + ')">'+mval+'</select><div id="notification'+new_row_data+'" style="color: red;font-style: italic;font-size: 12px;"></div></td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_name" data-input_id="' + new_row_data + '" name="extra_name[]" id="extra_name_' + new_row_data + '" onchange="" >'+mval2+'</select></td><td class="prop"><input type="text" class="form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" name="extra_name_ar[]" id="extra_name_ar_' + new_row_data + '" placeholder="Summary Desc in Arabic"></td><td class="remove_new_row_summary center" title="Remove New" onclick="remove_row_interest_summary(' + new_row_data + ')" style="cursor:pointer"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
				
				// $('#interest_tbody_summary').append('<tr id="interest_tr_3' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_type" data-input_id="' + new_row_data + '" name="extra_type[]" id="extra_type_' + new_row_data + '" onchange="" >'+mval+'</select><div id="notification'+new_row_data+'" style="color: red;font-style: italic;font-size: 12px;"></div></td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_name" data-input_id="' + new_row_data + '" name="extra_name[]" id="extra_name_' + new_row_data + '" onchange="" >'+mval2+'</select></td><td class="prop"><input type="text" class="form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" name="extra_name_ar[]" id="extra_name_ar_' + new_row_data + '" placeholder="Summary Desc in Arabic"></td><td class="remove_new_row_summary center" title="Remove  New" onclick="remove_row_interest_summary(' + new_row_data + ')"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
			 
				
				//$('#extra_name_'+new_row_data).val(result.summary[i].insp_summary_desc);
				$('#extra_name_ar_'+new_row_data).val(result.summary[i].insp_summary_desc_ar);
				$('#doc'+new_row_data).val(result.summary[i].id);	
				
				if(result.summary_type.length > 0)
				{  
					mval = " ";  
					for(var j=0; j < result.summary_type.length; j++)
					{
						if(result.summary_type[j].summary_type_id == result.summary[i].insp_summary_type)
						{
							mval += "<option value='"+result.summary_type[j].summary_type_id+"' selected>"+result.summary_type[j].summary_type_name+"</option>";
						}
						else
						{
							mval += "<option value='"+result.summary_type[j].summary_type_id+"'>"+result.summary_type[j].summary_type_name+"</option>";
						}
					}
				}
				$('#extra_type_'+new_row_data).html(mval);
				
				
				if(result.summary_desc.length > 0)
				{  
					var mval2 = "<option value=''>-- Summary Desc --</option>";
					for(var k=0; k < result.summary_desc.length; k++)
					{   
						//console.log(result.summary_desc[k].sum_desc_id); 
						//console.log(result.summary[i].insp_summary_desc_id); 
						
						if(result.summary_desc[k].sum_desc_id == result.summary[i].insp_summary_desc_id)
						{
							mval2 += "<option value='"+result.summary_desc[k].sum_desc_name+"' selected>"+result.summary_desc[k].sum_desc_name+"</option>";
						}
						else
						{
							mval2 += "<option value='"+result.summary_desc[k].sum_desc_name+"'>"+result.summary_desc[k].sum_desc_name+"</option>";
						}
					}
				}
				//$('#extra_name_'+new_row_data).html(mval2).select2();;
				$('#extra_name_' + new_row_data).html(mval2).select2({
					tags: true,
					placeholder: "Summary Desc",
					allowClear: true,
					width: '100%'
				});
				
			}
			/*********** Inspection Summary End ***********/
			
			/*************** Gallery Start ***************/
			$('#interest_tbody_gallery').html('');
			var int_count = result.gallery.length;
              
			for(var i=0;i<int_count;i++)
			{
				var new_row_data = i+1;
				var gval = "<option>-- Select Image Type -- </option>";
		
				$('#interest_tbody_gallery').append('<tr id="interest_tr_4' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><input type="hidden" id="docType'+new_row_data+'" name="docType[]"><select class="form-control form-select gallery_image_type" name="gallery_image_type[]" id="gallery_image_type_' + new_row_data + '" data-parsley-required-message="Image Type Required" data-input_id="' + new_row_data + '" onchange="imgTypeCheckUnique(this.id,'+new_row_data+');" disabled="true">'+gval+'</select><div id="galNotification'+new_row_data+'" style="color:red; font-style:italic; font-size:12px;" ></div></td> <td class="prop"><input type="hidden" name="g_sl[]" id="g_sl'+new_row_data+'" value="'+new_row_data+'"><input type="hidden" id="docimg'+new_row_data+'" name="docimg[]"><img id="img_g'+new_row_data+'"src="" style="height:50px;"><input type="hidden" id="hfile'+new_row_data+'" name="hfile[]"></td> <td class="prop"><textarea class="form-control input-sm input-sm-100" data-input_id="'+ new_row_data +'" name="gallery_image_desc[]" id="gallery_image_desc_' + new_row_data + '" placeholder="Image Description" rows="1" disabled="true"></textarea></td> <td class="remove_new_row center" title="Remove Gallery Row" onclick="remove_row_interest_gallery(' + new_row_data + ')" style="cursor:pointer"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
       
				$('#img_g'+new_row_data).attr("src","https://www.auto-assure.com/crm/public/uploads/inspectionreport/gallery/"+result.gallery[i].gallery_image);
					
				$('#docimg'+new_row_data).val(result.gallery[i].gallery_id);
				$('#docType'+new_row_data).val(result.gallery[i].gallery_id);
				
				$('#hfile'+new_row_data).val(result.gallery[i].gallery_image);
				$('#gallery_image_desc_'+new_row_data).val(result.gallery[i].gallery_image_desc);
				
				if(result.gallery_type.length > 0)
				{  
					gval = " ";  
					for(var j=0; j < result.gallery_type.length; j++)
					{
						if(result.gallery_type[j].gallery_type_id == result.gallery[i].gallery_image_type)
						{
							gval += "<option value='"+result.gallery_type[j].gallery_type_id+"' selected>"+result.gallery_type[j].gallery_type_name+"</option>";
						}
						else
						{
							gval += "<option value='"+result.gallery_type[j].gallery_type_id+"'>"+result.gallery_type[j].gallery_type_name+"</option>";
						}
					}
				}
				$('#gallery_image_type_'+new_row_data).html(gval);
				//$('#gallery_image_type_'+new_row_data).val(result.gallery[i].gallery_image_type);
			}

			$('#interest_row_start_gallery').val(parseInt(int_count+1)); 
			/*************** Gallery End ***************/
			
			/*************** REPORTS START ***************/
			$('#interest_tbody_reports').html('');
			var int_count = result.repfile.length;
              
			for(var i=0;i<int_count;i++)
			{
				var new_row_data = i+1;
		
				$('#interest_tbody_reports').append('<tr id="interest_tr_4' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><input type="hidden" name="g_sl_file[]" id="g_sl_file'+new_row_data+'" value="'+new_row_data+'"><input type="hidden" id="docfile'+new_row_data+'" name="docfile[]"><a id="img_fi'+new_row_data+'" href="" target="_blank"><img id="img_f'+new_row_data+'"src="" style="height:50px;"></a><input type="hidden" id="hfilefile'+new_row_data+'" name="hfilefile[]"></td><td class="remove_new_row center" title="Remove Reports" onclick="remove_row_interest_gallery(' + new_row_data + ')"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
       
				$('#img_f'+new_row_data).attr("src","https://www.auto-assure.com/crm/public/img/icons/pdf.png");
				$('#img_fi'+new_row_data).attr("href","https://www.auto-assure.com/crm/public/uploads/inspectionreport/reports/"+result.repfile[i].rep_file);
         
				$('#docfile'+new_row_data).val(result.repfile[i].rep_id);
				$('#hfilefile'+new_row_data).val(result.repfile[i].rep_file);
			}

			$('#interest_row_start_reports').val(parseInt(int_count+1)); 
			/*************** REPORTS END ***************/
			
			/*********** DAMAGED IMAGE START ***********/
			var img = document.createElement("img");
			img.src = "https://www.auto-assure.com/crm/public/uploads/inspectionreport/damages/"+result.damageImg.damage_image;
			img.style="width:350px;height:350px";
			var src = document.getElementById("damageImg");
			src.appendChild(img);
			/*********** DAMAGED IMAGE START ***********/
        }
    });
}

function clearForm()
{
    $('#createForm').parsley().reset();
    $('#createForm')[0].reset();   
}

function viewReport(report_id)
{
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'report_id': report_id},
        url: url_viewReport,
        success: function (result) 
		{
			$('#view-modal-body').html(result);
            $('#view_modal').modal('show');     
        }
    });
}

/*********************************/
function NextPriceTab(formId,tabname)
{  
    $('#' + formId).parsley().validate();
    if (!$('#' + formId).parsley().isValid()) 
    {
        return false;
    }
    else
    {
        tabToggle(tabname);
    }
}
  
function backToTab(formId,tabname)
{
    tabToggle(tabname);
}
  
/*********************************/  
function tabToggle(tab)
{  
    $(".nav-link").removeClass("active");
    $("#"+tab+"-tab").addClass("active");
    $(".tab-pane").removeClass("show active");
    $("#"+tab).addClass("show active");
	//$("#"+tab).css("display", "");
	return false;
}
/*********************************/

function addFollowUp(id) //date,status,remarks,assigned_user,convertstatus)      
{   
	//var status = document.getElementById("cStatus");
	//var status = skillsSelect.options[skillsSelect.selectedIndex].text; //alert(selectedText);
	var status  = $("#cStatus").val();
	var name    = $('#cStatus option:selected').text();    
	var date    = $("#follow_next_date").val();
	var remarks = '';
	var assigned_user = $("#assign_staff").val();

	var staff = $("#assign_staff").val();

	$.ajax({
		type: 'POST',
		url: url_add_followup,
		dataType:'json',
		data:{'id':id,'date':date,'status':status,'name':name,'remarks':remarks,'assinged_user':assigned_user,'staff':staff,'_token':token},

        success: function(data)
		{   
			if (data.status == 0) 
			{                      
				Command: toastr["success"](data.text)
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
				$('#inspectionReportDataTable').DataTable().ajax.reload();
            }
			else 
			{
				Command: toastr["error"](data.text)
					toastr.options = {
					  "closeButton": true,
					  "debug": false,
					  "heading": "data.heading",
					  "text": "data.msg",
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
            }
	     }
      }); 
 }
 
 
 
function SummaryDesEdit(SummaryType,row)
    { 
        if(SummaryType)
        { 
            $.ajax({
                type: 'GET',
                url: 'inspectionreport/SummaryDes',
                data: { SummaryType: SummaryType },
				success: function(res) 
				{
					const $select = $('#extra_name_' + row);
					//console.log('Target select:', $select);
					$select.empty().append('<option value="">-- Summary Desc --</option>');
					
					if (res) {
						$.each(res, function(key, value) {
							$select.append('<option value="' + value + '">' + value + '</option>');
						});
						// If you use select2, you need to refresh it
						if ($select.hasClass('select2-hidden-accessible')) 
						{
							$select.trigger('change.select2');
						}
					}
				},
				error: function(xhr) {
					console.error('AJAX error', xhr);
				}
            });
        }
    	else {}      
    } 