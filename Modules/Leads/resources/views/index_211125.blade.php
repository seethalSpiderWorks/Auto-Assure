@extends('layouts.myfudapp')
@section('content')

<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 

</style>

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Leads  </h4>

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
                    <div class="card">
                        <div class="card-body">
 
                            <div class="row">
                    
								<form class="forms-sample" id="lead_form" method="POST" enctype="multipart/form-data" action="{{url('leads/add_lead')}}" >
									<!--	<input type="hidden" id="breg_id" name="breg_id" value="{{old('breg_id')}}"> -->
									<!--	<input type="hidden" id="lead_id" name="lead_id" value="">  -->
									<input type="hidden" id="formtypeid" name="formtypeid" value="">
									<input type="hidden" id="lead_form_type_check" name="lead_form_type_check" value="">
									<input type="hidden" id="lead_form_type_old" name="lead_form_type_old" value="">
									<input id="countryShort" type="hidden" name="countryShort" value=" ">
									<input id="mobilecodedata" type="hidden" name="mobilecodedata" value=" ">
									<input type="hidden" name="phonecode" id="phonecode">
										
									<input type="hidden" id="breg_id" name="breg_id" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_id; ?>" <?php } ?>>
                                    <input type="hidden" id="lead_id" name="lead_id" <?php if(!empty($data)){ ?> value="<?php echo $data->lead_id; ?>" <?php } ?>>
                            
									@csrf
									<div class="row"> 
											   
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for="exampleInputEmail3"><span id="linktitle">Mobile</span><span style="color:red"> *</span></label>
													<input type="number" onkeyup="getmobilecode(),searchRegistrtaion(this.value,this)" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_mob; ?>" <?php } ?> required class="form-control @error('mobile') is-invalid @enderror" id="mobile"  name="mobile" placeholder="Enter Mobile" data-parsley-error-message="Mobile Number Required" pattern="/^[0-9]{1,15}$/" data-parsley-errors-container='#mobile-parsley-error'>
              
												<span style="float:right;" id="mobile-parsley-error"></span>
											</div>
										</div>
											
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for="exampleInputEmail3"><span id="linktitle">Email</span></label>
												<input type="email" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_email; ?>" <?php } ?> onkeyup=""  class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter Email ID" data-parsley-error-message="Enter Valid Email ID" data-parsley-errors-container='#email-parsley-error'>
												<div class="help-block with-errors"></div>
												<span style="float:right;" id="email-parsley-error"></span>
											</div>
										</div>			
								 
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for=""> Name<span style="color:red"> *</span></label>
												<input type="text" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_fname; ?>" <?php } ?>  class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="Enter First Name" data-parsley-error-message="First Name Required" pattern="[a-zA-Z ]{3,}" data-parsley-errors-container='#fname-parsley-error' required>
												<div class="help-block with-errors"></div>
												<span style="float:right;" id="fname-parsley-error"></span>	
											</div>
										</div>
										
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for=""> Name in Arabic <span style="color:red"> </span></label>
												<input type="text" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_fname_ar; ?>" <?php } ?> class="form-control @error('fname_ar') is-invalid @enderror" id="fname_ar" name="fname_ar" placeholder="Enter Name in Arabic" data-parsley-error-message="First Name Required" data-parsley-errors-container='#fname_ar-parsley-error' >
												<div class="help-block with-errors"></div>
												<span style="float:right;" id="fname-parsley-error"></span>	
											</div>
										</div>
										<!---------------------------------------------------------------->
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for=""> Place</label>
													<input type="text" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_place; ?>" <?php } ?>  class="form-control @error('breg_place') is-invalid @enderror" id="breg_place" name="breg_place" placeholder="Enter the place" data-parsley-errors-container='#bregplace-parsley-error'>
												<div class="help-block with-errors"></div>
												<span style="float:right;" id="bregplace-parsley-error"></span>	
											</div>
										</div>
										
										<?php 
										$user_branch = Auth::user()->user_branch;
										//$user_branch = session('application_branch');  //dd($user_branch);
										$district_id = DB::table('tbl_branch')
													->select('branch_city')
													->where('branch_id',$user_branch)
													->first();  
										$dist = $district_id->branch_city;  //echo $dist;
										  
										if(Auth::user()->previlage != 2)
										{ ?> 
											<input type="hidden" name="company_city" id="company_city" value="{{$dist}}"><?php 
										} 
										else
										{ ?>
											<input type="hidden" name="company_city" id="company_city" value="{{$dist}}"/> <?php 
										} ?>
											
										<!--------------  Centre -------------->
										<?php 
										if(Auth::user()->previlage != 2)
										{
											//$centre = Auth::user()->user_branch; 
											$centre = session('application_branch'); ?>
											<input type="hidden" name="centre" id="centre" value ="{{$centre}}"/> <?php
										}
										else
										{ 
											$centre = session('application_branch'); 
											$branchData = Auth::user()->dashboardbranchAction_new();
											$branch = Auth::user()->user_branch; 
											if(!empty($data)) 
											{ 
												$centre = $data->breg_branch_id	;
											} 
											else
											{ 
												$centre = session('application_branch');
											} ?>
												
											<input type="hidden" name="centre" id="centre" value ="{{$centre}}"/>
													<?php 
										} ?>
										<!----------------------------------------->		
										<?php 
										if(!empty($data))
										{
											$src = $data->lead_source;
										}
										else
										{
											$src = "";
										} ?>
										
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for="exampleInputEmail3">Source<span id="linktitle"></span></label><br>
												{!! html()->select('source', $sources , $src)->attributes([ 'class'=>'form-control form-select','id'=> 'source', 'required'=>'required'])->placeholder('Select')->required() !!}
												<span style="float:right;" id="parsley-id-23"></span>	
											</div>
										</div>
											
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for="exampleInputEmail3"><span id="linktitle">Message</span></label>
												<textarea rows="2" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_message; ?>" <?php } ?> class="form-control @error('message') is-invalid @enderror" id="message" name="message" placeholder="Message"data-parsley-errors-container="#error_contact_message" data-parsley-error-message="Message required" style="max-height:80px;resize:none;"></textarea>
												<div id="error_contact_message"></div>										
											</div>
										</div>
									</div>
									
									<hr>
									<!------------- Lead INfo ------------->
									<div class="row"><div class="mb-3"> <h5> Lead Info </h5> </div>  </div>
									<div class="row ">
										
										<?php 
										$make = DB::table('tbl_make')
											->select('make_name','make_id')
											->where('make_status',0)
											->where('make_publish_status',1)
											->get();
											
										$model = DB::table('tbl_model')
											->select('model_name','model_id')
											->where('model_status',0)
											->where('model_publish_status',1)
											->get();
											
										$location = DB::table('tbl_location')
											->select('location_name','location_id')
											->where('location_status',0)
											//->where('location_publish_status',1)
											->get(); 
										
										$package = DB::table('tbl_package')
											->select('package_name','package_id','package_payable')
											->where('package_status',0)
 											->get(); 										
											?>
											
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-3">
												<label for="formtype">Form Type <span style="color:red">*</span></label>
												 
												<select name="formtype" id="formtype" class="form-control form-select " data-parsley-error-message="Form Type Required" data-parsley-error-message="Form Type Required" data-parsley-errors-container='#formtype-parsley-error' required>
													<option value="">Select Form Type</option>
													<option value="Book Inspection"> Book Inspection</option>  
													<option value="Buy Assured"> Buy Assured</option>  
													<!--<option value="1"> Book Inspection</option>  -->
													<!--<option value="2"> Buy Assured</option>  -->
												</select>
												<span style="float:right;" id="formtype-parsley-error"></span> 
											</div>
										</div>
										
										<div class="col-md-3" id="make_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="make">Make <span style="color:red">*</span></label>
												<select name="make[]" id='make' class="select2 form-select form-control select2-multiple"data-placeholder = "Select Make" data-parsley-errors-container = '#make-parsley-error' data-parsley-required-message = 'Make Required' required>
													<option value="">Select Make</option> <!--  multiple="multiple"  -->
													<?php 
													foreach($make as $row1)
													{ ?>
														<option value="{{$row1->make_id}}">{{$row1->make_name}}</option> <?php
													} ?>
												</select>
												<span style="float:right;" id="make-parsley-error"></span> 
											</div>
										</div>
											
										<div class="col-md-3" id="model_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="model">Model <span style="color:red">*</span></label>
												<select name="model" id="model" class="select2 form-select form-control select2-multiple" data-model="" multiple="multiple" data-parsley-errors-container='#model-parsley-error' data-parsley-required-message='Model Required' required>
												<option value="">Select Model</option>
													<!--<?php 
													//foreach($model as $row2)
													//{ ?>
														<option value="<?php //echo $row2->model_id;?>"><?php //echo $row2->model_name;?> </option> <?php
													//} ?>-->
												</select>
												<span style="float:right;" id="model-parsley-error"></span> 
											</div>
										</div>
										
										<div class="col-md-3" id="vehicle_plate_no_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="lead_vehicle_plate_no">Vehicle Plate No<span style="color:red"> </span></label>
												<input name="lead_vehicle_plate_no" id="lead_vehicle_plate_no" class="form-control" data-parsley-errors-container='#lead_vehicle_plate_no-parsley-error' data-parsley-required-message='Vehicle Plate no Required'>
												<span style="float:right;" id="lead_vehicle_plate_no-parsley-error"></span> 
											</div>
										</div>
											
										<div class="col-md-3" id="year_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="year">Year</label>
												<input type="text" name="year" id="year" class="form-control form-select">
											 
											</div>
										</div>
										
										<div class="col-md-3" id="yearfrom_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="">Year From<span style="color:red">*</span></label>
												<select name="yearfrom" id="yearfrom" class="form-control form-select">
													<option value="">Select from year</option>
													<?php 
													for($i=1995;$i<=date("Y")+1;$i++)
													{ ?>
														<option value="<?php echo $i;?>"><?php echo $i;?></option> <?php
													} ?>
												</select>
											 
											</div>
										</div>
										
										<div class="col-md-3" id="yearto_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="">Year To<span style="color:red">*</span></label>
												<select name="yearto" id="yearto" class="form-control form-select" >
													<option value="">Select to year</option>
													<?php 
													for($i=1995;$i<=date("Y")+1;$i++)
													{ ?>
														<option value="<?php echo $i;?>"><?php echo $i;?></option> <?php
													} ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-3" id="budget_div" style="margin-top:-9px; ">
											<div class="mb-3">
												<label for="">Budget</label>
												<input type="text" name="budget" id="budget" class="form-control">
											</div>
										</div>
										 
										<div class="col-md-3" id="color_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="color">Color</label>
												<input type="text" name="color" id="color" class="form-control">
											</div>
										</div>
										
										<div class="col-md-3" id="color_ar_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="color_ar">Color in arabic</label>
												<input type="text" name="color_ar" id="color_ar" class="form-control">
											</div>
										</div>
											
										<div class="col-md-3" id="sellername_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellername">Seller Name</label>
												<input type="text" name="sellername" id="sellername" class="form-control">
												<span id="sellername_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
										
										<div class="col-md-3" id="sellername_ar_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellername_ar">Seller Name in arabic</label>
												<input type="text" name="sellername_ar" id="sellername_ar" class="form-control">
												<span id="sellernamear_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
										
										<div class="col-md-3" id="sellermobile_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellermobile">Seller Mobile</label>
												<input type="number" name="sellermobile" id="sellermobile" class="form-control">
												<span id="sellermobile_error" class="error" style="color:red;font-size:13px;float:right;margin-top:-10px"></span>
											</div>
										</div>
											
										<div class="col-md-3" id="location_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="location">Location</label>
												<select name="location" id="location" class="form-control form-select">
												<option value="">Select Location</option>
													<?php
													foreach($location as $lRow)
													{ ?>
														<option value="{{$lRow->location_id}}">{{$lRow->location_name}}</option> <?php 
													} ?>
												</select>
												<span id="location_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
											
										<div class="col-md-3" id="yourmobile_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="yourmobile">Your Mobile <span style="color:red">*</span></label>
												<input type="number" name="yourmobile" id="yourmobile" class="form-control" required>
											</div>
										</div>
											
										<div class="col-md-3" id="additionaldet_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="additionaldet">Additional Details (optional)</label>
												<textarea name="additionaldet" id="additionaldet" class="form-control"></textarea>
											</div>
										</div>
										
										<div class="col-md-3" id="package_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="package">Package</label>
												<select name="package" id="package" class="form-control form-select">
												<option value="">Select Package</option>
													<?php // print_r($package);
													foreach($package as $PackRow)
													{ ?>
														<option value="{{$PackRow->package_id}}">{{$PackRow->package_name}}- {{$PackRow->package_payable}}</option> <?php 
													} ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-3" id="payment_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="payment">Mode of Payment</label>
													<select name="payment" id="payment" class="form-control form-select">
														<option value="">Select Mode of Payment</option>
														<option value="1">Online Payment</option>
														<option value="2">Offline Payment</option>
													</select>
											</div>
										</div>
						
						                <div class="col-md-3" id="make_model_year_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="payment">Make/Model/Year</label>
												<input type="text" name="make_model_year" id="make_model_year" class="form-control">
											</div>
										</div>
										
										<div class="col-md-6" id="knowmore_div" style="margin-top:-9px">
											<div class="mb-3">
												<input class="form-check-input" type="checkbox" value="1" name="knowmore" id="knowmore">
												<label class="form-check-label" for="knowmore">
													If I buy the car, I would like to know more about Service Contracts &
													Extended Warranties.
												</label>
											</div>
										</div>
								 
									</div>
									<!------------- Lead INfo ------------->
										<div class="col-md-3" style="margin-top:14px">
											<div class="flex-wrap gap-3  editButton" <?php if(empty($data)) { ?> style="display:none;" <?php } ?> id="edit">
												<a href="#" class="btn btn-primary waves-effect waves-light"  onclick="setTabUpdate(this)" class="btn btn-info btn-block"> Update </a>
											</div>
											<div class="flex-wrap gap-3  saveButton" style="margin-top:4px">
												<a href="#" <?php if(!empty($data)) { ?> style="display:none" <?php } ?> class="btn btn-primary waves-effect waves-light" id="add" onclick="setTab(this)" class="btn btn-info btn-block">Save</a>&nbsp;
												<a href="#" <?php if(!empty($data)) { ?> style="display:none" <?php } ?> id="reset" onclick="clear_Form()" class="btn btn-danger waves-effect waves-light">Reset</a>
											</div>   
										</div>  

									</div>
						
								</form> <!-- form end -->
               
							</div>  <!---- row ---->
                    
						</div>
					</div>
				</div>
            </div> <!-- End Form Layout -->
						
						<!---- Filter ---->
						<div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
										<div class="row">
										
											<div class="col-md-2"> 
												<div class="mb-3">
												<?php $flr_source='';
												if(@session('filter_lead_source'))
												  $flr_source=session('filter_lead_source'); ?>
													{!! html()->select('filter_source', $sources , $flr_source)->attributes([ 'class'=>'form-control form-select select2','id'=> 'filter_source', "onchange"=>"filtering('','',filter_source.value)"])->placeholder('Select Source') !!}
												</div>
											</div>
											
											<div class="col-md-2"> 
												<div class="mb-3">
													<?php 
														$flr_staff='';
														if(@session('filter_staff'))
														$flr_staff=session('filter_staff');
													    //var_dump($flr_staff); ?>
													{!! html()->select('filter_staff', $users , $flr_staff)->attributes([ 'class'=>'form-control select2','id'=>'filter_staff','required'=> 'required','placeholder'=>'Select Staff'])->placeholder('Select Staff')->required() !!}
												</div>
											</div>
								 
											<!----------------------------------------------------------->		
											<!-- //include("leads filter_form")   -->                         
											<div class="col-md-2"> 
												<div class="mb-3">
												<div class="form-group"> 
													<button type="button" class="btn btn-danger waves-effect waves-light" onclick="filtering('unset')"> Reset</button>
												</div>
												</div>
											</div>
										</div>
					 
								<!------------------ Assign --------------------->						
								<form id="assignForm">
									<div class="row">
										<div class="col-md-2" style="padding-top:1px; padding-left:12px">
											<div class="mb-3">
												{!! html()->select('assign_lead_staff', $users , null)->attributes([ 'class'=>'form-select form-control select2', 'id'=> 'assign_lead_staff', 'data-parsley-errors-container'=>"#error_assigns", 'required'=>'required', 'data-parsley-required-message'=>'Please Select a Staff'])->placeholder('Select Staff')->required() !!}
												<div id="error_assigns" style="color:red;font-size:12px"></div>
												<div id="error_assigns_lead"  style="color:red;font-size:12px"></div>
											</div>  
										</div>  
										<div class="col-md-3">
											<div class="flex-wrap gap-3  saveButton" style="">
												<div class="mb-3">
													<a type="button" id="assign_btn" class="btn btn-primary mr-2">Assign</a>&nbsp;	
													<a type="button" id="assign_btn_delete" class="btn btn-warning mr-2">Delete</a>&nbsp;
													<!-- <a href='{{url("leads/export")}}' id="excelexport" target="_blank" class="btn btn-success" style="width:100px !important;background-color:green; color:#FFFFFF;"><i class="fa fa-file-excel-o"></i>Excel Export</a> -->
												</div>  
											</div>  
										</div>  
									</div>  
								</form>
								<!------------------ Assign -------------------->								
										
									<div style="margin-top:-2px">
											<!--h4 class="card-title">Added Company</h4-->
											<!--p class="card-title-desc"></p-->
										<div class="table-responsive">
											<table id="lead_table" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">   <!-- nowrap -->
												<thead>
												<tr>
													<th><input name="select_all" value="1" type="checkbox" style="display:none;"></th>
													<th class="sno">#</th>
													<th style="text-align:center;" width="9%!important">Date</th>
													<th>Name</th>
													<th>Mobile</th>
													<th>Package</th>
													<th>Form Type</th>
													<th>Source </th>
													<th style="text-align:center;">Assign To</th>
													<!--th style="width=5%">Assign Date</th-->
													<!-- <th>Campaign</th> -->
													<th>Status</th>
													<th class="actionwidth">Action</th>
												</tr>
												</thead>
												<tbody> </tbody>
											</table>
										</div>	
									</div>
									
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
			
		</div> <!-- container-fluid -->
     </div> <!-- End Page-content -->

	<!------------------------     Delete modal      ---------------------->						
		<div class="modal fade" id="delete_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete Lead</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}
 						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_lead_id' id="del_lead_id" value=''> 
						 Are you sure want to delete the lead <span id="delete_lead"></span>
						{!! html()->form()->close() !!}  
                    </div>
                    <div class="modal-footer">
						<button type="button" class="btn btn-primary" onclick="deleteLead(del_lead_id.value)">Delete</button>
                        <!--button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button-->
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
			</div>
		</div>
  <!------------------------ Delete modal ---------------------->		                     

 <!------------------------ followup modal ---------------------->

