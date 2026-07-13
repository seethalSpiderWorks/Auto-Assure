@extends('layouts.myfudapp')
@section('content')
 
<div class="page-content">
	<div class="container-fluid">

		<!-- start page title -->
		<div class="row">
			<div class="col-12">
				<div class="page-title-box d-flex align-items-center justify-content-between">
					<h4 class="mb-0">Manage Leads</h4>

					<div class="page-title-right d-flex align-items-center" style="gap:.75rem;">
						@if(!empty($inspection))
							<a href="{{ url('inspections/'.$inspection->id.'/edit') }}" target="_blank" class="btn btn-sm btn-primary">
								<i class="far fa-clipboard"></i> Open Inspection
							</a>
						@endif
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
							<li class="breadcrumb-item active">Manage Leads</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
        <!-- end page title -->
 
		<div class="row">
			<div class="col-lg-12">
 
				<!------------------>
				<div class="card">
					<div class="card-body">
						<h4 class="card-title"><b>Basic Details & Lead Info</b></h4>
						<?php
                        $fields = [
                            'Name'     => $data->breg_fname . ' ' . $data->breg_lname,
                            'Mobile'   => $data->breg_mob,
                            'Email'    => $data->breg_email,
                            'WhatsApp' => $data->breg_whatsapp,
                            'Place'    => $data->breg_place,
                            'Message'  => $data->breg_message,
                        ];
                        
                        $fields = array_filter($fields);
                        $chunks = array_chunk($fields, 3, true); 
                        ?>
                        
                        <table class="table tbl-u-boarderd">
                            <?php foreach ($chunks as $row) : ?>
                                <tr>
                                    <?php foreach ($row as $label => $value) : ?>
                                        <th><?php echo $label; ?></th> <th>:</th>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; 
                                    
                                    $missing = 3 - count($row);
                                    for ($i = 0; $i < $missing; $i++) {
                                        echo '<th></th><td></td>';
                                    }
                                    ?>
                                </tr>
                                
                            <?php endforeach; ?>
                            <tr><td colspan="9" style="border-top: gray thin dotted;margin-top: 10px;margin-bottom: 10px;"></td></tr>
                        <!--</table>-->
 
						<!--<p class="hr" style="border-top: gray thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>-->

						<?php
						$dataslead = DB::table('tbl_lead')
						        ->select('lead_unq_id','lead_date', 'source_name','name','lname','make_name','model_name','lead_vehicle_plate_no',
						        'lead_year','lead_color','lead_color_ar','lead_seller_name','lead_seller_name_ar','lead_seller_mobile','location_name',
						        'lead_your_mobile','lead_add_details','lead_form_type','make_model_year','lead_id','tbl_lead.created_at')
								->where('lead_id',$data->lead_id)
								->where('tbl_lead.lead_status',0)
								->leftjoin('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id')
								->leftjoin('users','users.id','lead_assigned_users')
								->leftjoin('tbl_source','tbl_source.source_id','lead_source')
								->leftjoin('tbl_branch','tbl_branch.branch_id','tbl_lead.lead_branch_id')
								->leftjoin('tbl_make','tbl_make.make_id','lead_make')
								->leftjoin('tbl_model','tbl_model.model_id','lead_model')
								->leftjoin('tbl_location','tbl_location.location_id','lead_location')
								->where('tbl_basic_registration.breg_status',0)
								//->orderby('lead_id','desc')
								->get();   ?>
								
					    <!--<table class="table tbl-u-boarderd">-->
                            @foreach($dataslead as $value)
                                <?php
                                $leadDatetime = $value->created_at;
                                // Create a DateTime object from the stored India time (IST)
                                $indiaTime = new DateTime($leadDatetime, new DateTimeZone('Asia/Kolkata')); // IST is UTC +5:30
                                
                                // Convert the datetime to Qatar time (Asia/Qatar, UTC +3:00)
                                $indiaTime->setTimezone(new DateTimeZone('Asia/Qatar')); // Qatar time is UTC +3:00
                                
                                // Format the datetime to display it in the desired format (e.g., d-m-Y h:i A)
                                $qatarTime = $indiaTime->format('h:i A');
                                
                                $fields = [
                                    'Lead ID'    => $value->lead_unq_id,
                                    'Date'       => $value->lead_date ? date("d-m-Y", strtotime($value->lead_date)) : null,
                                    'Qatar Time' => $qatarTime,
                                    'Source'  => $value->source_name,
                                    'Staff'   => $value->name . ' ' . $value->lname,
                                    'Make'    => $value->make_name,
                                    'Model'   => $value->model_name,
                                    'Year'    => $value->lead_year ?? null,
                                    'Color'   => $value->lead_color ?? null,
                                    'Color in Arabic'       => $value->lead_color_ar ?? null,
                                    'Vehicle Plate No'      => $value->lead_vehicle_plate_no ?? null,
                                    'Seller Name'           => $value->lead_seller_name ?? null,
                                    'Seller Name in Arabic' => $value->lead_seller_name_ar ?? null,
                                    'Seller Mobile'         => $value->lead_seller_mobile ?? null,
                                    'Location'              => $value->location_name ?? null,
                                    'Mobile'                => $value->lead_your_mobile ?? null,
                                    'Additional Details'    => $value->lead_add_details ?? null,
                                    'Lead Form'             => $value->lead_form_type ? ($value->lead_form_type == 1 ? "Book Inspection" : ($value->lead_form_type == 2 ? "Buy Assured" : $value->lead_form_type)) : null,
                                    'Make/Model/Year'       => $value->make_model_year ?? null,
                                ];
                        
                                $follow_date = DB::table('tbl_lead_followup')
                                        ->select('followup_date')
                                        ->where('followup_lead_id',$value->lead_id)
                                        ->where('followup_status',0)
                                        ->latest()
                                        ->first();
                        
                                $fields['Assign Date'] = $follow_date && $follow_date->followup_date ? date("d-m-Y", strtotime($follow_date->followup_date)) : date("d-m-Y");
                        
                                $followlead = DB::table('tbl_lead')
                                        ->select('lead_assigned_status')
                                        ->where('lead_id',$value->lead_id)
                                        ->where('lead_status',0)
                                        ->latest()
                                        ->first();
                        
                                $statusBadge = '';
                                if($followlead && $followlead->lead_assigned_status){
                                    $status = $followlead->lead_assigned_status;
                                    switch($status){
                                        case 'Assign': $statusBadge = '<span class="btn bg-success" style="padding:1px;min-width:50px;color:#f5f6f8">Assigned</span>'; break;
                                        case 'Reassign': $statusBadge = '<span class="btn bg-gradient-success" style="padding:1px;min-width:50px;color:#f5f6f8">Reassigned</span>'; break;
                                        case 'Followup': $statusBadge = '<span class="btn bg-primary" style="padding:1px;min-width:50px;color:#f5f6f8">Followup</span>'; break;
                                        case 'Rejected': $statusBadge = '<span class="btn btn-secondary" style="padding:1px;min-width:50px;color:#f5f6f8">Rejected</span>'; break;
                                        case 'Closed': $statusBadge = '<span class="btn bg-black" style="padding:1px;min-width:50px;color:#f5f6f8">Closed</span>'; break;
                                        case 'Inspection': $statusBadge = '<span class="btn bg-warning" style="padding:1px;min-width:50px;color:#f5f6f8">Inspection</span>'; break;
                                        case 'Inspection Completed': $statusBadge = '<span class="btn bg-primary" style="padding:1px;min-width:50px;color:#f5f6f8">Inspection Completed</span>'; break;
                                        case 'Plan / Shedule': $statusBadge = '<span class="btn bg-primary" style="padding:1px;min-width:50px;color:#f5f6f8">Plan / Shedule</span>'; break;
                                        case 'Reshedule': $statusBadge = '<span class="btn bg-info" style="padding:1px;min-width:50px;color:#f5f6f8">Reshedule</span>'; break;
                                        case 'Approved': $statusBadge = '<span class="btn bg-success" style="padding:1px;min-width:50px;color:#f5f6f8">Approved</span>'; break;
                                        case 'New': default: $statusBadge = '<span class="btn btn-danger" style="padding:1px;min-width:50px;">New</span>'; break;
                                    }
                                } else {
                                    $statusBadge = '<span class="btn btn-danger" style="padding:1px;min-width:50px;">New</span>';
                                }
                        
                                $fields['Status'] = $statusBadge;
                        
                                if(isset($fields['Staff'])){
                                    $staffValue = $fields['Staff'];
                                    unset($fields['Staff']); 
                                    $newFields = [];
                                    foreach($fields as $key => $val){
                                        $newFields[$key] = $val;
                                        if($key == 'Status'){
                                            $newFields['Staff'] = $staffValue;
                                        }
                                    }
                                    $fields = $newFields;
                                }
                        
                                $package = DB::table('tbl_lead_package')
                                        ->select('lead_pack_refe_id','package_name','package_payable','lead_mode_pay')
                                        ->leftJoin('tbl_package','tbl_package.package_id','lead_pack_lead_id')
                                        ->where('lead_pack_lead_id',$value->lead_id)
                                        ->where('lead_pack_status',0)
                                        ->latest()
                                        ->first();
                        
                                if($package) {
                                    if($package->lead_pack_refe_id) $fields['Reference ID'] = $package->lead_pack_refe_id;
                                    if($package->package_name) $fields['Package'] = $package->package_name;
                                    if($package->package_payable) $fields['Amount Payable'] = 'QAR. ' . $package->package_payable;
                                    if($package->lead_mode_pay) {
                                        $fields['Mode of Payment'] = $package->lead_mode_pay == 1 ? 'Online Payment' : ($package->lead_mode_pay == 2 ? 'Offline Payment' : null);
                                    }
                                }
                        
                                $fields = array_filter($fields);
                                $chunks = array_chunk($fields, 3, true); 
                                ?>
                        
                                @foreach($chunks as $row)
                                    <tr>
                                        @foreach($row as $label => $val)
                                            <th>{!! $label !!}</th> <th>:</th>
                                            <td>{!! $val !!}</td>
                                        @endforeach
                                        @for($i = count($row); $i < 3; $i++)
                                            <th></th><td></td>
                                        @endfor
                                    </tr>
                                @endforeach
                                <!--<tr><td colspan="6" style="border-top:1px dotted black;"></td></tr>-->
                            @endforeach
                        </table>
	
						 
					</div>
				</div> 
				<!------2------>
				<div class="card">
					<div class="card-body">
					    
						<form id="followup_form">
							<input type="hidden" id="modal_lead_id" name="modal_lead_id" value="">
							<input type="hidden" name="statususer" id="statususer" >
						<?php  $user_previlage = Auth::user()->previlage;	
						if($user_previlage != 48) { ?>
							<div class="row">
								<div class="col-md-2">
									<div class="mb-2">
										<label class="form-label">Status<span style="color:red">*</span></label>
										<!-- <select name="follow_status" id="follow_status" required class="form-control form-select select2 @error('follow_status') is-invalid @enderror"  value="{{ old('follow_status') }}" style="width:160px;" onchange="changeStatus(this.value,this.name)"  data-parsley-errors-container="#status-parsley-error"> </select> -->
										<?php 
										$lead_Status = DB::table('tbl_lead')
										    ->select('lead_followup_type')
											->where('lead_id',$data->lead_id)
											->where('lead_status',0)
											->first();   

										$leadStatus = $lead_Status->lead_followup_type;
										
										$status = DB::table('tbl_followup_type')
										    ->select('followup_type_id', 'followup_type_name')
											->where('followup_type_status',0)
											->select('followup_type_name','followup_type_id')
											->orderby('followup_type_priority','asc')
											->where('followup_type_name','!=' ,'New');
									 	
										if($user_previlage === 48 || $user_previlage === 49)
										{  
											if($leadStatus == 7)
											{
												$status = $status->where('followup_type_name','!=' ,'Assign'); //Assign
											}
											elseif($leadStatus == 1)
											{
												$status = $status->where('followup_type_name','!=' ,'Reassign'); //Reassign
											}
											 
											$status = $status->where('followup_type_name','!=' ,'Approved'); //Approved
										} 

										if($leadStatus == 7)
										{
											$status = $status->where('followup_type_name','!=' ,'Reassign');
											$status = $status->where('followup_type_name','!=' ,'Inspection'); //Inspection
											$status = $status->where('followup_type_name','!=' ,'Inspection Completed');
											$status = $status->where('followup_type_name','!=' ,'Approved');
										}
										elseif($leadStatus == 1)
										{
											$status = $status->where('followup_type_name','!=' ,'Assign'); //Assign
											$status = $status->where('followup_type_name','!=' ,'Approved');
										}
										elseif($leadStatus == 2)
										{
											$status = $status->where('followup_type_name','!=' ,'Assign'); //Assign
											$status = $status->where('followup_type_name','!=' ,'Approved');
										}
										elseif($leadStatus == 14)
										{
											$status = $status->where('followup_type_name','!=' ,'Inspection'); //Inspection
											$status = $status->where('followup_type_name','!=' ,'Assign'); //Assign
											$status = $status->where('followup_type_name','!=' ,'Reassign'); //Assign
										}
										elseif($leadStatus == 15)
										{
											$status = $status->where('followup_type_name','!=' ,'Plan / Shedule');
										}
										elseif($leadStatus == 16)
										{
											$status = $status->where('followup_type_name','!=' ,'Reshedule');
										}
										elseif($leadStatus == 18)
										{
											$status = $status->where('followup_type_name','!=' ,'Inspection Completed');
											$status = $status->where('followup_type_name','Approved');
										}
										
											$status = $status->get();  ?>
										<select name="follow_status" id="follow_status" required class="form-control form-select select2 "  style="width:160px;" onchange="changeStatus(this.value,this.name)"  data-parsley-errors-container="#status-parsley-error"> 
											<option value="">  -- Select -- </option> <?php 
											foreach($status as $srow)
											{ ?>
												<option value="{{$srow->followup_type_id}}">{{$srow->followup_type_name}}</option> <?php  
											}?>
										</select>
											
										<div class="help-block with-errors"></div>
										<span style="float:right;" id="status-parsley-error"></span>	
									</div>
								</div>
									
								<div class="col-md-2" id="div_follow4" style="display:none">
									<div class="mb-2">
										<label class="form-label">Staff&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=""></span></label>
										{!! html()->select('assign_staff', $users , null)->attributes([ 'class'=>'form-control select2','id'=> 'assign_staff', 'style'=>'width:160px', 'data-parsley-errors-container'=>"#error_assign"])->placeholder('Select Staff') !!}
										 <div id="error_assign"></div>
									</div>
								</div> 
								   
								<div class="col-md-2" id="div_follow1" style="display:none">
									<div class="mb-2">
										<label for="exampleInputEmail3">Next Followup Date<span class="text-red"></span></label>
										<input type="date"  value="" class="form-control datetimepicker-input " id="follow_next_date" name="follow_next_date" data-toggle="datetimepicker" data-target="#follow_next_date">											
									</div>
								</div>
								   
								<div class="col-md-2" id="div_follow3" style="">
									<div class="mb-2">
										<label for="exampleInputEmail3">Comments<span class="text-red" id="commentid"></span></label>
										<textarea  name="followup_remark" id="followup_remark" class="form-control" is="followup_remark"  data-parsley-errors-container="#error_followup_remark" style="height: 39px;resize:none"></textarea>
										<div id="error_followup_remark"></div> 
									</div>
								</div>
									 
								<div class="col-md-2" id="btn_followup_div" style="margin-top:25px;">
									<div class="mb-2">
										<button type="button" class="btn btn-primary" id="folsub" onclick="addFollowUp(<?php echo $data->lead_id;?>)">Submit</button>
										<!-- "addFollowUp(modal_lead_id.value,follow_next_date.value,follow_status.value,followup_remark.value,assign_staff.value)" -->
									</div>
								</div>   
							</div>
							<?php 
						}?>
						</form>
							
						<div class="row" >
							<div class="col-md-12">
								<div id="view_modal_body_follow">  
									<!----------------------------------->
									<!----------------------------------->
								</div>
							</div>
						</div>
					</div>
				</div>
					 
				<!------------>
			</div> 
		</div> 
			
	</div> 
