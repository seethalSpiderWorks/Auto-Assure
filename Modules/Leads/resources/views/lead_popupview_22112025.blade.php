@extends('layouts.myfudapp')
@section('content')
 
<div class="page-content">
	<div class="container-fluid">

		<!-- start page title -->
		<div class="row">
			<div class="col-12">
				<div class="page-title-box d-flex align-items-center justify-content-between">
					<h4 class="mb-0">Manage Leads</h4>

					<div class="page-title-right">
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
						<h4 class="card-title"><b>Basic Details</b></h4>
						<div class="row">
							<input type="hidden" id="leadid" value="<?php echo $data->lead_id; ?>">
							<?php //echo $data->lead_id; ?>
							<div class="col-md-3">
								<p class="card-text"><b>Name</b> : {{$data->breg_fname}} {{$data->breg_lname}}</p>
							</div>
							
							<?php if($data->breg_fname_ar){?>
							<div class="col-md-3">
								<p class="card-text"><b>Name in Arabic</b> : {{$data->breg_fname_ar}}</p>
							</div> <?php } ?>
							
							<div class="col-md-3">
								<p class="card-text"><b>Mobile</b> : {{$data->breg_mob}}</p>
							</div>
							
							<div class="col-md-3">
								<p class="card-text"><b>Email Id</b> : {{$data->breg_email}}</p>
							</div>
							
							<?php 
							if($data->breg_qualification)
							{?>
								<div class="col-md-3">
									<p class="card-text"><b>Qualification</b> : {{$data->breg_qualification}}</p>
								</div> <?php 
							} 
							if($data->breg_place)
							{ ?>
								<div class="col-md-3">
									<p class="card-text"><b>Place</b> : {{$data->breg_place}}</p>
								</div> <?php 
							} ?>
							 
							<div class="col-md-3">
								<p class="card-text"><b>Message</b> : {{$data->breg_message}}</p>
							</div>
							 
						</div>
						<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>

						<?php
						$dataslead = DB::table('tbl_lead')
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
								
						@foreach($dataslead as $value)
							<h4 class="card-title"><b>Lead Info</b></h4> 
							<div class="row">
							
								<div class="col-md-3">
									<p class="card-text"><b>Lead ID</b> : {{$value->lead_unq_id}}</p>
								</div>
							
								<div class="col-md-3">
									<p class="card-text"><b>Date</b> : {{date("d-m-Y",strtotime($value->lead_date))}}</p>
								</div>
						     
								<div class="col-md-3">
									<p class="card-text"><b>Source</b> : {{$value->source_name}}</p>
								</div>
							
								<div class="col-md-3">
									<p class="card-text"><b>Staff</b> : {{$value->name}} {{$value->lname}}</p>
								</div>
								
								<div class="col-md-3">
									<p class="card-text"><b>Make</b> :  {{$value->make_name}}</p>
								</div>
								
								<div class="col-md-3">
									<p class="card-text"><b>Model</b> : {{$value->model_name}}</p>
								</div>
								
								<?php 
								if($value->lead_vehicle_plate_no) { ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Vehicle Plate No</b> : {{$value->lead_vehicle_plate_no}}
    								</div> 
    								<?php 
								} ?>
								
								<div class="col-md-3">
									<p class="card-text"><b>Year</b> : {{$value->lead_year}}</p>
								</div>
								
								<?php
								if($value->lead_color)
								{ ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Color</b> : {{$value->lead_color}}</p>
    								</div> <?php
								}
								if($value->lead_color_ar)
								{ ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Color in Arabic</b> : {{$value->lead_color_ar}}</p>
    								</div> <?php
								}
								if($value->lead_seller_name) 
								{  ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Seller Name</b> : {{$value->lead_seller_name}}</p>
    								</div> <?php
								} 
								if($value->lead_seller_name_ar) 
								{  ?>
    								<div class="col-md-3">
										<p class="card-text"><b>Seller Name in Arabic</b> : {{$value->lead_seller_name_ar}}</p>
    								</div> <?php
								} 
								if($value->lead_seller_mobile)
								{ ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Seller Mobile</b> : {{$value->lead_seller_mobile}}</p>
    								</div> <?php
								}  
								if($value->location_name) 
								{ ?> 
    								<div class="col-md-3">
    									<p class="card-text"><b>Location</b> : {{$value->location_name}}</p>
    								</div> <?php
								}  
								if($value->lead_your_mobile)
								{   ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Mobile</b> : {{$value->lead_your_mobile}}</p>
    								</div> <?php
								} 
								if($value->lead_add_details)
								{   ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Additional Details</b> : {{$value->lead_add_details}}</p>
    								</div> <?php
								}?>
								 
								<?php 
								$follow_date = DB::table('tbl_lead_followup') 
										//->where('followup_reg_id',$data->lead_reg_id)
										->where('followup_lead_id',$data->lead_id)
										->where('followup_status',0)
										->latest()
										->first();
 
								if($follow_date)
								{ ?>
									<div class="col-md-3"> <?php  
										if($follow_date->followup_date != '')
										{ ?>
											<p id="lead_date" class="card-text"><b>Assign Date</b> : {{date("d-m-Y",strtotime($follow_date->followup_date))}} </p> <?php  
										}
										else
										{ ?>
											<p id="lead_date" class="card-text"><b>Assign Date</b> : {{date("d-m-Y")}} </p> <?php 	
										} ?>
									</div> <?php 
								} ?>
								
								<div class="col-md-3" id="lead_in"> <?php
									$followlead = DB::table('tbl_lead')
										->select('lead_assigned_status','lead_reg_id')
										->where('lead_id',$data->lead_id)
										->where('lead_status',0)
										->latest()
										->first();
		 							
									if($followlead)
									{
										if($followlead->lead_assigned_status)
										{
											$status = $followlead->lead_assigned_status;  
											if($status == 'Assign')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Assigned</span></p>';
											}	
											if($status == 'Reassign')		  
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-gradient-success" style="padding: 1px; min-width: 50px;color:#f5f6f8;cursor: auto !important;">Reassigned</span></p>';
											}							
											if($status == 'Followup')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Followup</span></p>';
											}		
											
											if($status == 'Rejected')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn btn-secondary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Rejected</span></p>';
											}
											
											if($status == 'Closed')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-black" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Closed</span></p>';
											}
											
											if($status == 'Inspection')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-warning" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Inspection</span></p>';
											}
											
											if($status == 'Inspection Completed')
											{
											 $leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-primary" style="color:#f5f6f8;cursor: auto !important;padding: 1px; min-width: 50px;">Inspection Completed</span></p>';
											}	
											
											if($status == 'Plan / Shedule')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-primary" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Plan / Shedule</span></p>';
											}
											
											if($status == 'Reshedule')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="bg-info" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Reshedule</span></p>';
											}
											if($status == 'Approved')
											{
												$leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn bg-success" style="padding: 1px; min-width: 50px; color:#f5f6f8;cursor: auto !important;">Approved</span></p>';
											}
											
											if($status == 'New')
											{
											 $leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn btn-danger" style="cursor: auto !important;padding: 1px; min-width: 50px;">New</span></p>';
											}	
											//echo $status = $followlead->lead_assigned_status;
											echo $leadstatus;
										}
										else
										{
											echo $leadstatus = '<p class="card-text"><b>Status </b>: <span class="btn btn-danger" style="cursor: auto !important;padding: 1px; min-width: 50px;">New</span></p>';
										}
									}
									else
									{
										$follow = DB::table('tbl_lead_followup')
												->select('followup_current_status')
												->where('followup_reg_id',$value->lead_reg_id)
												->where('followup_status',0)
												->latest()->first(); ?>
												
								        @if($follow !='')      
								            @if($follow->followup_current_status)
										        {{$follow->followup_current_status}}                    
										    @else
										        {{__('New')}}
										    @endif
								        @else
								            {{__('New')}}
								        @endif
								        <?php
								    } ?>
							    </div>
							
							    <?php 
								if($value->lead_form_type)
								{   ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Lead Form </b> :
										<?php 
										if($value->lead_form_type == 1)
										{
											echo "Book Inspection";
										}
										else if($value->lead_form_type == 2)
										{ 
											echo "Buy Assured";
										}
										else
										{
											echo $value->lead_form_type; 
										}?>
										</p>
    								</div> <?php
								}?>
								
								<?php 
								if($value->make_model_year)
								{   ?>
    								<div class="col-md-3">
    									<p class="card-text"><b>Make/Model/Year </b> :
										<?php 
										if($value->make_model_year)
										{
											echo $value->make_model_year;
										}
										?>
										</p>
    								</div> <?php
								}?>
							
								<!-------------- Payment Summary -------------->
								<?php 
								$package = DB::table('tbl_lead_package') 
								        ->leftjoin('tbl_package','tbl_package.package_id','lead_pack_lead_id')
										->where('lead_pack_lead_id',$data->lead_id)
										->where('lead_pack_status',0)
										->latest()
										->first(); 
										
							    if($package != null)
							    {   ?>
							        <!--<h4 clss="card-title"> Payment Summary </h4> -->
							        <?php
							        
                                    if($package->lead_pack_refe_id != null)
    								{   ?>
        								<div class="col-md-3">
        									<p class="card-text"><b>Reference ID</b> : {{$package->lead_pack_refe_id}}</p>
        								</div> <?php
    								} 
    
    								if($package->package_name)
    								{   ?>
        								<div class="col-md-3">
        									<p class="card-text"><b>Package</b> : {{$package->package_name}}</p>
        								</div> <?php
    								} 
    								
    								if($package->package_payable)
    								{   ?>
        								<div class="col-md-3">
        									<p class="card-text"><b>Amount Payable</b> : QAR. {{$package->package_payable}}</p>
        								</div> <?php
    								}
    								
    								if($package->lead_mode_pay)
    								{   ?>
            						    <div class="col-md-3"> 
            						    
            						        <p class="card-text"><b>Mode of Payment</b> :
            						            <?php
                    						    if($package->lead_mode_pay == 1) 
            								    {   
            						                echo "Online Payment"; 
            								    }
            								    else if($package->lead_mode_pay == 2) 
        								        {
        								            echo "Offline Payment";
        								        } 
        								        else
        								        {
        								        } ?>
        								    </p>
    								    </div> <?php
    								}
    							 } ?>
								
								<!--------------------------------------------->
						</div>
						<p class="hr" style="border-top: black thin dotted;margin-top: 10px;margin-bottom: 10px;"></p>
						@endforeach
						 
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
											->where('lead_id',$data->lead_id)
											->where('lead_status',0)
											->first();   

										$leadStatus = $lead_Status->lead_followup_type;
										
										$status = DB::table('tbl_followup_type')
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
  var public_path = '<?php echo url('/');?>';
  var url_lead_table   = '{{URL::to("leads/get-list")}}';
  
  var url_add_followup = "{{url('/leads/add_followup')}}";
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