<div class="modal fade bs-example-modal-xl" id="modal_followup"  role="dialog" aria-labelledby="exampleModalCenterLabel"  aria-modal="true" style="display:none">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"> </span></button>
            </div>
            <div class="modal-body">
            <div id="view_modal_body">   </div>
                        
            <!--<input type="text" id="modal_lead_ids" name="modal_lead_ids" >-->
                
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>    
 <!------------------------     view modal      ---------------------->

 @endsection
 
 @section('js')
<script type="text/javascript">
  var public_path    = '<?php echo url('/');?>';
  var url_lead_table = '{{URL::to("leads/get-list")}}';
  var url_setfilter_campaign = "{{url('/leads/setFilterCampaign')}}";
</script>
      
		<!-- Required datatable js -->
        <script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
		<!-- Datatable init js -->
        <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
        <!-- plugins -->
        <script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>       
        <!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
		
	   	<!-- parsleyjs -->
		<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>	
		<script src="{{asset('module.js/Leads/index.js?ver=2.4')}}"></script>  
		<script src="{{asset('module.js/Leads/main.js?ver=1.1') }}"></script>  
		<script src="{{asset('module.js/main.js?ver=1.2')}}"></script>
	
	<script>
    	var url_datatable = "{{url('leads/get-list')}}";
    	var url_edit      = "{{url('leads/getleadsdata')}}";
		
		var url_view_followup        = "{{url('/leads/set_lead_session')}}";
		var url_view_followup_table  = "{{url('/leads/set_lead_session_followtable')}}";
        var url_view_followup_tables = "{{url('/leads/set_lead_session_followtables')}}";
		
		var url_add_followup    = "{{url('/leads/add_followup')}}";
		var url_setfilter_staff = '{{URL::to("leads/setFilterStaff")}}'; 
		var url_staffData       = '{{URL::to("leads/getstaff")}}'; 
		var url_assignenquery   = '{{URL::to("leads/assignenquerydata")}}';
    </script>