</div> 

 @endsection
 
 @section('js')
<script type="text/javascript">
  var public_path              = '<?php echo url('/');?>';
  var url_lead_table           = '{{URL::to("leads/get-list")}}';
  var url_add_followup         = "{{url('/leads/add_followup')}}";
  var url_view_followup_table  = "{{url('/leads/set_lead_session_followtable')}}";
  var url_view_followup_tables = "{{url('/leads/set_lead_session_followtables')}}";
		
</script>  

	<!-- Required datatable js -->
	<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
	<!-- Datatable init js -->
	<script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
	<!-- plugins -->
	<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>   
	<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
 
	<script src="{{asset('module.js/Leads/index.js?ver=1.1')}}"></script>
 
<script>
$( document ).ready(function() {
    console.log( "ready!" );
	var id = $("#leadid").val(); 
	$.ajax({
            type: 'GET',
            url: url_view_followup_table,
            dataType:'html',
            data:{'id':id},
            success: function (data) 
			{
                $("#view_modal_body_follow").html(data);
            }
        });
});

function addFollowUp(id) //date,status,remarks,assigned_user,convertstatus)      
{  
	var date    = $("#follow_next_date").val();
	var status  = $("#follow_status").val();
	var remarks = $("#followup_remark").val();
	var assigned_user = $("#assign_staff").val();

	var name  = $('#follow_status option:selected').text();
	var staff = $("#assign_staff").val();

	//$("#follow_status").find('select2').attr('required','required');

	$.ajax({
		type: 'POST',
		url: url_add_followup,
		dataType:'json',
		data:{'id':id,'date':date,'status':status,'name':name,'remarks':remarks,'assinged_user':assigned_user,'staff':staff,'_token':"{{csrf_token()}}"},
            	  
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
				window.location.reload();
				$.ajax({
						type: 'GET',
						url: url_view_followup_tables,
						dataType:'html',
						data:{'id':id},
						success: function (data) 
						{ 
							console.log(id);
                            $("#view_modal_body_follow").html(data);
                        }
                    });      
                    
				$("#followup_form").trigger('reset');
				$('#follow_next_date').val('').trigger('change');
				$('#followup_remark').val('').trigger('change')						
                // $('#followup_form')[0].reset();
                $('#assign_staff').val(null).trigger("change");

				$("#lead_table").DataTable().ajax.reload();
				//  $('#lead_in').html(data);
				// $("#lead_in").append("status");
						
					var leadstatus;
						if(status == 1)
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Assigned</span></p>';
						}	
						if(status == 2)		  
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: pointer !important;">Reassigned</span></p>';
						}							
						if(status == 3)
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Followup</span></p>';
						}		
						if(status == 4)
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Registered</span></p>';
						}
						if(status == 5)
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-danger" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Rejected</span></p>';
						}
						if(status == 6)
						{
							leadstatus = '<p><b>Status </b>: <span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: pointer !important;">Closed</span></p>';
						}
					
						
					$("#lead_in").html(leadstatus);
						 
					var assign_d = date;
					if(assign_d != '')
					{
						var dateMod =new Date(assign_d);
						dateMod  = (dateMod.getDate()).toString().padStart(2, '0') + '-' + (dateMod.getMonth()+1).toString().padStart(2, '0') + '-' + dateMod.getFullYear() ;
					}
					else
					{
						var dateMod = date("d-m-Y");
					}

					$("#lead_date").html('<p><b>Assign Date </b>: '+dateMod+'</p>');	
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
 </script>
 
 <script>
	function changeStatus(status,name)
	{ 
		$(this).val('').trigger('change');
		 
		$("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
		$("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
		$("#div_follow2").hide();$("#div_follow2").find('select').removeAttr('required');
  
		$("#intm").html('');
		$("#inty").html('');
   
		$("#btn_followup_div").attr('class','col-md-3');
		$("#div_follow3").show();
	 
		$("#error_followup_remark").show();
          
		$("#div_follow3").find('textarea').attr('required','required');
		if(status=="1")   // Assign
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			 
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
         
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');
		}
		else if(status=="2")  // Reassign
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
                   
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');        
		}
		else if(status=="15")   // Plan / Shedule
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
                   
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');        
		}
		else if(status=="16")  // Reshedule
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
                   
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');        
		}
		else if(status=="3")  // Followup
		{
			$("#div_follow1").show();
		}
		else if(status=="5") // Rejected
		{
			$("#div_follow1").hide();  
			//$("#div_follow4").show();
		}
		else if(status=="6") // Closed
		{
			//$("#div_follow1").show();    
			$("#div_follow1").hide();  
			//$("#div_follow4").show();
		}
		else
		{
			$("#div_follow1").hide();
			$("#div_follow2").hide(); 
		}
	}  
</script> 
 @endsection