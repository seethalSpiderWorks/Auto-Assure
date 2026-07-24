@extends('layouts.myfudapp')
@section('content')

<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }
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
										
										<div class="col-md-3" style="margin-top:-9px">
											<div class="mb-2">
												<label for="breg_whatsapp"><span id="linktitle">WhatsApp No</span><span style="color:red"> </span></label>
													<input type="number" <?php if(!empty($data)){ ?> value="<?php echo $data->breg_whatsapp; ?>" <?php } ?> class="form-control @error('breg_whatsapp') is-invalid @enderror" id="breg_whatsapp" name="breg_whatsapp" placeholder="Enter WhatsApp No" data-parsley-error-message="WhatsApp No Required" pattern="/^[0-9]{1,15}$/" data-parsley-errors-container='#breg_whatsapp-parsley-error'>
												<span style="float:right;" id="breg_whatsapp-parsley-error"></span>
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
													<?php $formType = !empty($data) ? $data->lead_form_type : ""; ?>
													<option value="">Select Form Type</option>
													
													<option value="Book Inspection" <?= ($formType == "Book Inspection") ? "selected" : "" ?>>
                                                        Book Inspection
                                                    </option>
                                        
                                                    <option value="Buy Assured" <?= ($formType == "Buy Assured") ? "selected" : "" ?>>
                                                        Buy Assured
                                                    </option>
													<!--<option value="Book Inspection"> Book Inspection</option>  -->
													<!--<option value="Buy Assured"> Buy Assured</option>  -->
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
														<option value="{{$row1->make_id}}" {{ (!empty($data) && $data->lead_make == $row1->make_id) ? 'selected' : '' }}>{{$row1->make_name}}</option> <?php
													} ?>
												</select>
												<span style="float:right;" id="make-parsley-error"></span> 
											</div>
										</div>
											
										<div class="col-md-3" id="model_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="model">Model <span style="color:red">*</span></label>
												<select name="model" id="model" class="select2 form-select form-control select2-multiple" data-model="" data-selected-model="{{ !empty($data) ? $data->lead_model : '' }}" multiple="multiple" data-parsley-errors-container='#model-parsley-error' data-parsley-required-message='Model Required' required>
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
												<input name="lead_vehicle_plate_no" id="lead_vehicle_plate_no" value="<?= !empty($data->lead_vehicle_plate_no) ? $data->lead_vehicle_plate_no : '' ?>" class="form-control" data-parsley-errors-container='#lead_vehicle_plate_no-parsley-error' data-parsley-required-message='Vehicle Plate no Required'>
												<span style="float:right;" id="lead_vehicle_plate_no-parsley-error"></span> 
											</div>
										</div>
											
										<div class="col-md-3" id="year_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="year">Year</label>
												<input type="text" name="year" id="year" value="<?= !empty($data->lead_year) ? $data->lead_year : '' ?>" class="form-control form-select">
											 
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
														<option value="<?php echo $i;?>" <?= (!empty($data) && $data->lead_year_from == $i) ? 'selected' : '' ?>><?php echo $i;?></option> <?php
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
														<option value="<?php echo $i;?>" <?= (!empty($data) && $data->lead_year_to == $i) ? 'selected' : '' ?>><?php echo $i;?></option> <?php
													} ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-3" id="budget_div" style="margin-top:-9px; ">
											<div class="mb-3">
												<label for="">Budget</label>
												<input type="text" name="budget" id="budget" value="{{ optional($data)->lead_budget }}" class="form-control">
											</div>
										</div>
										 
										<div class="col-md-3" id="color_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="color">Color</label>
												<input type="text" name="color" id="color" value="{{ optional($data)->lead_color }}" class="form-control">
											</div>
										</div>
										
										<div class="col-md-3" id="color_ar_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="color_ar">Color in arabic</label>
												<input type="text" name="color_ar" id="color_ar" value="{{ optional($data)->lead_color_ar }}" class="form-control">
											</div>
										</div>
											
										<div class="col-md-3" id="sellername_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellername">Seller Name</label>
												<input type="text" name="sellername" id="sellername" value="{{ optional($data)->lead_seller_name }}" class="form-control">
												<span id="sellername_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
										
										<div class="col-md-3" id="sellername_ar_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellername_ar">Seller Name in arabic</label>
												<input type="text" name="sellername_ar" id="sellername_ar" value="{{ optional($data)->lead_seller_name_ar }}" class="form-control">
												<span id="sellernamear_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
										
										<div class="col-md-3" id="sellermobile_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="sellermobile">Seller Mobile</label>
												<input type="number" name="sellermobile" id="sellermobile" value="{{ optional($data)->lead_seller_mobile }}" class="form-control">
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
														<option value="{{$lRow->location_id}}" {{ (!empty($data) && $data->lead_location == $lRow->location_id) ? 'selected' : '' }}>{{$lRow->location_name}}</option> <?php 
													} ?>
												</select>
												<span id="location_error" class="error" style="color:red; font-size:13px; float:right; margin-top:-10px"></span>
											</div>
										</div>
											
										<div class="col-md-3" id="yourmobile_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="yourmobile">Your Mobile <span style="color:red">*</span></label>
												<input type="number" name="yourmobile" id="yourmobile" value="{{ optional($data)->lead_your_mobile }}" class="form-control" required>
											</div>
										</div>
											
										<div class="col-md-3" id="additionaldet_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="additionaldet">Additional Details (optional)</label>
												<textarea name="additionaldet" id="additionaldet" class="form-control">{{ optional($data)->lead_add_details }}</textarea>
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
														<option value="{{$PackRow->package_id}}" {{ (!empty($data) && $data->lead_pack_name_id == $PackRow->package_id) ? 'selected' : '' }}>{{$PackRow->package_name}}- {{$PackRow->package_payable}}</option> <?php 
													} ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-3" id="payment_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="payment">Mode of Payment</label>
													<select name="payment" id="payment" class="form-control form-select">
														<option value="">Select Mode of Payment</option>
														<option value="1" {{ (!empty($data) && $data->lead_mode_pay == 1) ? 'selected' : '' }}>Online Payment</option>
														<option value="2" {{ (!empty($data) && $data->lead_mode_pay == 2) ? 'selected' : '' }}>Offline Payment</option>
													</select>
											</div>
										</div>
						
						                <div class="col-md-3" id="make_model_year_div" style="margin-top:-9px">
											<div class="mb-3">
												<label for="payment">Make/Model/Year</label>
												<input type="text" name="make_model_year" id="make_model_year" value="{{ optional($data)->make_model_year }}" class="form-control">
											</div>
										</div>
										
										<div class="col-md-6" id="knowmore_div" style="margin-top:-9px">
											<div class="mb-3">
												<input class="form-check-input" type="checkbox" value="1" name="knowmore" id="knowmore" {{ optional($data)->lead_know_more ? 'checked' : '' }}>
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

							{{-- ===== Inspection (edit only) — same card, after Lead Info ===== --}}
							@if(!empty($data))
							<div class="insp-block" style="padding:6px 14px 0 14px;">
							<div class="row"><div class="mb-3"> <h5> Inspection </h5> </div></div>
							<input type="hidden" id="insp_lead_id" value="{{ $data->lead_id }}">
							<div class="row">
								<div class="col-md-4">
									<div class="mb-3">
										<label class="form-label">Inspection Template</label>
										<select id="insp_type" class="form-control form-select">
											<option value="">Select Template</option>
											@foreach(($inspectionTypes ?? []) as $tid => $tname)
												<option value="{{ $tid }}" {{ (optional($leadInspection)->inspection_type_id == $tid) ? 'selected' : '' }}>{{ $tname }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="mb-3">
										<label class="form-label">Assigned Person</label>
											@php
												// Prefer the inspection's technician; if no inspection exists yet
												// but the lead was assigned from the list page, fall back to the
												// lead's assigned user so the technician is still shown here.
												$assignedTech = optional($leadInspection)->technician_id ?: (optional($data)->lead_assigned_users ?? '');
											@endphp
										<select id="insp_staff" class="form-control form-select">
											<option value="">Select Staff</option>
											@foreach(($users ?? []) as $uid => $uname)
												<option value="{{ $uid }}" {{ ($assignedTech && $assignedTech == $uid) ? 'selected' : '' }}>{{ $uname }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="mb-3">
										<label class="form-label">Scheduled Date &amp; Time</label>
										<input type="datetime-local" id="insp_scheduled" class="form-control" value="{{ ($leadInspection && $leadInspection->scheduled_at) ? $leadInspection->scheduled_at->format('Y-m-d\TH:i') : '' }}">
									</div>
								</div>
								<div class="col-md-1 d-flex align-items-end">
									<div class="mb-3 w-100">
										<button type="button" id="insp_update_btn" class="btn btn-primary w-100" title="Update Inspection"><i class="bx bx-save"></i></button>
									</div>
								</div>
							</div>
														<div id="insp_msg" style="font-size:12px;margin-top:6px;"></div>
							</div>
							@endif

						</div>
					</div>
				</div>
            </div> <!-- End Form Layout -->

			{{-- ============== Lead Notes (edit only) ============== --}}
			@if(!empty($data))
				<div class="row">
					<div class="col-12">
						<div class="card note-card">
							<div class="card-body">
								<div class="note-head">
									<h5 class="note-title"><i class="bx bx-note"></i> Notes</h5>
									<span class="note-count" id="note_count">{{ $notes->count() }}</span>
								</div>

								<div class="note-add">
									<textarea id="note_text" class="form-control" rows="2" maxlength="5000" placeholder="Write a note about this lead…"></textarea>
									<button type="button" id="note_add_btn" class="btn note-btn"><i class="bx bx-plus"></i> Add Note</button>
								</div>
								<div id="note_error" class="note-error"></div>

								<div class="note-list" id="note_list">
									@forelse($notes as $n)
										<div class="note-item" data-note-id="{{ $n->note_id }}">
											<div class="note-item__avatar">{{ strtoupper(substr($n->author ?: 'U',0,1)) }}</div>
											<div class="note-item__body">
												<div class="note-item__head">
													<span class="note-item__author">{{ $n->author ?: 'User' }}</span>
													<span class="note-item__date">{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y, h:i A') }}</span>
													<button type="button" class="note-item__edit" title="Edit" onclick="editLeadNote(this)"><i class="bx bx-edit"></i></button>
													<button type="button" class="note-item__del" title="Delete" onclick="deleteLeadNote({{ $n->note_id }})"><i class="bx bx-trash"></i></button>
												</div>
												<div class="note-item__text">{{ $n->note_text }}</div>
											</div>
										</div>
									@empty
										<div class="note-empty" id="note_empty">No notes yet. Add the first one above.</div>
									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			@endif


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
												<select id="assign_insp_type" class="form-control form-select" style="width:100%">
													<option value="" disabled selected>Inspection Template</option>
													@foreach(($inspectionTypes ?? []) as $tid => $tname)
														<option value="{{ $tid }}">{{ $tname }}</option>
													@endforeach
												</select>
												<div id="error_assigns_type" style="color:red;font-size:12px"></div>
											</div>
										</div>
										<div class="col-md-2" style="padding-top:1px;">
											<div class="mb-3">
												<input type="datetime-local" id="assign_insp_scheduled" class="form-control" placeholder="Scheduled (optional)" title="Scheduled date &amp; time (optional)">
											</div>
										</div>
										<div class="col-md-2" style="padding-top:1px; padding-left:12px">
											<div class="mb-3">
												{!! html()->select('assign_lead_staff', $users , null)->attributes([ 'class'=>'form-select form-control', 'id'=> 'assign_lead_staff', 'data-parsley-errors-container'=>"#error_assigns", 'required'=>'required', 'data-parsley-required-message'=>'Please Select a Staff'])->placeholder('Select Staff')->required() !!}
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
													<th style="text-align:center;">Inspection</th>
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

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    :root { --nt-dark:#00263D; --nt-brand:#04B084; }
    .note-card { border:0; border-radius:16px; box-shadow:0 4px 20px rgba(16,40,70,.06); }
    .note-head { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .note-title { margin:0; font-weight:700; color:var(--nt-dark); display:flex; align-items:center; gap:8px; }
    .note-title i { color:var(--nt-brand); font-size:20px; }
    .note-count { font-size:12px; font-weight:700; background:#e7f8ef; color:var(--nt-brand); border-radius:20px; padding:2px 11px; }

    .note-add { display:flex; gap:10px; align-items:flex-start; }
    .note-add textarea { border:1px solid #e4e8ee; border-radius:12px; resize:vertical; font-size:14px; }
    .note-add textarea:focus { border-color:var(--nt-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    .note-btn { background:var(--nt-dark); border-color:var(--nt-dark); color:#fff; font-weight:600; border-radius:10px; white-space:nowrap; align-self:stretch; }
    .note-btn:hover { background:var(--nt-brand); border-color:var(--nt-brand); color:#fff; }
    .note-error { color:#e5484d; font-size:12.5px; min-height:16px; margin-top:6px; }

    .note-list { margin-top:8px; display:flex; flex-direction:column; gap:12px; }
    .note-item { display:flex; gap:12px; background:#f9fbfc; border:1px solid #eef1f5; border-radius:12px; padding:12px 14px; }
    .note-item__avatar { flex:0 0 auto; width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--nt-dark),var(--nt-brand)); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .note-item__body { flex:1; min-width:0; }
    .note-item__head { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    .note-item__author { font-weight:700; color:#1f2a37; font-size:13.5px; }
    .note-item__date { font-size:12px; color:#98a2b3; }
    .note-item__edit { margin-left:auto; border:0; background:transparent; color:#c2c8d2; font-size:16px; cursor:pointer; padding:0 2px; }
    .note-item__edit:hover { color:var(--nt-brand); }
    .note-item__del { border:0; background:transparent; color:#c2c8d2; font-size:16px; cursor:pointer; padding:0 2px; }
    .note-item__del:hover { color:#e5484d; }
    .note-item__editbox { margin-top:8px; }
    .note-item__editbox .note-edit-ta { border:1px solid #e4e8ee; border-radius:10px; font-size:13.5px; }
    .note-item__editbox .note-edit-ta:focus { border-color:var(--nt-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    .note-edit-actions { display:flex; align-items:center; gap:8px; margin-top:6px; }
    .note-edit-actions .note-btn { padding:4px 14px; }
    .note-edit-err { color:#e5484d; font-size:12px; }
    .note-item__text { font-size:13.5px; color:#475467; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
    .note-empty { color:#98a2b3; font-size:13.5px; padding:10px 2px; }
    @media (max-width:575px){ .note-add { flex-direction:column; } .note-btn { width:100%; } }

    /* ===== Modern edit-form theme ===== */
    .page-title-box h4 { color:var(--nt-dark); font-weight:700; }
    .page-content .card { border:0; border-radius:16px; box-shadow:0 4px 20px rgba(16,40,70,.06); }
    .page-content .card > .card-body { padding:24px 26px; }

    #lead_form label { font-size:12.5px; font-weight:600; color:#5b6472; margin-bottom:.3rem; }
    #lead_form .form-control, #lead_form .form-select { border:1px solid #e4e8ee; border-radius:10px; font-size:14px; }
    #lead_form .form-control:focus, #lead_form .form-select:focus { border-color:var(--nt-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    #lead_form textarea.form-control { border-radius:12px; }
    #lead_form hr { border-top:1px solid #eef1f5; opacity:1; margin:1.4rem 0; }

    /* Section heading ("Lead Info") with brand accent */
    #lead_form h5 { color:var(--nt-dark); font-weight:700; display:inline-flex; align-items:center; gap:9px; font-size:1rem; }
    #lead_form h5::before { content:''; width:16px; height:3px; border-radius:3px; background:var(--nt-brand); }

    /* Buttons */
    .btn { border-radius:10px; }
    #lead_form .btn-primary, .editButton .btn-primary, .saveButton .btn-primary { background:var(--nt-dark); border-color:var(--nt-dark); font-weight:600; }
    #lead_form .btn-primary:hover, .editButton .btn-primary:hover, .saveButton .btn-primary:hover { background:var(--nt-brand); border-color:var(--nt-brand); }

    /* select2 to match rounded inputs */
    #lead_form .select2-container--default .select2-selection--single { border:1px solid #e4e8ee !important; border-radius:10px !important; height:38px !important; }
    #lead_form .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px !important; color:#344054; }
    #lead_form .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px !important; }
    #lead_form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--nt-brand) !important; }
</style>
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
		<script src="{{asset('module.js/Leads/index.js?ver=3.0')}}"></script>  
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
		var reg_id     = $("#breg_id").val();
		var email      = $("#email").val();
		var fname      = $("#first_name").val();
		var fname_ar   = $("#fname_ar").val();
		var mob        = $("#mobile").val();
		var phone_code = $("#phonecode").val();
		
		var source     = $("#source").val();
		var centre     = $("#centre").val();
		var breg_place = $("#breg_place").val();
		var message    = $("#message").val();	
		var whatsapp   = $("#breg_whatsapp").val();	
		
		var branch_state = $("#branch_state").val();
		var company_city = $("#company_city").val();
		
		/******* Lead info ******/
		var formtype      = $("#formtype").val();
		var make          = $("#make").val();
		var model         = $("#model").val();
		var plate_no      = $("#lead_vehicle_plate_no").val();
		
		var year          = $("#year").val();
		var color         = $("#color").val();
		var color_ar      = $("#color_ar").val();
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
		var make_model_year = $("#make_model_year").val();  
		
		var mobilecodedata =  $('#mobilecodedata').val();
		 
		$.ajax({
                type: 'GET',
                url: "leads/insert_basic_info",
            	dataType:'JSON',
            	data:{'reg_id':reg_id,'mob':mob,'email':email,'fname':fname,'fname_ar':fname_ar,'branch_state':branch_state,'company_city':company_city,'centre':centre,'source':source,'message':message, 'whatsapp':whatsapp,'breg_place':breg_place,'phone_code':phone_code,'mobilecodedata':mobilecodedata,'formtype':formtype,'make':make,'model':model,'year':year,'color':color,'color_ar':color_ar,'sellername':sellername,'sellername_ar':sellername_ar,'sellermobile':sellermobile,'location':location,'yourmobile':yourmobile,'additionaldet':additionaldet,'lpackage':lpackage,'payment':payment, 'yearfrom':yearfrom,'yearto':yearto,'budget':budget,'knowmore':knowmore,'plate_no':plate_no , 'make_model_year':make_model_year},
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
	
		var centre       = $("#centre").val();
		var source       = $("#source").val();	
		var message      = $("#message").val();		
		var whatsapp     = $("#breg_whatsapp").val();		
		var breg_place   = $("#breg_place").val();
	 
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
		
		var make_model_year = $("#make_model_year").val();  
		
		$.ajax({
			type: 'GET',
			url : "leads/update_basic_info",
			dataType : 'JSON',
            data : {'lead_id':lead_id,'reg_id':reg_id,'mob':mob,'email':email,'fname':fname,'fname_ar':fname_ar,'branch_state':branch_state,'company_city':company_city,'centre':centre,'source':source,'message':message, 'whatsapp':whatsapp,'breg_place':breg_place,'phone_code':phone_code,'mobilecodedata':mobilecodedata, 'formtype':formtype,'make':make,'model':model,'year':year,'color':color,'color_ar':color_ar,'sellername':sellername,'sellername_ar':sellername_ar,'sellermobile':sellermobile,'location':location,'yourmobile':yourmobile,'additionaldet':additionaldet,'lpackage':lpackage,'payment':payment, 'yearfrom':yearfrom,'yearto':yearto,'budget':budget,'knowmore':knowmore, 'plate_no':plate_no, 'make_model_year':make_model_year},
			
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
						// Save the Inspection section too, then reload so its values persist.
						if (window.saveLeadInspection) { window.saveLeadInspection(function(){ window.location.reload(); }); }
						else { window.location.reload(); }
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

// Inspection Template + Staff dropdowns in the Assign row are kept as NATIVE
// selects (no select2). select2 auto-focuses its search box on open, which makes
// a long leads page jump/scroll. Native selects never scroll the page and behave
// the same on live and local. If either was already select2-initialised (global
// initializer), tear it down so the native control is used.
$(function () {
  ['#assign_insp_type', '#assign_lead_staff'].forEach(function (sel) {
    var $el = $(sel);
    if ($el.hasClass('select2-hidden-accessible')) { $el.select2('destroy'); }
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
	var insp_type = $("#assign_insp_type").val();
	var insp_sched = $("#assign_insp_scheduled").val();

	$("#error_assigns").html("");
	$("#error_assigns_lead").html("");
	$("#error_assigns_type").html("");
	if(staff.length==0)
	{
		$("#error_assigns").html("Please Select Staff");
		return false;
	}

	if(!insp_type)
	{
		$("#error_assigns_type").html("Please select an inspection template");
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
            	   data:{'_token':token,'leads':rows_selected,'user_id':staff,'inspection_type_id':insp_type,'scheduled_at':insp_sched},
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
$(document).ready(function() {

    //Run once on page load (EDIT MODE)
    var formType = $('#formtype').val();
    checkFormType(formType);     
    console.log(formType);

    //Run again when user changes dropdown
    $('#formtype').on('change', function() {
        checkFormType($(this).val());
    });

});

function checkFormType(form) {

    // Hide everything first
    $('#make_div, #model_div, #vehicle_plate_no_div, #year_div, #color_div, #color_ar_div, #sellername_div, #sellername_ar_div, #sellermobile_div, #location_div, #package_div, #payment_div, #make_model_year_div, #yourmobile_div, #additionaldet_div, #yearfrom_div, #yearto_div, #budget_div, #knowmore_div').hide();

    // If Book Inspection selected
    if (form === 'Book Inspection') {

        $('#make_div').show();
        $('#model_div').show();
        $('#vehicle_plate_no_div').show();
        $('#year_div').show();
        $('#color_div').show();
        $('#color_ar_div').show();
        $('#sellername_div').show();
        $('#sellername_ar_div').show();
        $('#sellermobile_div').show();
        $('#location_div').show();
        $('#package_div').show();
        $('#payment_div').show();
        $('#make_model_year_div').show();
        $('#yourmobile_div').show();
        $('#additionaldet_div').show();
    } 
    else if (form === 'Buy Assured') {

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
}

/*$(document).ready(function() 
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
}); */
</script>

<!--------- getModel --------->
<script type="text/javascript">
$(document).ready(function() {

    // Saved model id(s) for edit — lead_model may hold several comma-separated ids.
    let selectedModel = ($('#model').attr('data-selected-model') || '').toString();
    let selectedModels = selectedModel.split(',').map(function (s) { return s.trim(); }).filter(Boolean);

    // Function to load models via AJAX
    function loadModels(make) {
        if (!make) return;

        $.ajax({
            type: 'GET',
            url: 'leads/getModel',
            data: { make: make },
            success: function(res) {
                $("#model").empty();
                $("#model").append('<option value="">Select Model</option>');

                if (res) {
                    $.each(res, function(id, name) {
                        let selected = (selectedModels.indexOf(String(id)) !== -1) ? 'selected' : '';
                        $("#model").append('<option value="'+id+'" '+selected+'>'+name+'</option>');
                    });

                    $('#model').trigger('change'); // Refresh select2
                }
            }
        });
    }

    // Load models on page load if Make is already selected (EDIT mode)
    let make = $('#make').val();
    if(make) {
        loadModels(make);
    }

    // Load models on Make change (INSERT mode or user changes)
    $('#make').on('change', function() {
        selectedModels = []; // Clear previous selection if user changes Make
        loadModels($(this).val());
    });
});


/*$( document ).ready(function() 
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
});*/
</script>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Lead Notes -->
<script type="text/javascript">
    (function () {
        var addBtn = document.getElementById('note_add_btn');
        if (!addBtn) return;
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

        function noteHtml(n) {
            var author = n.author || 'User';
            var initial = author.trim().charAt(0).toUpperCase() || 'U';
            var when = n.created_at ? new Date(n.created_at.replace(' ', 'T')).toLocaleString([], { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '';
            return '<div class="note-item" data-note-id="' + n.note_id + '">' +
                '<div class="note-item__avatar">' + esc(initial) + '</div>' +
                '<div class="note-item__body">' +
                    '<div class="note-item__head">' +
                        '<span class="note-item__author">' + esc(author) + '</span>' +
                        '<span class="note-item__date">' + esc(when) + '</span>' +
                        '<button type="button" class="note-item__edit" title="Edit" onclick="editLeadNote(this)"><i class="bx bx-edit"></i></button>' +
                        '<button type="button" class="note-item__del" title="Delete" onclick="deleteLeadNote(' + n.note_id + ')"><i class="bx bx-trash"></i></button>' +
                    '</div>' +
                    '<div class="note-item__text">' + esc(n.note_text) + '</div>' +
                '</div></div>';
        }

        function updateCount(d) {
            var c = document.getElementById('note_count');
            if (c) c.textContent = document.querySelectorAll('#note_list .note-item').length;
        }

        addBtn.addEventListener('click', function () {
            var txt = (document.getElementById('note_text').value || '').trim();
            var leadId = document.getElementById('lead_id') ? document.getElementById('lead_id').value : '';
            var err = document.getElementById('note_error');
            if (!txt) { err.textContent = 'Please write a note.'; return; }
            if (!leadId) { err.textContent = 'Save the lead first.'; return; }

            addBtn.disabled = true; err.textContent = '';
            fetch("{{ url('leads/add-note') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ note_lead_id: leadId, note_text: txt })
            })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (data) {
                if (data.status != 1) throw new Error();
                var empty = document.getElementById('note_empty');
                if (empty) empty.remove();
                var list = document.getElementById('note_list');
                list.insertAdjacentHTML('afterbegin', noteHtml(data.note));
                document.getElementById('note_text').value = '';
                updateCount();
            })
            .catch(function () { err.textContent = 'Could not save the note. Try again.'; })
            .finally(function () { addBtn.disabled = false; });
        });
    })();

    // Inspection details (template / assigned person / scheduled date) save.
    // Exposed globally so the main Lead Info "Update" saves it too before the
    // page reloads — otherwise the values entered here are lost on reload.
    window.saveLeadInspection = function (onDone) {
        var leadEl = document.getElementById('insp_lead_id');
        if (!leadEl) { if (onDone) onDone(); return; }   // section not on page (add mode)

        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var msg  = document.getElementById('insp_msg');
        var btn  = document.getElementById('insp_update_btn');

        if (msg) { msg.style.color = '#667085'; msg.textContent = 'Saving…'; }
        if (btn) btn.disabled = true;

        fetch("{{ url('leads/update-lead-inspection') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({
                lead_id: leadEl.value,
                inspection_type_id: document.getElementById('insp_type').value,
                technician_id: document.getElementById('insp_staff').value,
                scheduled_at: document.getElementById('insp_scheduled').value
            })
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            if (data.status == 1) {
                if (msg) { msg.style.color = '#04B084'; msg.textContent = data.text || 'Updated.'; }
            } else {
                if (msg) { msg.style.color = '#e5484d'; msg.textContent = data.text || 'Could not update.'; }
            }
        })
        .catch(function () { if (msg) { msg.style.color = '#e5484d'; msg.textContent = 'Could not update. Try again.'; } })
        .finally(function () { if (btn) btn.disabled = false; if (onDone) onDone(); });
    };

    (function () {
        var btn = document.getElementById('insp_update_btn');
        if (btn) btn.addEventListener('click', function () { window.saveLeadInspection(); });
    })();

    function editLeadNote(btn) {
        var item = btn.closest('.note-item');
        if (!item || item.querySelector('.note-item__editbox')) return;   // already editing
        var id = item.getAttribute('data-note-id');
        var textEl = item.querySelector('.note-item__text');
        var current = textEl.textContent;

        var box = document.createElement('div');
        box.className = 'note-item__editbox';
        box.innerHTML =
            '<textarea class="form-control note-edit-ta" rows="2" maxlength="5000"></textarea>' +
            '<div class="note-edit-actions">' +
                '<button type="button" class="btn btn-sm note-btn note-edit-save">Save</button>' +
                '<button type="button" class="btn btn-sm btn-light note-edit-cancel">Cancel</button>' +
                '<span class="note-edit-err"></span>' +
            '</div>';
        textEl.style.display = 'none';
        textEl.insertAdjacentElement('afterend', box);
        var ta = box.querySelector('.note-edit-ta');
        ta.value = current; ta.focus();

        box.querySelector('.note-edit-cancel').addEventListener('click', function () {
            box.remove(); textEl.style.display = '';
        });
        box.querySelector('.note-edit-save').addEventListener('click', function () {
            var val = (ta.value || '').trim();
            var errEl = box.querySelector('.note-edit-err');
            if (!val) { errEl.textContent = 'Note cannot be empty.'; return; }
            var saveBtn = this; saveBtn.disabled = true; errEl.textContent = '';
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ url('leads/update-note') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ note_id: id, note_text: val })
            })
            .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function (data) {
                textEl.textContent = data.note_text;
                box.remove(); textEl.style.display = '';
            })
            .catch(function () { errEl.textContent = 'Could not update. Try again.'; saveBtn.disabled = false; });
        });
    }

    function _doDeleteNote(id) {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        return fetch("{{ url('leads/delete-note') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ note_id: id })
        })
        .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
        .then(function () {
            var el = document.querySelector('#note_list .note-item[data-note-id="' + id + '"]');
            if (el) el.remove();
            var c = document.getElementById('note_count');
            if (c) c.textContent = document.querySelectorAll('#note_list .note-item').length;
            if (document.querySelectorAll('#note_list .note-item').length === 0) {
                document.getElementById('note_list').innerHTML = '<div class="note-empty" id="note_empty">No notes yet. Add the first one above.</div>';
            }
        });
    }

    function deleteLeadNote(id) {
        if (typeof Swal === 'undefined') {
            if (confirm('Delete this note?')) _doDeleteNote(id).catch(function () { alert('Could not delete the note.'); });
            return;
        }
        Swal.fire({
            title: 'Delete this note?',
            text: 'This note will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e5484d',
            cancelButtonColor: '#8a94a6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;
            _doDeleteNote(id)
                .then(function () { Swal.fire({ icon: 'success', title: 'Deleted', text: 'The note has been removed.', timer: 1400, showConfirmButton: false }); })
                .catch(function () { Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not delete the note.' }); });
        });
    }
</script>

@endsection