<script type="text/javascript">
$( document ).ready(function() 
{
  	$('#branch_state').trigger('change');
});
$('#branch_state').change(function()
{
	var state = $(this).val();
	
	if(state)
    { 
		$.ajax({
          type: 'GET',
          url: 'leads/get_district',
          data: { state: state },
          success:function(res)
            {              
            $("#company_city").empty();
            $("#company_city").append('<option value="">--- District ---</option>');
            if(res)
              {
              var a = $('#company_city').attr('data-city'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#company_city").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#company_city").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              }
            }
         });
    }
  else
    {
    $("#company_city").empty();
    $("#company_city").append('<option value="">--- District ---</option>');
    } 
  });
	
</script>
<!--------------------------------------------->
<script type="text/javascript">
$( document ).ready(function() 
{
  	$('#company_city').trigger('change');
});
$('#company_city').change(function()
{
	var branch = $(this).val();

	if(branch)
    { 
		$.ajax({
          type: 'GET',
          url: 'leads/get_branch',
          data: { branch: branch },
          success:function(res)
            {              
            $("#centre").empty();
            $("#centre").append('<option value="">Select</option>');
            if(res)
              {
              var a = $('#centre').attr('data-centre'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#centre").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#centre").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              }
            }
         });
    }
  else
    {
    $("#centre").empty();
    $("#centre").append('<option value="">Select</option>');
    } 
  });
	
</script>

<!--------------------------------------------->

<script>
var token = $('meta[name="csrf-token"]').attr('content');
//function set_filter_staff(staff)
//  {
$('#filter_staff').on('change', function(e)
{
	var staff = $(this).val();
       $.ajax({
                   type: 'POST',
                   url: url_setfilter_staff,           	
            	   data:{'_token':token,'staff':staff},
                   success: function (data) 
					{
						$('#lead_table').DataTable().ajax.reload();
					}
						
				   });
 });

       /********** select basic reg info  ************/
            function searchRegistrtaion(mob,element)
            {
                $.ajax({
                   type: 'GET',
                   url: "leads/searchRegistration",
            	   dataType:'JSON',
            	   data:{'mob':mob},
                    success: function (data) {
						
                        if(data.status==1)
                        {   
            			$('#breg_id').val(data.result.breg_id);
            			$('#first_name').val(data.result.breg_fname);
            			$('#last_name').val(data.result.breg_lname);
            			$('#email').val(data.result.breg_email);
            			$('#city').val(data.result.breg_city);
            			
            			//console.log(data.result.breg_mobilecountrydata);
            			$( "div .iti__selected-flag" ).find('.iti__flag').removeClass('iti__in');
                		$( "div .iti__selected-flag" ).find('.iti__flag').addClass('iti__'+data.country_code);
                		$( "div .iti__selected-flag" ).attr("title", data.result.breg_mobilecountrydata);
                		  
            			$('#mobile').val(data.result.breg_mob).trigger('change');
            			$("#phonecode").val(data.result.breg_mob_code).trigger('change.select2');
            			$('#country').val(data.result.breg_country).trigger('change.select2');
            			$('#alt_mobile').val(data.result.breg_alternative_no);
            			
            			$('#lead_form').parsley().validate();
            			flag_function(data.flag,data.result.breg_mob_code);
                        }
                        else
                        {  
                            $('#breg_id').val("");
							$('#first_name').val("");
							$('#email').val("");
                        }
                    }
                });
            }
		/********** select basic reg info  ************/
</script>

<script>
//next & previous tab
function setTab(element)
{
	var linkHref = $(element).parents('.tab-pane').attr('id');
	//if(linkHref=="basic_info")
	//{
        $("#message").removeAttr('required');
        <?php
        if(!$errors->any())
        { ?>
			$('#lead_form').parsley().validate();
			if(!$('#lead_form').parsley().isValid())
			{
				return; 
			} <?php
        } ?>
          
	/*********** insert or update basic reg details ***********/        
		var reg_id   = $("#breg_id").val();
		var email    = $("#email").val();
		var fname    = $("#first_name").val();
		var fname_ar = $("#fname_ar").val();
		var mob      = $("#mobile").val();
		var phone_code = $("#phonecode").val();
		
		var source = $("#source").val();
		var centre = $("#centre").val();
		var breg_place = $("#breg_place").val();
		var message    = $("#message").val();	
		
		var branch_state = $("#branch_state").val();
		var company_city = $("#company_city").val();
		
		/******* Lead info ******/
		var formtype = $("#formtype").val();
		
		var make     = $("#make").val();
		var model    = $("#model").val();
		var plate_no = $("#lead_vehicle_plate_no").val();
		
		var year     = $("#year").val();
		var color    = $("#color").val();
		var color_ar = $("#color_ar").val();
		var sellername    = $("#sellername").val();
		var sellername_ar = $("#sellername_ar").val();
		var sellermobile  = $("#sellermobile").val();
		var location      = $("#location").val();
		var yourmobile    = $("#yourmobile").val();
		var additionaldet = $("#additionaldet").val();
		var lpackage      = $("#package").val();
		var payment       = $("#payment").val();
		
		var yearfrom   = $("#yearfrom").val();
		var yearto     = $("#yearto").val();
		var budget     = $("#budget").val();
		var fullname   = $("#fullname").val();
		//var yourmobile = $("#yourmobile").val();
		var knowmore   = $("#knowmore").val();
		 
		/******* Lead info ******/
		var mobilecodedata =  $('#mobilecodedata').val();
		 
		$.ajax({
                type: 'GET',
                url: "leads/insert_basic_info",
            	dataType:'JSON',
            	data:{'reg_id':reg_id,'mob':mob,'email':email,'fname':fname,'fname_ar':fname_ar,'branch_state':branch_state,'company_city':company_city,'centre':centre,'source':source,'message':message,'breg_place':breg_place,'phone_code':phone_code,'mobilecodedata':mobilecodedata,'formtype':formtype,'make':make,'model':model,'year':year,'color':color,'color_ar':color_ar,'sellername':sellername,'sellername_ar':sellername_ar,'sellermobile':sellermobile,'location':location,'yourmobile':yourmobile,'additionaldet':additionaldet,'lpackage':lpackage,'payment':payment, 'yearfrom':yearfrom,'yearto':yearto,'budget':budget,'knowmore':knowmore,'plate_no':plate_no },
                success: function (data) 
				{
                    if(data.status==1)
                    {
                        $("#breg_id").val(data.reg_id);
                        $("#lead_id").val(data.lead_id);
                        $("#formtypeid").val(data.formtypeid);

                        $("#lead_table").DataTable().ajax.reload();
                        
                        //$("#lead_info").find("input, select:not(#event)").attr('required','required');
    
						Command: toastr["success"]("Basic Details Submitted Successfully")
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
                   
						//lead_form
						window.location.reload();
                       }
                    }
                });

        $("#lead_info").find("input, select").removeAttr('required');
	}

////////////// UPDATE ///////////////

function setTabUpdate(element)
{
	var linkHref = $(element).parents('.tab-pane').attr('id');
	//if(linkHref=="basic_info")
	//{
		$("#message").removeAttr('required');
		<?php
		if(!$errors->any())
		{?>
			$('#lead_form').parsley().validate();
			if(!$('#lead_form').parsley().isValid())
			{         
				return; 
			} <?php
		} ?>
			
		/*********** insert or update basic reg details ***********/
		var lead_id = $("#lead_id").val();
		var reg_id  = $("#breg_id").val();
		var mob     = $("#mobile").val();
		var phone_code   = $("#phonecode").val();
		var email        = $("#email").val();
		var fname        = $("#first_name").val();
		var fname_ar     = $("#fname_ar").val();
		var branch_state = $("#branch_state").val();
		var company_city = $("#company_city").val();
	
		var centre  = $("#centre").val();
		var source  = $("#source").val();	
		var message = $("#message").val();		
		var breg_place = $("#breg_place").val();
	 
		var mobilecodedata =  $('#mobilecodedata').val();
		
		/******* Lead info ******/
		var formtype = $("#formtype").val();
		var make     = $("#make").val();
		var model    = $("#model").val();
		var plate_no = $("#lead_vehicle_plate_no").val();
		
		var year     = $("#year").val();
		var color    = $("#color").val();
		var color_ar = $("#color_ar").val();
		var sellername    = $("#sellername").val();
		var sellername_ar = $("#sellername_ar").val();
		var sellermobile  = $("#sellermobile").val();
		var location      = $("#location").val();
		var yourmobile    = $("#yourmobile").val();
		var additionaldet = $("#additionaldet").val();
		var lpackage      = $("#package").val();
		var payment       = $("#payment").val();
		
		var yearfrom   = $("#yearfrom").val();
		var yearto     = $("#yearto").val();
		var budget     = $("#budget").val();
		var fullname   = $("#fullname").val();
		//var yourmobile = $("#yourmobile").val();
		var knowmore   = $("#knowmore").val();
		/******* Lead info ******/
		
		$.ajax({
			type: 'GET',
			url : "leads/update_basic_info",
			dataType : 'JSON',
            data : {'lead_id':lead_id,'reg_id':reg_id,'mob':mob,'email':email,'fname':fname,'fname_ar':fname_ar,'branch_state':branch_state,'company_city':company_city,'centre':centre,'source':source,'message':message,'breg_place':breg_place,'phone_code':phone_code,'mobilecodedata':mobilecodedata, 'formtype':formtype,'make':make,'model':model,'year':year,'color':color,'color_ar':color_ar,'sellername':sellername,'sellername_ar':sellername_ar,'sellermobile':sellermobile,'location':location,'yourmobile':yourmobile,'additionaldet':additionaldet,'lpackage':lpackage,'payment':payment, 'yearfrom':yearfrom,'yearto':yearto,'budget':budget,'knowmore':knowmore, 'plate_no':plate_no },
			
			success: function (data) 
			{
				if(data.status == 1)
				{                          
					$("#breg_id").val(data.reg_id);						   
					$("#lead_id").val(data.lead_id);
					$("#formtypeid").val(data.formtypeid);
					$("#lead_table").DataTable().ajax.reload();                          

                    $("#lead_info").find("input, select:not(#event)").attr('required','required');
         
					Command: toastr["success"]("Basic Details Submitted Successfully")
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
					}
				}
			});

		$('a.nav-link').not('.active').css('pointer-events', 'none');
}
</script>

<script type="text/javascript"> 
function getmobilecode()
{
    var title = $('div.iti__selected-flag').attr('title');
	$('#mobilecodedata').val(title);
}
$(document).ready(function(){
    $(document).on('click','ul li',function(){
        var title = $('div.iti__selected-flag').attr('title');
        $('#mobilecodedata').val(title);
    })
})
</script>	

<script>
	function changeStatus(status,name)
	{
		$(this).val('').trigger('change');
		//$("#follow_status").val('').trigger('change.select2');
		//$('#follow_status').val('').trigger('change');
		//$("#follow_status").trigger('change.select2');
		//$('#follow_status').val(null).trigger("change");
		//$('#follow_status').trigger('click');
		
		$("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
		$("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
		$("#div_follow2").hide();$("#div_follow2").find('select').removeAttr('required');
  
        $("#intm").html('');
        $("#inty").html('');
   
		$("#btn_followup_div").attr('class','col-md-3');
		$("#div_follow3").show();
		//$("#commentid").html('*');
		$("#error_followup_remark").show();
          
		$("#div_follow3").find('textarea').attr('required','required');
		if(status=="1")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			//  $("#div_follow1").find('input').attr('required','required');
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
         
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');
		}
		else if(status=="2")
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
                   
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');        
		}
		else if(status=="3")
		{
			$("#div_follow1").show();
		}
		else if(status=="4")
		{
			//$("#div_follow1").show();
			//$("#div_follow4").show();
			//  $("#div_follow1").find('input').attr('required','required');
			$("#div_follow4").find('select').attr('required','required');
			$("#div_follow2").val('');
			$("#div_follow3").find('textarea').removeAttr('required');
			$("#commentid").html('');              
		}
		else if(status=="5")
		{
			$("#div_follow1").hide();  
			//$("#div_follow4").show();
		}
		else if(status=="6")
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

<script>
 $(function(){
  $('#follow_status').select2({
    dropdownParent: $('#modal_followup')
  });
}); 

 $(function(){
  $('#assign_staff').select2({
    dropdownParent: $('#modal_followup')
  });
}); 
</script>


<script>
function addFollowUp(id,date,status,remarks,assigned_user,convertstatus)      
{
	var name = $('#follow_status option:selected').text();
	var staff = $("#assign_staff").val();
    
    /*$('#followup_form').parsley().validate();
      if(! $('#followup_form').parsley().isValid())
        {
            return false;
        }*/
	
	//$("#follow_status").find('select2').attr('required','required');

        $.ajax({
            type: 'POST',
            url: url_add_followup,
            dataType:'json',
            data:{'id':id,'date':date,'status':status,'name':name,'remarks':remarks,'_token':"{{csrf_token()}}",'assinged_user':assigned_user,'staff':staff},

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
 
 function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);
   
   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}
 
 // Handle click on checkbox
   $('#lead_table tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

      // Get row data
      var data = dTable.row($row).data();

      // Get row ID
      var rowId = data.lead_id;

      // Determine whether row ID is in the list of selected row IDs
      var index = $.inArray(rowId, rows_selected);

      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(dTable);
      //console.log(rows_selected);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });
   
  //var url_getAllLeads = "{{url('leads/getAllLeads')}}";
  
   $('thead input[name="select_all"]', dTable.table().container()).on('click', function(e){
     
      if(this.checked){
          var searching = $("input[type='search']").val();
           $.ajax({
                   type: 'POST',
                   url: "{{url('/leads/getAllLeads')}}",
            	   dataType:'json',
            	   data:{'_token':"{{csrf_token()}}",'search':searching},
                    success: function (data) {
                        rows_selected=[];
                        rows_selected=data;
                    }
                }); 
          
         $('#lead_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
          rows_selected=[];
         $('#lead_table tbody input[type="checkbox"]:checked').trigger('click');
      } 

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });        

   // Handle table draw event
   dTable.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(dTable);
   });
   
   
   
/*********** Assign Lead **************/
var token = $('meta[name="csrf-token"]').attr('content');
var url_assign_leads="{{url('/leads/assign_leads')}}";

$("#assign_btn").click(function()
{    
	var staff = $("#assign_lead_staff").val();

	$("#error_assigns").html("");
	$("#error_assigns_lead").html("")
	if(staff.length==0)
	{
		$("#error_assigns").html("Please Select Staff");
		return false;
	}

	if(rows_selected.length==0)
	{
		$("#error_assigns_lead").html("Please select at least one lead");
		return false;
	}

        $.ajax({
                   type: 'POST',
                   url: url_assign_leads,
            	   data:{'_token':token,'leads':rows_selected,'user_id':staff},
                    success: function (data) {
				
					Command: toastr["success"](data.text)
						toastr.options = {
						  "heading": "data.heading",
						  "text": "data.text",
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
					
    			rows_selected=[];
				//$("#lead_table").DataTable().ajax.reload();
    			$("#lead_table").DataTable().ajax.reload( null,false );
    			$("#assign_lead_staff").val("").trigger('change');
                    }
                }); 
})

/*********** Delete Assign Lead **************/

$("#assign_btn_delete").click(function()
{            
    $("#error_assigns").html("");
    $("#error_assigns_lead_delete").html("")

        if(rows_selected.length==0)
        {
            $("#error_assigns_lead_delete").html("please select at least one lead");
            return false;
        }
        $.ajax({
                   type: 'POST',
                   url: "{{url('/leads/assign_leads_delete')}}",
                   dataType:'json',
                   data:{'leads':rows_selected,'_token':"{{csrf_token()}}"},
                    success: function (data) 
					{
						Command: toastr["success"](data.text)
						toastr.options = {
						  "heading": "data.heading",
						  "text": "data.text",
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
						rows_selected=[];
						$("#lead_table").DataTable().ajax.reload();
                    }
                }); 
})
 </script>

<script>
$("select[name='centre']").change(function() 
{
    var branch_id = $("select[name='centre']").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
            type: 'POST',
            data: { '_token': token, 'id': branch_id },
            url: '<?php echo url('leads/set_branch') ?>',
            success: function(result) 
                {
                //location.reload();
                }
        });
});
	
function setFiltercampaign(campaign)
{
	$.ajax({
		type: 'GET',
		url: url_setfilter_campaign,
		dataType:'JSON',
		data:{'campaign':campaign},
		success: function (res) 
		{
			$('#lead_table').DataTable().ajax.reload();
        }
	});
}  
 
function clear_Form()
{
  	$('#lead_form').parsley().reset();
  	$('#lead_form')[0].reset();
}
</script>

<script>
$(document).ready(function() 
{
	$('#make_div').hide();
	$('#model_div').hide();
	$('#vehicle_plate_no_div').hide();
	$('#year_div').hide();
	$('#color_div').hide();      $('#color_ar_div').hide();
	$('#sellername_div').hide(); $('#sellername_ar_div').hide(); 
	$('#sellermobile_div').hide();
	$('#location_div').hide();
 
	$('#yearfrom_div').hide();
	$('#yearto_div').hide();
	$('#budget_div').hide();
	$('#knowmore_div').hide();
	$('#package_div').hide();
	$('#payment_div').hide();
	$('#make_model_year_div').hide(); 
	$('#yourmobile_div').hide();
	$('#additionaldet_div').hide();
 
	$('#formtype').on('change', function() 
	{
		var form = $(this).val();   
		if(form == 'Book Inspection')  // if(form == 1 )
		{
			$('#make_div').show();
			$('#model_div').show();
			$('#vehicle_plate_no_div').show();
			$('#year_div').show();
			$('#color_div').show();      $('#color_ar_div').show();
			$('#sellername_div').show(); $('#sellername_ar_div').show(); 
			$('#sellermobile_div').show();
			$('#location_div').show();
 			$('#package_div').show();
			$('#payment_div').show();
			$('#make_model_year_div').show(); 
			
			$('#yourmobile_div').show();
			$('#additionaldet_div').show();
			
			$('#yearfrom_div').hide();
			$('#yearto_div').hide();
			$('#budget_div').hide();
			$('#knowmore_div').hide();
		}
		else
		{
			$('#year_div').hide();
			$('#color_div').hide();      $('#color_ar_div').hide(); 
			$('#sellername_div').hide(); $('#sellername_ar_div').hide(); 
			$('#sellermobile_div').hide();
			$('#location_div').hide();
			$('#package_div').hide();
			$('#payment_div').hide();
			$('#make_model_year_div').hide(); 
			
			$('#make_div').show();
			$('#model_div').show();
			$('#vehicle_plate_no_div').show();
			$('#yearfrom_div').show();
			$('#yearto_div').show();
			$('#budget_div').show();
			$('#knowmore_div').show();
			
			$('#yourmobile_div').show();
			$('#additionaldet_div').show();
		}
		
	});
});
	  
</script>

<!--------- getModel --------->
<script type="text/javascript">
$( document ).ready(function() 
{
    $('#make').trigger('change');
    $('#make').change(function()
    {  
        var make = $(this).val();   
        if(make)
        { 
            $.ajax({
                type: 'GET',
                url: 'leads/getModel',
                data: { make: make },
                success:function(res)
                {              
                    $("#model").empty();
                    $("#model").append('<option value=""> Select Model </option>');
          
                    if(res)
                    {
                        var a = $('#model').attr('data-model'); 
                        $.each(res,function(key,value)
                        {
                            var x = "";
                            if(a === key)
                            {
                                x="selected";
                                $("#model").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                            }  
                            else
                            {
                                $("#model").append('<option value="'+key+'">'+value+'</option>');
                            }
                        });
                        
                        $('#model').trigger('change');
                    }
                }
            });
        }
    	else
        {
         
        }      
    });
});
</script>
 
@endsection     