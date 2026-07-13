@extends('layouts.myfudapp')
@section('content')

<style>
    /* .select2-container--default .select2-selection--single{display:none !important;} */
</style>

<style>
    .select2-search__field{
        width: 100%!important;
    }
	
    /*.note-btn[title]{*/
    /*    background:green !important;*/
    /*}*/
	
    #myTab li 
    {
        box-shadow: 0px 1px 3px 0px #dfd3cc !important;
        background: #084063 !important;
        color: #fff !important;
    }
    #myTab li a 
    {
        color: #fff !important;
    }
    #myTab li a.active
    {
        color: #e76c90 !important;
    }	
</style>

<style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0; 
        }
</style>

<style>
.form-control{
    box-shadow: none !important;
} 
/*.select2-container {*/
/*    width: 100% !important;*/
/*}*/
.mt-2
{
    margin-bottom: 10px !important;
}
.innerdash .authentication-form form > div
{
    height :auto;
}
</style>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 35px;
  height: 20px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.innerdash .authentication-form .group {
    margin-bottom: 25px;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 4px;
  top:2px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(14px);
  -ms-transform: translateX(14px);
  transform: translateX(14px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 20px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<!---- DAMAGES ---->
<style>
#canvas {
  border: 1px solid black;
  margin-top: 10px;
}

.color {
  height: 25px;
  width: 25px;
  margin-left: 0.5em;
  border-radius: 18px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
  border: 2px solid #aaa;
  cursor: pointer;
}
 </style>
<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>        


<div class="page-content">
	<div class="container-fluid">
            
		<!-- start page title -->
		<div class="row">
			<div class="col-12">
				<div class="page-title-box d-flex align-items-center justify-content-between">
					<h4 class="mb-0">Manage Inspection Report</h4>

                    <div class="page-title-right">
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
								<li class="breadcrumb-item active"> Inspection Report</li>
						</ol>
                   </div>
                </div>
			</div>
		</div> <!-- end page title -->                      

		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
	   
						<div class="row authentication-form mx-auto">
						
							<input type="hidden" name="report_id" id="report_id">
              
							<ul class="nav nav-tabs w-100" id="myTab" role="tablist">
								<li class="nav-item li-new" role="presentation">
									<a class="nav-link active" id="home-tab" href="#" role="tab" aria-controls="home" aria-selected="true">GENERAL INFO</a>
								</li>
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="vehicle-tab" href="#" role="tab" aria-controls="vehicle" aria-selected="false">VEHICLE INFO</a>
								</li>
								<!-- <li class="nav-item" role="presentation">
									<a class="nav-link" id="spec-tab"  href="#" role="tab" aria-controls="spec" aria-selected="false">ADDITIONAL SPECS</a>
								</li> -->
								<!-- <li class="nav-item" role="presentation">
									<a class="nav-link" id="warranty-tab"  href="#" role="tab" aria-controls="warranty"  >WARRANTY / SERVICES</a>
								</li> -->
								
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="specification-tab" href="#" role="tab" aria-controls="specification">VEHICLE SPECIFICATION</a>
								</li>
								
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="checklist-tab" href="#" role="tab" aria-controls="checklist"> INSPECTION CHECKLIST </a>
								</li>
 								
 								<li class="nav-item" role="presentation">
									<a class="nav-link" id="summary-tab" href="#" role="tab" aria-controls="summary"> SUMMARY </a>
								</li>
								
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="overview-tab" href="#" role="tab" aria-controls="overview"> OVERVIEW </a>
								</li>
								
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="reports-tab" href="#" role="tab" aria-controls="reports" aria-selected="false">REPORTS</a>
								</li>
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="gallery-tab" href="#" role="tab" aria-controls="gallery" aria-selected="false">GALLERY</a>
								</li>
								<li class="nav-item" role="presentation">
									<a class="nav-link" id="damages-tab" href="#" role="tab" aria-controls="damages" aria-selected="false">DAMAGES</a>
								</li>
							</ul>    
            
							<div class="tab-content w-100" id="myTabContent">
							<br>  	
								<!------------------ FORM 1 - GENERAL INFO ------------------>
								<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
									<form class="myform" method="post" id="homeForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id" name="edit_id"> <!-- Report id -->
										<input type="hidden" class="update_id" name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
									 
										<div class="row"> 					
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Reference')}}<span class="text-red"> </span></label>
                                                    <input id="report_reference_no" type="text" class="form-control @error('report_reference_no') is-invalid @enderror" name="report_reference_no" placeholder="Reference" data-parsley-required-message="Reference required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('report_reference_no')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
								
                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Client Name')}}<span class="text-red"> </span></label>
                                                    <input id="report_client_name" type="text" class="form-control @error('report_client_name') is-invalid @enderror" name="report_client_name" placeholder="Client Name" data-parsley-required-message="Client Name required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('report_client_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Client Name in Arabic')}}<span class="text-red"> </span></label>
                                                    <input id="report_client_name_ar" type="text" class="form-control @error('report_client_name_ar') is-invalid @enderror" name="report_client_name_ar" placeholder="Client Name in Arabic" data-parsley-required-message="Client Name in Arabic required">
                                                    <div class="help-block with-errors"></div>
                                                    @error('report_client_name_ar')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{__('Date of Inspection')}}<span class="text-red"> </span></label>
                                                    <input type="date" id="report_date_of_inspection" name="report_date_of_inspection" class="form-control @error('report_date_of_inspection') is-invalid @enderror"  placeholder="Date of Inspection" data-parsley-required-message="Date of Inspection required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('report_date_of_inspection')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="report_vehicle_plate_no">{{__('Plate Number')}}<span class="text-red"> </span></label>
                                                    <input type="text" id="report_vehicle_plate_no" name="report_vehicle_plate_no" class="form-control @error('report_vehicle_plate_no') is-invalid @enderror"  placeholder="Plate Number" data-parsley-required-message="Plate Number required">
                                                    <div class="help-block with-errors"></div>
                                                    @error('report_vehicle_plate_no')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                             
                                            <div class="col-md-3 " style="margin-top:25px"> <!-- saveButton -->
                                                <div class="mb-3">
 													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addGeneralInfo")}}', 'homeForm', 'add-modal', 'inspectionReportDataTable','',true,'vehicle')" class="btn btn-info btn-block" value="Save & Next"> 
                                                </div>
                                            </div> 
                                            
                                        </div>
									</form>
								</div>
				
							<!------------------ FORM 2 - VEHICLE ------------------>
							<!-- <div class="tab-pane fade" id="vehicle_old1" role="tabpanel" aria-labelledby="vehicle-tab_old1"> -->
							
							<!--  <div class="tab-pane fade show active" id="vehicle_old" role="tabpanel" aria-labelledby="vehicle-tab_old" style="display:none;" > 16-05-2025-->
							<div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
							<!-- <h5> Vehicle</h5> <br> -->
								{!! html()->form('POST')->attributes(['method'=>'post', 'id'=>'vehicleForm', 'class'=>'myform'])->open() !!}
									
									<input type='hidden' name='_token' value='{{csrf_token()}}'>
                                    <input type="hidden" class="edit_id"  name="edit_id" > 
									<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
									<input type="hidden" class="add_edit" name="add_edit" value="0">
									 
										<div class="row"> 					
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Title')}}<span class="text-red"> </span></label>
                                                    <input id="vehicle_info_title" type="text" class="form-control @error('vehicle_info_title') is-invalid @enderror" name="vehicle_info_title" placeholder="Title" data-parsley-required-message="Title required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_title')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
							
                                             <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Model Year')}}<span class="text-red"> </span></label>
													<select id="vehicle_info_model_year" name="vehicle_info_model_year" data-parsley-required-message="Model Year Required" required="" class='form-control form-select' required>
														<option value="">-- Select Model Year --</option>
														<?php 
														for($i=1995;$i<=date("Y");$i++)
														{ ?>
														  <option value="<?php echo $i;?>"><?php echo $i;?></option> <?php
														} ?>
													</select>
                                                   
                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_model_year')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Manufacturing Year')}}<span class="text-red"> </span></label>
													<select id="vehicle_info_manuf_year" name="vehicle_info_manuf_year" data-parsley-required-message="Model Year Required" required="" class='form-control form-select' required>
														<option value="">-- Select Manufacturing Year --</option>
														<?php 
														for($i=1995;$i<=date("Y");$i++)
														{ ?>
															<option value="<?php echo $i;?>"><?php echo $i;?></option> <?php
														} ?>
													</select>
                                                    
                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_manuf_year')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('VIN (Vehicle Identification No)')}}<span class="text-red"> </span></label> <!-- Chassis Number -->
                                                    <input id="vehicle_info_chassis_no" type="text" class="form-control @error('vehicle_info_chassis_no') is-invalid @enderror" name="vehicle_info_chassis_no" placeholder="VIN" data-parsley-required-message="Chassis Number required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_chassis_no')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Odometer')}}<span class="text-red"> </span></label>
                                                    <input id="vehicle_info_odometer" type="text" class="form-control @error('vehicle_info_odometer') is-invalid @enderror" name="vehicle_info_odometer" placeholder="Odometer" data-parsley-required-message="Odometer required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_odometer')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Condition')}}<span class="text-red"> </span></label>
                                                    <!--<input id="vehicle_info_condition_old" type="text" class="form-control @error('vehicle_info_condition') is-invalid @enderror" name="vehicle_info_condition" placeholder="Condition" data-parsley-required-message="Condition required" required>-->
                                                    <select name="vehicle_info_condition" id="vehicle_info_condition" class="form-control form-select" data-parsley-required-message="Condition Required" required>
                                                        <option value=""> -- Select Condition -- </option>
                                                        <option value="1"> Used </option>
                                                        <option value="2"> New </option>
                                                    </select>

                                                    <div class="help-block with-errors"></div>
                                                    @error('vehicle_info_condition')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
											
										<!----- VEHICLE ADDITIONAL SPEC START ----->
										<div class="row"> 
											<h5> Additional Specification </h5>
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_region">{{ __('Region')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_region" type="text" class="form-control @error('add_spec_region') is-invalid @enderror" name="add_spec_region" placeholder="Region" data-parsley-required-message="Region required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_region')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_exterior_color">{{ __('Exterior color')}}<span class="text-red"> </span></label>
													<select id="add_spec_exterior_color" name="add_spec_exterior_color" data-parsley-required-message="Exterior color Required" required="" class='form-control form-select' required>
														<option value="">-- Select Exterior color --</option>
														<?php 
														foreach($exte_color as $exte)
														{ ?>
															<option value="<?php echo $exte->exte_color_id;?>"><?php echo $exte->exte_color_name;?></option>  <?php
														} ?>
													</select>
                                                    
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_exterior_color')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Interior color')}}<span class="text-red"> </span></label>
													<select id="add_spec_interior_color" name="add_spec_interior_color" data-parsley-required-message="Interior color Required" required="" class='form-control form-select' required>
														<option value="">-- Select Interior color --</option>
														<?php 
														foreach($inte_color as $inte)
														{ ?>
															<option value="<?php echo $inte->inte_color_id;?>"><?php echo $inte->inte_color_name;?></option> <?php
														} ?>
													</select>
                                                    
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_interior_color')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Gearbox')}}<span class="text-red"> </span></label>
													<select id="add_spec_gearbox" name="add_spec_gearbox" data-parsley-required-message="Gearbox Required" required="" class='form-control form-select' required>
														<option value="">-- Select Gearbox --</option>
														<?php 
														foreach($gear_box as $gearbox)
														{ ?>
															<option value="<?php echo $gearbox->gearbox_type_id;?>"><?php echo $gearbox->gearbox_type_name;?></option><?php
														} ?>
													</select>
                                                    
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_gearbox')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Fuel type')}}<span class="text-red"> </span></label>
													<select id="add_spec_fuel_type" name="add_spec_fuel_type" data-parsley-required-message="Fuel type Required" required="required" class='form-control form-select' required>
														<option value="">-- Select Fuel type --</option>
														<?php 
														foreach($fuel_type as $fueltype)
														{ ?>
															<option value="<?php echo $fueltype->fuel_type_id;?>"><?php echo $fueltype->fuel_type_name;?></option><?php
														} ?>
													</select>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_fuel_type')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3" style="display:none;">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Steering side')}}<span class="text-red"> </span></label>
													<select id="add_spec_steering_side" name="add_spec_steering_side" data-parsley-required-message="Steering side Required" class='form-control form-select' >
														<option value="">-- Select Steering side --</option>
														<?php 
														foreach($steer_side as $steerside)
														{ ?>
															<option value="<?php echo $steerside->steering_side_id;?>"><?php echo $steerside->steering_side_name;?></option><?php
														} ?>
													</select>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_steering_side')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_cylinders">{{ __('Cylinders')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_cylinders" type="text" class="form-control @error('add_spec_cylinders') is-invalid @enderror" name="add_spec_cylinders" placeholder="Cylinders" data-parsley-required-message="Cylinders required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_cylinders')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_engine_size">{{ __('Engine size')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_engine_size" type="text" class="form-control @error('add_spec_engine_size') is-invalid @enderror" name="add_spec_engine_size" placeholder="Engine size" data-parsley-required-message="Engine size required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_engine_size')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_keys">{{ __('Keys')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_keys" type="text" class="form-control @error('add_spec_keys') is-invalid @enderror" name="add_spec_keys" placeholder="Keys" data-parsley-required-message="Keys required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_keys')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_doors">{{ __('Doors')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_doors" type="text" class="form-control @error('add_spec_doors') is-invalid @enderror" name="add_spec_doors" placeholder="Doors" data-parsley-required-message="Doors required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_doors')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="add_spec_seats">{{ __('Seats')}}<span class="text-red"> </span></label>
                                                    <input id="add_spec_seats" type="text" class="form-control @error('add_spec_seats') is-invalid @enderror" name="add_spec_seats" placeholder="Seats" data-parsley-required-message="Seats required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('add_spec_seats')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
								
                                            <!-- <div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
                                             <!--   <div class="mb-3">
													<a href="#"  onclick="backToTab('specForm','vehicle')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
													<a href="#" onclick="insertEntry('{{URL::to("inspectionreport/addAdditionalSpec")}}', 'specForm', 'add-modal', 'inspectionReportDataTable','',true,'warranty')" class="btn btn-info btn-block"> Save & Next</a>
                                                </div>
                                            </div>  -->
                                        </div>
										
										<!----- VEHICLE ----->
										<!----- WARRANTY START ----->
										<div class="row"> 
											<h5> Warranty / Services </h5>
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('With Service History')}}<span class="text-red"> </span></label>
                                                    <textarea name="war_service_history" id="war_service_history" class="form-control"> 
                                                    </textarea> 
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="war_service_last">{{__('Last Service')}}<span class="text-red"> </span></label>
                                                    <input type="date" id="war_service_last" name="war_service_last" class="form-control @error('war_service_last') is-invalid @enderror"  placeholder="Last Service" data-parsley-required-message="Last Service required" required>
                                                    <div class="help-block with-errors"></div>
                                                    @error('war_service_last')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3" style="display:none;">
                                                <div class="mb-3">
                                                    <label for="war_service_next">{{__('Next Service Due')}}<span class="text-red"> </span></label>
                                                    <input type="date" id="war_service_next" name="war_service_next" class="form-control @error('war_service_next') is-invalid @enderror"  placeholder="Next Service Due" data-parsley-required-message="Next Service Due required" >
                                                    <div class="help-block with-errors"></div>
                                                    @error('war_service_next')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											 
                                            <!-- <div class="col-md-3" style="margin-top:25px">  
                                                <div class="mb-3">
													<a href="#"  onclick="backToTab('warrantyForm','spec')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
 													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addWarrantyServices")}}', 'warrantyForm', 'add-modal', 'inspectionReportDataTable','',true,'summary')" class="btn btn-info btn-block" value="Save & Next"> 
                                                </div> -->
											<!----- WARRANTY END ----->
											
                                            <div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
                                                <div class="mb-3">
													<a href="#"  onclick="backToTab('vehicleForm','home')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
													
													<a href="#" onclick="insertEntry('{{URL::to("inspectionreport/addVehicleInfo")}}', 'vehicleForm', 'add-modal', 'inspectionReportDataTable','',true,'specification')"   class="btn btn-info btn-block"> Save & Next</a> <!-- warranty -->
                                                </div>
                                            </div> 
										</div> 
								{!! html()->form()->close() !!} 
							</div> 
							 
								<!------------------ FORM 5 - VEHICLE SPECIFICATION ------------------>
								<!--  <div class="tab-pane fade show active" id="specification_old" role="tabpanel" aria-labelledby="specification-tab_old" style="display:none;" > 16-05-2025-->
								<div class="tab-pane fade" id="specification" role="tabpanel" aria-labelledby="specification-tab">
								
									<form class="myform" method="post" id="specificationForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
										
										<!---------------- Performance--------------->
										<!-- <label for="next_service_due">{{__('Performance')}}<span class="text-red"> </span></label> <br> -->
										
										<p><b> English & Arabic comment(example : Good quality[ar]نوعية جيدة)   </b></p>
										<h5 style="color:#37458b">Performance</h5>  
										
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="air_suspension" style="color:#37458b">{{__('Air Suspension')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="air_suspension1" name="air_suspension" value="1" class="air_suspension"> <label for="pass">Pass</label>
													  
													<input type="radio" id="air_suspension2" name="air_suspension" value="2" class="air_suspension"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="air_suspension3" name="air_suspension" value="3" class="air_suspension"> <label for="na">N/A</label>
													
													<input type="text" id="air_suspension_cmnt" name="air_suspension_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="adaptive_air_suspension" style="color:#37458b">{{__('Adaptive Air Suspension')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="adaptive_air_suspension1" name="adaptive_air_suspension" value="1" class="adaptive_air_suspension"> <label for="pass">Pass</label>
													  
													<input type="radio" id="adaptive_air_suspension2" name="adaptive_air_suspension" value="2" class="adaptive_air_suspension"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="adaptive_air_suspension3" name="adaptive_air_suspension" value="3" class="adaptive_air_suspension"> <label for="na">N/A</label>
													
													<input type="text" id="adaptive_air_suspension_cmnt" name="adaptive_air_suspension_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="differential_lock" style="color:#37458b">{{__('Differential Lock')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="differential_lock1" name="differential_lock" value="1" class="differential_lock"> <label for="pass">Pass</label>
													  
													<input type="radio" id="differential_lock2" name="differential_lock" value="2" class="differential_lock"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="differential_lock3" name="differential_lock" value="3" class="differential_lock"> <label for="na">N/A</label>
													
													<input type="text" id="differential_lock_cmnt" name="differential_lock_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="paddle_shifters" style="color:#37458b">{{__('Paddle Shifters')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="paddle_shifters1" name="paddle_shifters" value="1" class="paddle_shifters"> <label for="pass">Pass</label>
													  
													<input type="radio" id="paddle_shifters2" name="paddle_shifters" value="2" class="paddle_shifters"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="paddle_shifters3" name="paddle_shifters" value="3" class="paddle_shifters"> <label for="na">N/A</label>
													
													<input type="text" id="paddle_shifters_cmnt" name="paddle_shifters_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="tiptronic" style="color:#37458b">{{__('Tiptronic')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="tiptronic1" name="tiptronic" value="1" class="tiptronic"> <label for="pass">Pass</label>
													  
													<input type="radio" id="tiptronic2" name="tiptronic" value="2" class="tiptronic"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="tiptronic3" name="tiptronic" value="3" class="tiptronic"> <label for="na">N/A</label>
													
													<input type="text" id="tiptronic_cmnt" name="tiptronic_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="hill_descent_assist" style="color:#37458b">{{__('Hill Descent Assist')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="hill_descent_assist1" name="hill_descent_assist" value="1" class="hill_descent_assist"> <label for="pass">Pass</label>
													  
													<input type="radio" id="hill_descent_assist2" name="hill_descent_assist" value="2" class="hill_descent_assist"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="hill_descent_assist3" name="hill_descent_assist" value="3" class="hill_descent_assist"> <label for="na">N/A</label>
													
													<input type="text" id="hill_descent_assist_cmnt" name="hill_descent_assist_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<!-- -->
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Hill Start Assist')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="hill_start_assist1" name="hill_start_assist" value="1" class="hill_start_assist"> <label for="pass">Pass</label>
													  
													<input type="radio" id="hill_start_assist2" name="hill_start_assist" value="2" class="hill_start_assist"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="hill_start_assist3" name="hill_start_assist" value="3" class="hill_start_assist"> <label for="na">N/A</label>
													
													<input type="text" id="hill_start_assist_cmnt" name="hill_start_assist_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="auto_hold" style="color:#37458b">{{__('Auto Hold')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="auto_hold1" name="auto_hold" value="1" class="auto_hold"> <label for="pass">Pass</label>
													  
													<input type="radio" id="auto_hold2" name="auto_hold" value="2" class="auto_hold"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="auto_hold3" name="auto_hold" value="3" class="auto_hold"> <label for="na">N/A</label>
													
													<input type="text" id="auto_hold_cmnt" name="auto_hold_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="comfort_seats" style="color:#37458b">{{__('Comfort Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="comfort_seats1" name="comfort_seats" value="1" class="comfort_seats"> <label for="pass">Pass</label>
													  
													<input type="radio" id="comfort_seats2" name="comfort_seats" value="2" class="comfort_seats"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="comfort_seats3" name="comfort_seats" value="3" class="comfort_seats"> <label for="na">N/A</label>
													
													<input type="text" id="comfort_seats_cmnt" name="comfort_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="sport_seats" style="color:#37458b">{{__('Sport Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sport_seats1" name="sport_seats" value="1" class="sport_seats"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sport_seats2" name="sport_seats" value="2" class="sport_seats"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sport_seats3" name="sport_seats" value="3" class="sport_seats"> <label for="na">N/A</label>
													
													<input type="text" id="sport_seats_cmnt" name="sport_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="sport_brakes" style="color:#37458b">{{__('Sport Brakes')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sport_brakes1" name="sport_brakes" value="1" class="sport_brakes"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sport_brakes2" name="sport_brakes" value="2" class="sport_brakes"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sport_brakes3" name="sport_brakes" value="3" class="sport_brakes"> <label for="na">N/A</label>
													
													<input type="text" id="sport_brakes_cmnt" name="sport_brakes_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="sport_suspension" style="color:#37458b">{{__('Sport Suspension')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sport_suspension1" name="sport_suspension" value="1" class="sport_suspension"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sport_suspension2" name="sport_suspension" value="2" class="sport_suspension"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sport_suspension3" name="sport_suspension" value="3" class="sport_suspension"> <label for="na">N/A</label>
													
													<input type="text" id="sport_suspension_cmnt" name="sport_suspension_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="sport_exhaust" style="color:#37458b">{{__('Sport Exhaust')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sport_exhaust1" name="sport_exhaust" value="1" class="sport_exhaust"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sport_exhaust2" name="sport_exhaust" value="2" class="sport_exhaust"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sport_exhaust3" name="sport_exhaust" value="3" class="sport_exhaust"> <label for="na">N/A</label>
													
													<input type="text" id="sport_exhaust_cmnt" name="sport_exhaust_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="lane_change" style="color:#37458b">{{__('Lane Change')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="lane_change1" name="lane_change" value="1" class="lane_change"> <label for="pass">Pass</label>
													  
													<input type="radio" id="lane_change2" name="lane_change" value="2" class="lane_change"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="lane_change3" name="lane_change" value="3" class="lane_change"> <label for="na">N/A</label>
													
													<input type="text" id="lane_change_cmnt" name="lane_change_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<!-- -->
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="launch_control" style="color:#37458b">{{__('Assist Launch Control')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="launch_control1" name="launch_control" value="1" class="launch_control"> <label for="pass">Pass</label>
													  
													<input type="radio" id="launch_control2" name="launch_control" value="2" class="launch_control"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="launch_control3" name="launch_control" value="3" class="launch_control"> <label for="na">N/A</label>
													
													<input type="text" id="launch_control_cmnt" name="launch_control_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
										</div> 
										
										<!-- ##################################################### -->
										<hr>
										
										<!-------------  Safety ------------>
										<!-- <label for="next_service_due">{{__('')}}<span class="text-red"> </span></label> -->
										<h5 style="color:#37458b">Safety</h5>  
										
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Child Safety Seats (ISOFIX)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="child_safety_seats1" name="child_safety_seats" value="1" class="child_safety_seats"> <label for="pass">Pass</label>
													  
													<input type="radio" id="child_safety_seats2" name="child_safety_seats" value="2" class="child_safety_seats"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="child_safety_seats3" name="child_safety_seats" value="3" class="child_safety_seats"> <label for="na">N/A</label>
													
													<input type="text" id="child_safety_seats_cmnt" name="child_safety_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="front_view_camera" style="color:#37458b">{{__('Front View Camera')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_view_camera1" name="front_view_camera" value="1" class="front_view_camera"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_view_camera2" name="front_view_camera" value="2" class="front_view_camera"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_view_camera3" name="front_view_camera" value="3" class="front_view_camera"> <label for="na">N/A</label>
													
													<input type="text" id="front_view_camera_cmnt" name="front_view_camera_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_view_camera" style="color:#37458b">{{__('Rear View Camera')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_view_camera1" name="rear_view_camera" value="1" class="rear_view_camera"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_view_camera2" name="rear_view_camera" value="2" class="rear_view_camera"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_view_camera3" name="rear_view_camera" value="3" class="rear_view_camera"> <label for="na">N/A</label>
													
													<input type="text" id="rear_view_camera_cmnt" name="rear_view_camera_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">  
													<label for="degree_camera" style="color:#37458b">{{__('360 Degree Camera')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="degree_camera1" name="degree_camera" value="1" class="degree_camera"> <label for="pass">Pass</label>
													  
													<input type="radio" id="degree_camera2" name="degree_camera" value="2" class="degree_camera"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="degree_camera3" name="degree_camera" value="3" class="degree_camera"> <label for="na">N/A</label>
													
													<input type="text" id="degree_camera_cmnt" name="degree_camera_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Front Parking Sensors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_parking_sensors1" name="front_parking_sensors" value="1" class="front_parking_sensors"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_parking_sensors2" name="front_parking_sensors" value="2" class="front_parking_sensors"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_parking_sensors3" name="front_parking_sensors" value="3" class="front_parking_sensors"> <label for="na">N/A</label>
													
													<input type="text" id="front_parking_sensors_cmnt" name="front_parking_sensors_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due">{{__('Rear Parking Sensors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_parking_sensors1" name="rear_parking_sensors" value="1" class="rear_parking_sensors"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_parking_sensors2" name="rear_parking_sensors" value="2" class="rear_parking_sensors"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_parking_sensors3" name="rear_parking_sensors" value="3" class="rear_parking_sensors"> <label for="na">N/A</label>
													
													<input type="text" id="rear_parking_sensors_cmnt" name="rear_parking_sensors_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="lane_departure" style="color:#37458b">{{__('Lane Departure')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="lane_departure1" name="lane_departure" value="1" class="lane_departure"> <label for="pass">Pass</label>
													  
													<input type="radio" id="lane_departure2" name="lane_departure" value="2" class="lane_departure"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="lane_departure3" name="lane_departure" value="3" class="lane_departure"> <label for="na">N/A</label>
													
													<input type="text" id="lane_departure_cmnt" name="lane_departure_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Anti-Lock Brakes (ABS)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="anti_lock_brakes1" name="anti_lock_brakes" value="1" class="anti_lock_brakes"> <label for="pass">Pass</label>
													  
													<input type="radio" id="anti_lock_brakes2" name="anti_lock_brakes" value="2" class="anti_lock_brakes"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="anti_lock_brakes3" name="anti_lock_brakes" value="3" class="anti_lock_brakes"> <label for="na">N/A</label>
													
													<input type="text" id="anti_lock_brakes_cmnt" name="anti_lock_brakes_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('EBD')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="ebd1" name="ebd" class="ebd" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="ebd2" name="ebd" class="ebd" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="ebd3" name="ebd" class="ebd" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="ebd_cmnt" name="ebd_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Alarm')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="alarm1" name="alarm" class="alarm" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="alarm2" name="alarm" class="alarm" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="alarm3" name="alarm" class="alarm" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="alarm_cmnt" name="alarm_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Front Airbags')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_airbags1" name="front_airbags" class="front_airbags" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_airbags2" name="front_airbags" class="front_airbags" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_airbags3" name="front_airbags" class="front_airbags" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_airbags_cmnt" name="front_airbags_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2"> 
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Side Airbags')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="side_airbags1" name="side_airbags" class="side_airbags" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="side_airbags2" name="side_airbags" class="side_airbags" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="side_airbags3" name="side_airbags" class="side_airbags" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="side_airbags_cmnt" name="side_airbags_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="traction_control_sys" style="color:#37458b">{{__('Traction Control System')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="traction_control_sys1" name="traction_control_sys" class="traction_control_sys" value="1"> 
													<label for="pass">Pass</label>
													
													<input type="radio" id="traction_control_sys2" name="traction_control_sys" class="traction_control_sys" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="traction_control_sys3" name="traction_control_sys" class="traction_control_sys" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="traction_control_sys_cmnt" name="traction_control_sys_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="park_assist" style="color:#37458b">{{__('Park Assist')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="park_assist1" name="park_assist" value="1" class="park_assist"> <label for="pass">Pass</label>
													  
													<input type="radio" id="park_assist2" name="park_assist" value="2" class="park_assist"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="park_assist3" name="park_assist" value="3" class="park_assist"> <label for="na">N/A</label>
													
													<input type="text" id="park_assist_cmnt" name="park_assist_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="blind_spot_monitor" style="color:#37458b">{{__('Blind Spot Monitor ')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="blind_spot_monitor1" name="blind_spot_monitor" value="1" class="blind_spot_monitor"> <label for="pass">Pass</label>
													  
													<input type="radio" id="blind_spot_monitor2" name="blind_spot_monitor" value="2" class="blind_spot_monitor"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="blind_spot_monitor3" name="blind_spot_monitor" value="3" class="blind_spot_monitor"> <label for="na">N/A</label>
													
													<input type="text" id="blind_spot_monitor_cmnt" name="blind_spot_monitor_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											  
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Tire Pressure Monitor')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="tire_pressure_monitor1" name="tire_pressure_monitor" class="tire_pressure_monitor" value="1"> 
													<label for="pass">Pass</label>
													
													<input type="radio" id="tire_pressure_monitor2" name="tire_pressure_monitor" class="tire_pressure_monitor" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="tire_pressure_monitor3" name="tire_pressure_monitor" class="tire_pressure_monitor" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="tire_pressure_monitor_cmnt" name="tire_pressure_monitor_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-3">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Anti Glare Rear View Mirror')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="anti_glare_rear_view1" name="anti_glare_rear_view" class="anti_glare_rear_view" value="1"> 
													<label for="pass">Pass</label>
													
													<input type="radio" id="anti_glare_rear_view2" name="anti_glare_rear_view" class="anti_glare_rear_view" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="anti_glare_rear_view3" name="anti_glare_rear_view" class="anti_glare_rear_view" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="anti_glare_rear_view_cmnt" name="alarm_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
										</div>
										<hr>
										<!----------- Interior - Entertainment ----------->
										<!-- <label for="next_service_due">{{__('Interior - Entertainment')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Interior - Entertainment</h5>  
										
										<div class="row"> 
										
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="digital_driver_display" style="color:#37458b">{{__('Digital Driver Display')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="digital_driver_display1" name="digital_driver_display" class="digital_driver_display" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="digital_driver_display2" name="digital_driver_display" class="digital_driver_display" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="digital_driver_display3" name="digital_driver_display" class="digital_driver_display" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="digital_driver_display_cmnt" name="digital_driver_display_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('CD Player')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="cd_player1" name="cd_player" class="cd_player"value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="cd_player2" name="cd_player" class="cd_player" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="cd_player3" name="cd_player" class="cd_player" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="cd_player_cmnt" name="cd_player_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('DVD Player')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="dvd_player1" name="dvd_player" class="dvd_player" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="dvd_player2" name="dvd_player" class="dvd_player" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="dvd_player3" name="dvd_player" class="dvd_player" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="dvd_player_cmnt" name="dvd_player_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('MP3 Player')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="mp_player1" name="mp_player" class="mp_player" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="mp_player2" name="mp_player" class="mp_player" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="mp_player3" name="mp_player" class="mp_player" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="mp_player_cmnt" name="mp_player_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="sd_card_player" style="color:#37458b">{{__('SD Card Player')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sd_card_player1" name="sd_card_player" class="sd_card_player" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sd_card_player2" name="sd_card_player" class="sd_card_player" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sd_card_player3" name="sd_card_player" class="sd_card_player" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="sd_card_player_cmnt" name="sd_card_player_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="bluetooth_interface" style="color:#37458b">{{__('Bluetooth Interface')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="bluetooth_interface1" name="bluetooth_interface" class="bluetooth_interface" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="bluetooth_interface2" name="bluetooth_interface" class="bluetooth_interface" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="bluetooth_interface3" name="bluetooth_interface" class="bluetooth_interface" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="bluetooth_interface_cmnt" name="bluetooth_interface_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="premium_sound_system" style="color:#37458b">{{__('Premium Sound System')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="premium_sound_system1" name="premium_sound_system" class="premium_sound_system" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="premium_sound_system2" name="premium_sound_system" class="premium_sound_system" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="premium_sound_system3" name="premium_sound_system" class="premium_sound_system" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="premium_sound_system_cmnt" name="premium_sound_system_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('AUX Audio System')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="aux_audio_system1" name="aux_audio_system" class="aux_audio_system" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="aux_audio_system2" name="aux_audio_system" class="aux_audio_system" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="aux_audio_system3" name="aux_audio_system" class="aux_audio_system" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="aux_audio_system_cmnt" name="aux_audio_system_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="usb" style="color:#37458b">{{__('USB')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="usb1" name="usb" class="usb" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="usb2" name="usb" class="usb" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="usb3" name="usb" class="usb" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="usb_cmnt" name="usb_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="usb_c" style="color:#37458b">{{__('USB-C')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="usb_c1" name="usb_c" class="usb_c" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="usb_c2" name="usb_c" class="usb_c" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="usb_c3" name="usb_c" class="usb_c" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="usb_c_cmnt" name="usb_c_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="touch_screen" style="color:#37458b">{{__('Touch Screen')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="touch_screen1" name="touch_screen" class="touch_screen" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="touch_screen2" name="touch_screen" class="touch_screen" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="touch_screen3" name="touch_screen" class="touch_screen" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="touch_screen_cmnt" name="touch_screen_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_seat_enter_sys" style="color:#37458b">{{__('Rear Seat Entertain. Sys')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_seat_enter_sys1" name="rear_seat_enter_sys" class="rear_seat_enter_sys" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_seat_enter_sys2" name="rear_seat_enter_sys" class="rear_seat_enter_sys" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_seat_enter_sys3" name="rear_seat_enter_sys" class="rear_seat_enter_sys" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_seat_enter_sys_cmnt" name="rear_seat_enter_sys_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="wireless" style="color:#37458b">{{__('Wireless')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="wireless1" name="wireless" class="wireless" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="wireless2" name="wireless" class="wireless" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="wireless3" name="wireless" class="wireless" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="wireless_cmnt" name="wireless_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="ambient_lighting" style="color:#37458b">{{__('Ambient Lighting')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="ambient_lighting1" name="ambient_lighting" class="ambient_lighting" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="ambient_lighting2" name="ambient_lighting" class="ambient_lighting" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="ambient_lighting3" name="ambient_lighting" class="ambient_lighting" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="ambient_lighting_cmnt" name="ambient_lighting_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="apple_carplay" style="color:#37458b">{{__('Apple CarPlay')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="apple_carplay1" name="apple_carplay" class="apple_carplay" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="apple_carplay2" name="apple_carplay" class="apple_carplay" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="apple_carplay3" name="apple_carplay" class="apple_carplay" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="apple_carplay_cmnt" name="apple_carplay_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="navigation" style="color:#37458b">{{__('Navigation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="navigation1" name="navigation" class="navigation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="navigation2" name="navigation" class="navigation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="navigation3" name="navigation" class="navigation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="navigation_cmnt" name="navigation_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="standard_ac" style="color:#37458b">{{__('Standard AC')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="standard_ac1" name="standard_ac" class="standard_ac" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="standard_ac2" name="standard_ac" class="standard_ac" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="standard_ac3" name="standard_ac" class="standard_ac" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="standard_ac_cmnt" name="standard_ac_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="dual_climcont_ac" style="color:#37458b">{{__('Dual-Zone Climate Ctrl AC')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="dual_climcont_ac1" name="dual_climcont_ac" class="dual_climcont_ac" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="dual_climcont_ac2" name="dual_climcont_ac" class="dual_climcont_ac" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="dual_climcont_ac3" name="dual_climcont_ac" class="dual_climcont_ac" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="dual_climcont_ac_cmnt" name="dual_climcont_ac_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="multi_climcont_ac" style="color:#37458b">{{__('Multi-Zone Climate Ctrl AC')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="multi_climcont_ac1" name="multi_climcont_ac" class="multi_climcont_ac" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="multi_climcont_ac2" name="multi_climcont_ac" class="multi_climcont_ac" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="multi_climcont_ac3" name="multi_climcont_ac" class="multi_climcont_ac" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="multi_climcont_ac_cmnt" name="multi_climcont_ac_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="keyless_entry" style="color:#37458b">{{__('Keyless Entry')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="keyless_entry1" name="keyless_entry" class="keyless_entry" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="keyless_entry2" name="keyless_entry" class="keyless_entry" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="keyless_entry3" name="keyless_entry" class="keyless_entry" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="keyless_entry_cmnt" name="keyless_entry_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="keyless_start" style="color:#37458b">{{__('Keyless Start')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="keyless_start1" name="keyless_start" class="keyless_start" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="keyless_start2" name="keyless_start" class="keyless_start" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="keyless_start3" name="keyless_start" class="keyless_start" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="keyless_start_cmnt" name="keyless_start_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_steering" style="color:#37458b">{{__('Power Steering')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_steering1" name="power_steering" class="power_steering" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_steering2" name="power_steering" class="power_steering" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_steering3" name="power_steering" class="power_steering" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_steering_cmnt" name="power_steering_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="heads_up_display" style="color:#37458b">{{__('Heads Up Display')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="heads_up_display1" name="heads_up_display" class="heads_up_display" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="heads_up_display2" name="heads_up_display" class="heads_up_display" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="heads_up_display3" name="heads_up_display" class="heads_up_display" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="heads_up_display_cmnt" name="heads_up_display_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="cruise_control" style="color:#37458b">{{__('Cruise Control')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="cruise_control1" name="cruise_control" class="cruise_control" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="cruise_control2" name="cruise_control" class="cruise_control" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="cruise_control3" name="cruise_control" class="cruise_control" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="cruise_control_cmnt" name="cruise_control_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="adaptive_cruise_control" style="color:#37458b">{{__('Adaptive Cruise Control')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="adaptive_cruise_control1" name="adaptive_cruise_control" class="adaptive_cruise_control" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="adaptive_cruise_control2" name="adaptive_cruise_control" class="adaptive_cruise_control" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="adaptive_cruise_control3" name="adaptive_cruise_control" class="adaptive_cruise_control" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="adaptive_cruise_control_cmnt" name="adaptive_cruise_control_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_cooling_front" style="color:#37458b">{{__('Seat Cooling Front')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_cooling_front1" name="seat_cooling_front" class="seat_cooling_front" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_cooling_front2" name="seat_cooling_front" class="seat_cooling_front" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_cooling_front3" name="seat_cooling_front" class="seat_cooling_front" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_cooling_front_cmnt" name="seat_cooling_front_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_cooling_rear" style="color:#37458b">{{__('Seat Cooling Rear')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_cooling_rear1" name="seat_cooling_rear" class="seat_cooling_rear" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_cooling_rear2" name="seat_cooling_rear" class="seat_cooling_rear" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_cooling_rear3" name="seat_cooling_rear" class="seat_cooling_rear" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_cooling_rear_cmnt" name="seat_cooling_rear_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_massage_front" style="color:#37458b">{{__('Seat Massage Front')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_massage_front1" name="seat_massage_front" class="seat_massage_front" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_massage_front2" name="seat_massage_front" class="seat_massage_front" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_massage_front3" name="seat_massage_front" class="seat_massage_front" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_massage_front_cmnt" name="seat_massage_front_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_massage_rear" style="color:#37458b">{{__('Seat Massage Rear')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_massage_rear1" name="seat_massage_rear" class="seat_massage_rear" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_massage_rear2" name="seat_massage_rear" class="seat_massage_rear" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_massage_rear3" name="seat_massage_rear" class="seat_massage_rear" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_massage_rear_cmnt" name="seat_massage_rear_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="driver_memory_seat" style="color:#37458b">{{__('Driver Memory Seat')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="driver_memory_seat1" name="driver_memory_seat" class="driver_memory_seat" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="driver_memory_seat2" name="driver_memory_seat" class="driver_memory_seat" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="driver_memory_seat3" name="driver_memory_seat" class="driver_memory_seat" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="driver_memory_seat_cmnt" name="driver_memory_seat_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="passenger_memory_seat" style="color:#37458b">{{__('Passenger Memory Seat')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="passenger_memory_seat1" name="passenger_memory_seat" class="passenger_memory_seat" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="passenger_memory_seat2" name="passenger_memory_seat" class="passenger_memory_seat" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="passenger_memory_seat3" name="passenger_memory_seat" class="passenger_memory_seat" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="passenger_memory_seat_cmnt" name="passenger_memory_seat_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_driver_seats" style="color:#37458b">{{__('Power Driver Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_driver_seats1" name="power_driver_seats" class="power_driver_seats" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_driver_seats2" name="power_driver_seats" class="power_driver_seats" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_driver_seats3" name="power_driver_seats" class="power_driver_seats" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_driver_seats_cmnt" name="power_driver_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_passenger_seats" style="color:#37458b">{{__('Power Passenger Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_passenger_seats1" name="power_passenger_seats" class="power_passenger_seats" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="power_passenger_seats2" name="power_passenger_seats" class="power_passenger_seats" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_passenger_seats3" name="power_passenger_seats" class="power_passenger_seats" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="power_passenger_seats_cmnt" name="power_passenger_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_rear_seats" style="color:#37458b">{{__('Power Rear Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_rear_seats1" name="power_rear_seats" class="power_rear_seats" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_rear_seats2" name="power_rear_seats" class="power_rear_seats" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_rear_seats3" name="power_rear_seats" class="power_rear_seats" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_rear_seats_cmnt" name="power_rear_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_front_windows" style="color:#37458b">{{__('Power Front Windows')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_front_windows1" name="power_front_windows" class="power_front_windows" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_front_windows2" name="power_front_windows" class="power_front_windows" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_front_windows3" name="power_front_windows" class="power_front_windows" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_front_windows_cmnt" name="power_front_windows_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_rear_windows" style="color:#37458b">{{__('Power Rear Windows')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_rear_windows1" name="power_rear_windows" class="power_rear_windows" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_rear_windows2" name="power_rear_windows" class="power_rear_windows" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_rear_windows3" name="power_rear_windows" class="power_rear_windows" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_rear_windows_cmnt" name="power_rear_windows_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_trunk" style="color:#37458b">{{__('Power Trunk')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_trunk1" name="power_trunk" class="power_trunk" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_trunk2" name="power_trunk" class="power_trunk" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_trunk3" name="power_trunk" class="power_trunk" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_trunk_cmnt" name="power_trunk_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_locks" style="color:#37458b">{{__('Power Locks')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_locks1" name="power_locks" class="power_locks" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_locks2" name="power_locks" class="power_locks" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_locks3" name="power_locks" class="power_locks" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_locks_cmnt" name="power_locks_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_mirrors" style="color:#37458b">{{__('Power Mirrors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_mirrors1" name="power_mirrors" class="power_mirrors" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_mirrors2" name="power_mirrors" class="power_mirrors" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_mirrors3" name="power_mirrors" class="power_mirrors" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_mirrors_cmnt" name="power_mirrors_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_folding_mirrors" style="color:#37458b">{{__('Power Folding Mirrors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_folding_mirrors1" name="power_folding_mirrors" class="power_folding_mirrors" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="power_folding_mirrors2" name="power_folding_mirrors" class="power_folding_mirrors" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_folding_mirrors3" name="power_folding_mirrors" class="power_folding_mirrors" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="power_folding_mirrors_cmnt" name="power_folding_mirrors_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="sun_roof" style="color:#37458b">{{__('Sun Roof')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sun_roof1" name="sun_roof" class="sun_roof" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sun_roof2" name="sun_roof" class="sun_roof" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sun_roof3" name="sun_roof" class="sun_roof" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="sun_roof_cmnt" name="sun_roof_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="panoramic_roof" style="color:#37458b">{{__('Panoramic Roof')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="panoramic_roof1" name="panoramic_roof" class="panoramic_roof" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="panoramic_roof2" name="panoramic_roof" class="panoramic_roof" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="panoramic_roof3" name="panoramic_roof" class="panoramic_roof" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="panoramic_roof_cmnt" name="panoramic_roof_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="cool_box" style="color:#37458b">{{__('Cool Box')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="cool_box1" name="cool_box" class="cool_box" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="cool_box2" name="cool_box" class="cool_box" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="cool_box3" name="cool_box" class="cool_box" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="cool_box_cmnt" name="cool_box_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_heated_front" style="color:#37458b">{{__('Seat Heated Front')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_heated_front1" name="seat_heated_front" class="seat_heated_front" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_heated_front2" name="seat_heated_front" class="seat_heated_front" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_heated_front3" name="seat_heated_front" class="seat_heated_front" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_heated_front_cmnt" name="seat_heated_front_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="auto_park" style="color:#37458b">{{__('Auto Park')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="auto_park1" name="auto_park" class="auto_park" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="auto_park2" name="auto_park" class="auto_park" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="auto_park3" name="auto_park" class="auto_park" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="auto_park_cmnt" name="auto_park_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="remote_start_engine" style="color:#37458b">{{__('Remote Start Engine')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="remote_start_engine1" name="remote_start_engine" class="remote_start_engine" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="remote_start_engine2" name="remote_start_engine" class="remote_start_engine" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="remote_start_engine3" name="remote_start_engine" class="remote_start_engine" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="remote_start_engine_cmnt" name="remote_start_engine_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="soft_close_doors" style="color:#37458b">{{__('Soft Close Doors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="soft_close_doors1" name="soft_close_doors" class="soft_close_doors" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="soft_close_doors2" name="soft_close_doors" class="soft_close_doors" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="soft_close_doors3" name="soft_close_doors" class="soft_close_doors" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="soft_close_doors_cmnt" name="soft_close_doors_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="adaptive_lights" style="color:#37458b">{{__('Adaptive Lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="adaptive_lights1" name="adaptive_lights" class="adaptive_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="adaptive_lights2" name="adaptive_lights" class="adaptive_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="adaptive_lights3" name="adaptive_lights" class="adaptive_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="adaptive_lights_cmnt" name="adaptive_lights_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="night_vision" style="color:#37458b">{{__('Night Vision')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="night_vision1" name="night_vision" class="night_vision" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="night_vision2" name="night_vision" class="night_vision" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="night_vision3" name="night_vision" class="night_vision" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="night_vision_cmnt" name="night_vision_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="captain_rear_seats" style="color:#37458b">{{__('Captain Rear Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="captain_rear_seats1" name="captain_rear_seats" class="captain_rear_seats" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="captain_rear_seats2" name="captain_rear_seats" class="captain_rear_seats" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="captain_rear_seats3" name="captain_rear_seats" class="captain_rear_seats" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="captain_rear_seats_cmnt" name="captain_rear_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="leather_seats" style="color:#37458b">{{__('Leather Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="leather_seats1" name="leather_seats" class="leather_seats" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="leather_seats2" name="leather_seats" class="leather_seats" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="leather_seats3" name="leather_seats" class="leather_seats" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="leather_seats_cmnt" name="leather_seats_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="leather_fabric" style="color:#37458b">{{__('Fabric Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="leather_fabric1" name="leather_fabric" class="leather_fabric" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="leather_fabric2" name="leather_fabric" class="leather_fabric" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="leather_fabric3" name="leather_fabric" class="leather_fabric" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="leather_fabric_cmnt" name="leather_fabric_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="body_kit" style="color:#37458b">{{__('Body Kit')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="body_kit1" name="body_kit" class="body_kit" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="body_kit2" name="body_kit" class="body_kit" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="body_kit3" name="body_kit" class="body_kit" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="body_kit_cmnt" name="body_kit_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="lift_kit" style="color:#37458b">{{__('Lift Kit')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="lift_kit1" name="lift_kit" class="lift_kit" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="lift_kit2" name="lift_kit" class="lift_kit" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="lift_kit3" name="lift_kit" class="lift_kit" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="lift_kit_cmnt" name="lift_kit_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_spoiler" style="color:#37458b">{{__('Front Spoiler')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_spoiler1" name="front_spoiler" class="front_spoiler" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_spoiler2" name="front_spoiler" class="front_spoiler" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_spoiler3" name="front_spoiler" class="front_spoiler" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_spoiler_cmnt" name="front_spoiler_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_spoiler" style="color:#37458b">{{__('Rear Spoiler')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_spoiler1" name="rear_spoiler" class="rear_spoiler" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_spoiler2" name="rear_spoiler" class="rear_spoiler" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_spoiler3" name="rear_spoiler" class="rear_spoiler" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_spoiler_cmnt" name="rear_spoiler_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="fog_light_front" style="color:#37458b">{{__('Fog Light Front')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fog_light_front1" name="fog_light_front" class="fog_light_front" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fog_light_front2" name="fog_light_front" class="fog_light_front" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fog_light_front3" name="fog_light_front" class="fog_light_front" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fog_light_front_cmnt" name="fog_light_front_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="roof_carrier" style="color:#37458b">{{__('Roof Carrier Front')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="roof_carrier1" name="roof_carrier" class="roof_carrier" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="roof_carrier2" name="roof_carrier" class="roof_carrier" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="roof_carrier3" name="roof_carrier" class="roof_carrier" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="roof_carrier_cmnt" name="roof_carrier_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="halogen_headlight" style="color:#37458b">{{__('Halogen Headlight')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="halogen_headlight1" name="halogen_headlight" class="halogen_headlight" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="halogen_headlight2" name="halogen_headlight" class="halogen_headlight" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="halogen_headlight3" name="halogen_headlight" class="halogen_headlight" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="halogen_headlight_cmnt" name="halogen_headlight_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="led_headlight" style="color:#37458b">{{__('LED Headlight')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="led_headlight1" name="led_headlight" class="led_headlight" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="led_headlight2" name="led_headlight" class="led_headlight" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="led_headlight3" name="led_headlight" class="led_headlight" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="led_headlight_cmnt" name="led_headlight_cmnt" class="form-control" placeholder="Comments" style="display:none"> 
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="xenon_headlight" style="color:#37458b">{{__('Xenon Headlight')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="xenon_headlight1" name="xenon_headlight" class="xenon_headlight" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="xenon_headlight2" name="xenon_headlight" class="xenon_headlight" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="xenon_headlight3" name="xenon_headlight" class="xenon_headlight" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="xenon_headlight_cmnt" name="xenon_headlight_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="trailer_hook_coupling" style="color:#37458b">{{__('Trailer Hook Coupling')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="trailer_hook_coupling1" name="trailer_hook_coupling" class="trailer_hook_coupling" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="trailer_hook_coupling2" name="trailer_hook_coupling" class="trailer_hook_coupling" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="trailer_hook_coupling3" name="trailer_hook_coupling" class="trailer_hook_coupling" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="trailer_hook_coupling_cmnt" name="trailer_hook_coupling_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
										</div>
										<hr>
										
										<!----------- Interior - Entertainment ----------->
										<!-- <label for="next_service_due">{{__('Interior - Entertainment')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b"> Aftermarket Added Accessories </h5>  
										
										<div class="row"> 
										
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="next_service_due" style="color:#37458b">{{__('Winch')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="winch1" name="winch" class="winch" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="winch2" name="winch" class="winch" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="winch3" name="winch" class="winch" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="winch_cmnt" name="winch_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="body_kit_aaa" style="color:#37458b">{{__('Body Kit')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="body_kit_aaa1" name="body_kit_aaa" class="body_kit_aaa" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="body_kit_aaa2" name="body_kit_aaa" class="body_kit_aaa" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="body_kit_aaa3" name="body_kit_aaa" class="body_kit_aaa" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="body_kit_aaa_cmnt" name="body_kit_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="lift_kit_aaa" style="color:#37458b">{{__('Lift Kit')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="lift_kit_aaa1" name="lift_kit_aaa" class="lift_kit_aaa" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="lift_kit_aaa2" name="lift_kit_aaa" class="lift_kit_aaa" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="lift_kit_aaa3" name="lift_kit_aaa" class="lift_kit_aaa" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="lift_kit_aaa_cmnt" name="lift_kit_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="leather_seats_aaa" style="color:#37458b">{{__('Leather Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="leather_seats_aaa1" name="leather_seats_aaa" class="leather_seats_aaa" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="leather_seats_aaa2" name="leather_seats_aaa" class="leather_seats_aaa" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="leather_seats_aaa3" name="leather_seats_aaa" class="leather_seats_aaa" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="leather_seats_aaa_cmnt" name="leather_seats_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_seat_enter_sys_aaa" style="color:#37458b">{{__('Rear Seat Entertain. Sys')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_seat_enter_sys_aaa1" name="rear_seat_enter_sys_aaa" class="rear_seat_enter_sys_aaa" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_seat_enter_sys_aaa2" name="rear_seat_enter_sys_aaa" class="rear_seat_enter_sys_aaa" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_seat_enter_sys_aaa3" name="rear_seat_enter_sys_aaa" class="rear_seat_enter_sys_aaa" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_seat_enter_sys_aaa_cmnt" name="rear_seat_enter_sys_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="parking_sensors" style="color:#37458b">{{__('Parking Sensors')}}<span class="text-red"> </span></label> <br>
													<input type="radio" id="parking_sensors1" name="parking_sensors" class="parking_sensors" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="parking_sensors2" name="parking_sensors" class="parking_sensors" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="parking_sensors3" name="parking_sensors" class="parking_sensors" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="parking_sensors_cmnt" name="parking_sensors_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_view_camera_aaa_aaa" style="color:#37458b">{{__('Rear View Camera')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_view_camera_aaa1" name="rear_view_camera_aaa" class="rear_view_camera_aaa" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="rear_view_camera_aaa2" name="rear_view_camera_aaa" class="rear_view_camera_aaa" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_view_camera_aaa3" name="rear_view_camera_aaa" class="rear_view_camera_aaa" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="rear_view_camera_aaa_cmnt" name="rear_view_camera_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="navigation_aaa" style="color:#37458b">{{__('Navigation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="navigation_aaa1" name="navigation_aaa" class="navigation_aaa" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="navigation_aaa2" name="navigation_aaa" class="navigation_aaa" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="navigation_aaa3" name="navigation_aaa" class="navigation_aaa" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="navigation_aaa_cmnt" name="navigation_aaa_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="fire_extinguisher" style="color:#37458b">{{__('Fire extinguisher')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fire_extinguisher1" name="fire_extinguisher" class="fire_extinguisher" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fire_extinguisher2" name="fire_extinguisher" class="fire_extinguisher" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fire_extinguisher3" name="fire_extinguisher" class="fire_extinguisher" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fire_extinguisher_cmnt" name="fire_extinguisher_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
												<div class="mb-3">
													<a href="#"  onclick="backToTab('specificationForm','vehicle')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
													
													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addVehicleSpecification")}}', 'specificationForm', 'add-modal', 'inspectionReportDataTable','',true,'checklist')" class="btn btn-info btn-block" value="Save & Next"> 
												</div>
											</div> 
										</div>
									</form>
								</div>
								
								<!------------------ FORM  - CHECKLIST ------------------>
 								<!-- <div class="tab-pane fade show active" id="checklist_old" role="tabpanel" aria-labelledby="checklist-tab_old" style="display:none;" > 16-05-2025--> 
								<div class="tab-pane fade" id="checklist" role="tabpanel" aria-labelledby="checklist-tab"> 
								
									<form class="myform" method="post" id="checklistForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
										
										<!---------------- Exterior--------------->
										<!-- <label for="next_service_due">{{__('Exterior')}}<span class="text-red"> </span></label> <br> -->
										<p><b> English & Arabic comment(example : Good quality[ar]نوعية جيدة)   </b></p>
										<h5 style="color:#37458b">Exterior</h5>  
										
										<div class="row">

											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="fuel_filler_cover_petrol" style="color:#37458b">{{__('Fuel filler cover/Petrol Cap')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fuel_filler_cover_petrol1" name="fuel_filler_cover_petrol" class="fuel_filler_cover_petrol" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="fuel_filler_cover_petrol2" name="fuel_filler_cover_petrol" class="fuel_filler_cover_petrol" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fuel_filler_cover_petrol3" name="fuel_filler_cover_petrol" class="fuel_filler_cover_petrol" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="fuel_filler_cover_petrol_cmnt" name="fuel_filler_cover_petrol_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="door_locks_operation" style="color:#37458b">{{__('Door locks / operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="door_locks_operation1" name="door_locks_operation" class="door_locks_operation" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="door_locks_operation2" name="door_locks_operation" class="door_locks_operation" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="door_locks_operation3" name="door_locks_operation" class="door_locks_operation" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="door_locks_operation_cmnt" name="door_locks_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="glass" style="color:#37458b">{{__('Glass')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="glass1" name="glass" class="glass" value="1">
													<label for="pass">Pass</label>
													  
													<input type="radio" id="glass2" name="glass" class="glass" value="2">
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="glass3" name="glass" class="glass" value="3">
													<label for="na">N/A</label>
													
													<input type="text" id="glass_cmnt" name="glass_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="molding" style="color:#37458b">{{__('Molding')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="molding1" name="molding" class="molding" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="molding2" name="molding" class="molding" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="molding3" name="molding" class="molding" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="molding_cmnt" name="molding_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="bumper_grills" style="color:#37458b">{{__('Bumper Grills')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="bumper_grills1" name="bumper_grills" class="bumper_grills" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="bumper_grills2" name="bumper_grills" class="bumper_grills" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="bumper_grills3" name="bumper_grills" class="bumper_grills" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="bumper_grills_cmnt" name="bumper_grills_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_bumper" style="color:#37458b">{{__('Front bumper')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_bumper1" name="front_bumper" class="front_bumper" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_bumper2" name="front_bumper" class="front_bumper" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_bumper3" name="front_bumper" class="front_bumper" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_bumper_cmnt" name="front_bumper_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_bumper" style="color:#37458b">{{__('Rear bumper')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_bumper1" name="rear_bumper" class="rear_bumper" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_bumper2" name="rear_bumper" class="rear_bumper" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_bumper3" name="rear_bumper" class="rear_bumper" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_bumper_cmnt" name="rear_bumper_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_left_headlights" style="color:#37458b">{{__('Front left headlights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_left_headlights1" name="front_left_headlights" class="front_left_headlights" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="front_left_headlights2" name="front_left_headlights" class="front_left_headlights" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_left_headlights3" name="front_left_headlights" class="front_left_headlights" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="front_left_headlights_cmnt" name="front_left_headlights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_right_headlights" style="color:#37458b">{{__('Front right headlights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_right_headlights1" name="front_right_headlights" class="front_right_headlights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_right_headlights2" name="front_right_headlights" class="front_right_headlights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_right_headlights3" name="front_right_headlights" class="front_right_headlights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_right_headlights_cmnt" name="front_right_headlights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_left_tail_lights" style="color:#37458b">{{__('Rear left tail lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_left_tail_lights1" name="rear_left_tail_lights" class="rear_left_tail_lights" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="rear_left_tail_lights2" name="rear_left_tail_lights" class="rear_left_tail_lights" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_left_tail_lights3" name="rear_left_tail_lights" class="rear_left_tail_lights" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="rear_left_tail_lights_cmnt" name="rear_left_tail_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_right_tail_lights" style="color:#37458b">{{__('Rear right tail lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_right_tail_lights1" name="rear_right_tail_lights" class="rear_right_tail_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_right_tail_lights2" name="rear_right_tail_lights" class="rear_right_tail_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_right_tail_lights3" name="rear_right_tail_lights" class="rear_right_tail_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_right_tail_lights_cmnt" name="rear_right_tail_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="general_body_condition" style="color:#37458b">{{__('General body condition')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="general_body_condition1" name="general_body_condition" class="general_body_condition" value="1"> <label for="pass">Pass</label>
													
													<input type="radio" id="general_body_condition2" name="general_body_condition" class="general_body_condition" value="2"> <label for="fail">Fail</label>
													&nbsp;&nbsp;
													<input type="radio" id="general_body_condition3" name="general_body_condition" class="general_body_condition" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="general_body_condition_cmnt" name="general_body_condition_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
										</div>
										
										<!---------------- Interior--------------->
										<!-- <label for="next_service_due">{{__('Interior')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Interior</h5>  
										
										<div class="row"> 
										
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seat_belts" style="color:#37458b">{{__('Seat belts')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seat_belts1" name="seat_belts" class="seat_belts" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seat_belts2" name="seat_belts" class="seat_belts" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seat_belts3" name="seat_belts" class="seat_belts" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seat_belts_cmnt" name="seat_belts_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="headliner" style="color:#37458b">{{__('Headliner')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="headliner1" name="headliner" class="headliner" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="headliner2" name="headliner" class="headliner" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="headliner3" name="headliner" class="headliner" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="headliner_cmnt" name="headliner_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rearview_mirror" style="color:#37458b">{{__('Rearview mirror')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rearview_mirror1" name="rearview_mirror" class="rearview_mirror" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rearview_mirror2" name="rearview_mirror" class="rearview_mirror" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rearview_mirror3" name="rearview_mirror" class="rearview_mirror" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rearview_mirror_cmnt" name="rearview_mirror_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="steering_wheel" style="color:#37458b">{{__('Steering wheel')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="steering_wheel1" name="steering_wheel" class="steering_wheel" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="steering_wheel2" name="steering_wheel" class="steering_wheel" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="steering_wheel3" name="steering_wheel" class="steering_wheel" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="steering_wheel_cmnt" name="steering_wheel_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="gear_lever" style="color:#37458b">{{__('Gear lever')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="gear_lever1" name="gear_lever" class="gear_lever" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="gear_lever2" name="gear_lever" class="gear_lever" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="gear_lever3" name="gear_lever" class="gear_lever" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="gear_lever_cmnt" name="gear_lever_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="sun_visor" style="color:#37458b">{{__('Sun visor')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sun_visor1" name="sun_visor" class="sun_visor" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sun_visor2" name="sun_visor" class="sun_visor" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sun_visor3" name="sun_visor" class="sun_visor" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="sun_visor_cmnt" name="sun_visor_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="pillar_trim" style="color:#37458b">{{__('Pillar trim')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="pillar_trim1" name="pillar_trim" class="pillar_trim" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="pillar_trim2" name="pillar_trim" class="pillar_trim" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="pillar_trim3" name="pillar_trim" class="pillar_trim" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="pillar_trim_cmnt" name="pillar_trim_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="armrest_console" style="color:#37458b">{{__('Armrest console')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="armrest_console1" name="armrest_console" class="armrest_console" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="armrest_console2" name="armrest_console" class="armrest_console" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="armrest_console3" name="armrest_console" class="armrest_console" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="armrest_console_cmnt" name="armrest_console_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="floor_mats_carpets" style="color:#37458b">{{__('Floor mats and carpets')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="floor_mats_carpets1" name="floor_mats_carpets" class="floor_mats_carpets" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="floor_mats_carpets2" name="floor_mats_carpets" class="floor_mats_carpets" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="floor_mats_carpets3" name="floor_mats_carpets" class="floor_mats_carpets" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="floor_mats_carpets_cmnt" name="floor_mats_carpets_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="trunk_liner" style="color:#37458b">{{__('Trunk liner')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="trunk_liner1" name="trunk_liner" class="trunk_liner" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="trunk_liner2" name="trunk_liner" class="trunk_liner" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="trunk_liner3" name="trunk_liner" class="trunk_liner" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="trunk_liner_cmnt" name="trunk_liner_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="dashboard" style="color:#37458b">{{__('Dashboard')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="dashboard1" name="dashboard" class="dashboard" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="dashboard2" name="dashboard" class="dashboard" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="dashboard3" name="dashboard" class="dashboard" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="dashboard_cmnt" name="dashboard_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="glove_compartment" style="color:#37458b">{{__('Glove compartment')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="glove_compartment1" name="glove_compartment" class="glove_compartment" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="glove_compartment2" name="glove_compartment" class="glove_compartment" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="glove_compartment3" name="glove_compartment" class="glove_compartment" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="glove_compartment_cmnt" name="glove_compartment_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="seats" style="color:#37458b">{{__('Seats')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="seats1" name="seats" class="seats" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="seats2" name="seats" class="seats" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="seats3" name="seats" class="seats" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="seats_cmnt" name="seats_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="door_trims" style="color:#37458b">{{__('Door trims')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="door_trims1" name="door_trims" class="door_trims" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="door_trims2" name="door_trims" class="door_trims" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="door_trims3" name="door_trims" class="door_trims" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="door_trims_cmnt" name="door_trims_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="ac_grills" style="color:#37458b">{{__('A/C grills')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="ac_grills1" name="ac_grills" class="ac_grills" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="ac_grills2" name="ac_grills" class="ac_grills" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="ac_grills3" name="ac_grills" class="ac_grills" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="ac_grills_cmnt" name="ac_grills_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-3">
                                                <div class="mb-3"> 
													<label for="sunroof_shade_liner" style="color:#37458b">{{__('Sunroof shade / Sunroof liner')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sunroof_shade_liner1" name="sunroof_shade_liner" class="sunroof_shade_liner" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sunroof_shade_liner2" name="sunroof_shade_liner" class="sunroof_shade_liner" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sunroof_shade_liner3" name="sunroof_shade_liner" class="sunroof_shade_liner" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="sunroof_shade_liner_cmnt" name="sunroof_shade_liner_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											 
										</div>
										
										<!---------------- Tyre --------------->
										<!-- <label for="next_service_due">{{__('Tyre')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Tyre</h5>  
										
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="spare_tyre" style="color:#37458b">{{__('Spare Tyre')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="spare_tyre1" name="spare_tyre" class="spare_tyre" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="spare_tyre2" name="spare_tyre" class="spare_tyre" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="spare_tyre3" name="spare_tyre" class="spare_tyre" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="spare_tyre_cmnt" name="spare_tyre_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_left_tyre" style="color:#37458b">{{__('Front Left Tyre')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_left_tyre1" name="front_left_tyre" class="front_left_tyre" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_left_tyre2" name="front_left_tyre" class="front_left_tyre" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_left_tyre3" name="front_left_tyre" class="front_left_tyre" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_left_tyre_cmnt" name="front_left_tyre_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="back_right_tyre" style="color:#37458b">{{__('Back Right Tyre')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="back_right_tyre1" name="back_right_tyre" class="back_right_tyre" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="back_right_tyre2" name="back_right_tyre" class="back_right_tyre" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="back_right_tyre3" name="back_right_tyre" class="back_right_tyre" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="back_right_tyre_cmnt" name="back_right_tyre_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="front_right_tyre" style="color:#37458b">{{__('Front Right Tyre')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="front_right_tyre1" name="front_right_tyre" class="front_right_tyre" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="front_right_tyre2" name="front_right_tyre" class="front_right_tyre" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="front_right_tyre3" name="front_right_tyre" class="front_right_tyre" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="front_right_tyre_cmnt" name="front_right_tyre_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2" style="color:#37458b">
                                                <div class="mb-3"> 
													<label for="back_left_tyre">{{__('Back Left Tyre')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="back_left_tyre1" name="back_left_tyre" class="back_left_tyre" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="back_left_tyre2" name="back_left_tyre" class="back_left_tyre" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="back_left_tyre3" name="back_left_tyre" class="back_left_tyre" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="back_left_tyre_cmnt" name="back_left_tyre_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
										</div>
										
										<hr>
										<!---------------- Engine--------------->
										<!-- <label for="next_service_due">{{__('Engine')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Engine</h5>  
										
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="coolant_level" style="color:#37458b">{{__('Coolant level')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="coolant_level1" name="coolant_level" class="coolant_level" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="coolant_level2" name="coolant_level" class="coolant_level" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="coolant_level3" name="coolant_level" class="coolant_level" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="coolant_level_cmnt" name="coolant_level_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="coolant_leaks" style="color:#37458b">{{__('Coolant leaks')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="coolant_leaks1" name="coolant_leaks" class="coolant_leaks" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="coolant_leaks2" name="coolant_leaks" class="coolant_leaks" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="coolant_leaks3" name="coolant_leaks" class="coolant_leaks" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="coolant_leaks_cmnt" name="coolant_leaks_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="steering_fluid" style="color:#37458b">{{__('Steering Fluid')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="steering_fluid1" name="steering_fluid" class="steering_fluid" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="steering_fluid2" name="steering_fluid" class="steering_fluid" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="steering_fluid3" name="steering_fluid" class="steering_fluid" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="steering_fluid_cmnt" name="steering_fluid_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="brake_master_booster" style="color:#37458b">{{__('Brake master and booster')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="brake_master_booster1" name="brake_master_booster" class="brake_master_booster" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="brake_master_booster2" name="brake_master_booster" class="brake_master_booster" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="brake_master_booster3" name="brake_master_booster" class="brake_master_booster" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="brake_master_booster_cmnt" name="brake_master_booster_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="evidence_overheating" style="color:#37458b">{{__('Evidence of overheating')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="evidence_overheating1" name="evidence_overheating" class="evidence_overheating" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="evidence_overheating2" name="evidence_overheating" class="evidence_overheating" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="evidence_overheating3" name="evidence_overheating" class="evidence_overheating" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="evidence_overheating_cmnt" name="evidence_overheating_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="coolant_conditions" style="color:#37458b">{{__('Coolant Conditions')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="coolant_conditions1" name="coolant_conditions" class="coolant_conditions" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="coolant_conditions2" name="coolant_conditions" class="coolant_conditions" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="coolant_conditions3" name="coolant_conditions" class="coolant_conditions" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="coolant_conditions_cmnt" name="coolant_conditions_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="radiator_cap" style="color:#37458b">{{__('Radiator cap')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="radiator_cap1" name="radiator_cap" class="radiator_cap" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="radiator_cap2" name="radiator_cap" class="radiator_cap" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="radiator_cap3" name="radiator_cap" class="radiator_cap" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="radiator_cap_cmnt" name="radiator_cap_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="radiator_fan" style="color:#37458b">{{__('Radiator fan')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="radiator_fan1" name="radiator_fan" class="radiator_fan" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="radiator_fan2" name="radiator_fan" class="radiator_fan" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="radiator_fan3" name="radiator_fan" class="radiator_fan" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="radiator_fan_cmnt" name="radiator_fan_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="fender_liner" style="color:#37458b">{{__('Fender liner')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fender_liner1" name="fender_liner" class="fender_liner" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fender_liner2" name="fender_liner" class="fender_liner" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fender_liner3" name="fender_liner" class="fender_liner" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fender_liner_cmnt" name="fender_liner_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="hoses_pipes" style="color:#37458b">{{__('Hoses & pipes')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="hoses_pipes1" name="hoses_pipes" class="hoses_pipes" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="hoses_pipes2" name="hoses_pipes" class="hoses_pipes" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="hoses_pipes3" name="hoses_pipes" class="hoses_pipes" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="hoses_pipes_cmnt" name="hoses_pipes_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="cable_harnes_connector" style="color:#37458b">{{__('Cable,harnes&connectors')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="cable_harnes_connector1" name="cable_harnes_connector" class="cable_harnes_connector" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="cable_harnes_connector2" name="cable_harnes_connector" class="cable_harnes_connector" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="cable_harnes_connector3" name="cable_harnes_connector" class="cable_harnes_connector" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="cable_harnes_connector_cmnt" name="cable_harnes_connector_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="power_steer_fluidlevel" style="color:#37458b">{{__('Power steering fluid level')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_steer_fluidlevel1" name="power_steer_fluidlevel" class="power_steer_fluidlevel" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_steer_fluidlevel2" name="power_steer_fluidlevel" class="power_steer_fluidlevel" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_steer_fluidlevel3" name="power_steer_fluidlevel" class="power_steer_fluidlevel" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_steer_fluidlevel_cmnt" name="power_steer_fluidlevel_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="engine_oil_level" style="color:#37458b">{{__('Engine oil level')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="engine_oil_level1" name="engine_oil_level" class="engine_oil_level" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="engine_oil_level2" name="engine_oil_level" class="engine_oil_level" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="engine_oil_level3" name="engine_oil_level" class="engine_oil_level" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="engine_oil_level_cmnt" name="engine_oil_level_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="external_engine_leaks" style="color:#37458b">{{__('External engine leaks')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="external_engine_leaks1" name="external_engine_leaks" class="external_engine_leaks" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="external_engine_leaks2" name="external_engine_leaks" class="external_engine_leaks" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="external_engine_leaks3" name="external_engine_leaks" class="external_engine_leaks" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="external_engine_leaks_cmnt" name="external_engine_leaks_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="engine_mounts" style="color:#37458b">{{__('Engine mounts')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="engine_mounts1" name="engine_mounts" class="engine_mounts" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="engine_mounts2" name="engine_mounts" class="engine_mounts" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="engine_mounts3" name="engine_mounts" class="engine_mounts" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="engine_mounts_cmnt" name="engine_mounts_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="turbo_supercharger" style="color:#37458b">{{__('Turbo/ Supercharger')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="turbo_supercharger1" name="turbo_supercharger" class="turbo_supercharger" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="turbo_supercharger2" name="turbo_supercharger" class="turbo_supercharger" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="turbo_supercharger3" name="turbo_supercharger" class="turbo_supercharger" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="turbo_supercharger_cmnt" name="turbo_supercharger_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="fuel_pump_pipes" style="color:#37458b">{{__('Fuel pump & pipes')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fuel_pump_pipes1" name="fuel_pump_pipes" class="fuel_pump_pipes" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fuel_pump_pipes2" name="fuel_pump_pipes" class="fuel_pump_pipes" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fuel_pump_pipes3" name="fuel_pump_pipes" class="fuel_pump_pipes" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fuel_pump_pipes_cmnt" name="fuel_pump_pipes_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="cold_starting" style="color:#37458b">{{__('Cold starting')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="cold_starting1" name="cold_starting" class="cold_starting" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="cold_starting2" name="cold_starting" class="cold_starting" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="cold_starting3" name="cold_starting" class="cold_starting" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="cold_starting_cmnt" name="cold_starting_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="fast_idle" style="color:#37458b">{{__('Fast idle when engine cold')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fast_idle1" name="fast_idle" class="fast_idle" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fast_idle2" name="fast_idle" class="fast_idle" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fast_idle3" name="fast_idle" class="fast_idle" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fast_idle_cmnt" name="fast_idle_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="noise_level" style="color:#37458b">{{__('Noise lvl whn engine cold')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="noise_level1" name="noise_level" class="noise_level" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="noise_level2" name="noise_level" class="noise_level" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="noise_level3" name="noise_level" class="noise_level" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="noise_level_cmnt" name="noise_level_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="excess_smoke" style="color:#37458b">{{__('ExcessSmoke(minor/major)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="excess_smoke1" name="excess_smoke" class="excess_smoke" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="excess_smoke2" name="excess_smoke" class="excess_smoke" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="excess_smoke3" name="excess_smoke" class="excess_smoke" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="excess_smoke_cmnt" name="excess_smoke_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="inlet_manifold" style="color:#37458b">{{__('Inlet manifold')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="inlet_manifold1" name="inlet_manifold" class="inlet_manifold" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="inlet_manifold2" name="inlet_manifold" class="inlet_manifold" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="inlet_manifold3" name="inlet_manifold" class="inlet_manifold" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="inlet_manifold_cmnt" name="inlet_manifold_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="outlet_manifold" style="color:#37458b">{{__('Outlet manifold')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="outlet_manifold1" name="outlet_manifold" class="outlet_manifold" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="outlet_manifold2" name="outlet_manifold" class="outlet_manifold" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="outlet_manifold3" name="outlet_manifold" class="outlet_manifold" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="outlet_manifold_cmnt" name="outlet_manifold_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="exhaust_pipes" style="color:#37458b">{{__('Exhaust Pipes')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="exhaust_pipes1" name="exhaust_pipes" class="exhaust_pipes" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="exhaust_pipes2" name="exhaust_pipes" class="exhaust_pipes" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="exhaust_pipes3" name="exhaust_pipes" class="exhaust_pipes" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="exhaust_pipes_cmnt" name="exhaust_pipes_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="silencer" style="color:#37458b">{{__('Silencer(s)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="silencer1" name="silencer" class="silencer"  value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="silencer2" name="silencer" class="silencer" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="silencer3" name="silencer" class="silencer" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="silencer_cmnt" name="silencer_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="head_shield_mounting" style="color:#37458b">{{__('Head shields & mountings')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="head_shield_mounting1" name="head_shield_mounting" class="head_shield_mounting" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="head_shield_mounting2" name="head_shield_mounting" class="head_shield_mounting" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="head_shield_mounting3" name="head_shield_mounting" class="head_shield_mounting" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="head_shield_mounting_cmnt" name="head_shield_mounting_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="joints_couplings" style="color:#37458b">{{__('Joints & couplings')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="joints_couplings1" name="joints_couplings" class="joints_couplings" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="joints_couplings2" name="joints_couplings" class="joints_couplings" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="joints_couplings3" name="joints_couplings" class="joints_couplings" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="joints_couplings_cmnt" name="joints_couplings_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="engine_underside_leak" style="color:#37458b">{{__('Engine underside leaks')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="engine_underside_leak1" name="engine_underside_leak" class="engine_underside_leak" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="engine_underside_leak2" name="engine_underside_leak" class="engine_underside_leak" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="engine_underside_leak3" name="engine_underside_leak" class="engine_underside_leak" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="engine_underside_leak_cmnt" name="engine_underside_leak_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="catalytic_converter" style="color:#37458b">{{__('Catalytic converter')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="catalytic_converter1" name="catalytic_converter" class="catalytic_converter" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="catalytic_converter2" name="catalytic_converter"class="catalytic_converter" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="catalytic_converter3" name="catalytic_converter"class="catalytic_converter" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="catalytic_converter_cmnt" name="catalytic_converter_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="engine_shield" style="color:#37458b">{{__('Engine shield')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="engine_shield1" name="engine_shield" class="engine_shield" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="engine_shield2" name="engine_shield" class="engine_shield" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="engine_shield3" name="engine_shield" class="engine_shield" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="engine_shield_cmnt" name="engine_shield_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
										</div>
										<hr>

										<!---------------- Transmission--------------->
										<!-- <label for="next_service_due">{{__('Transmission')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Transmission</h5>  
										
										<div class="row"> 
										
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="gear_selector" style="color:#37458b">{{__('Gear selector')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="gear_selector1" name="gear_selector" class="gear_selector" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="gear_selector2" name="gear_selector" class="gear_selector" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="gear_selector3" name="gear_selector" class="gear_selector"value="3"> <label for="na">N/A</label>
													
													<input type="text" id="gear_selector_cmnt" name="gear_selector_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="gear_shifting" style="color:#37458b">{{__('Gear shifting')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="gear_shifting1" name="gear_shifting" class="gear_shifting" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="gear_shifting2" name="gear_shifting" class="gear_shifting" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="gear_shifting3" name="gear_shifting" class="gear_shifting" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="gear_shifting_cmnt" name="gear_shifting_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="gear_noise" style="color:#37458b">{{__('Gear noise')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="gear_noise1" name="gear_noise" class="gear_noise" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="gear_noise2" name="gear_noise" class="gear_noise" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="gear_noise3" name="gear_noise" class="gear_noise" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="gear_noise_cmnt" name="gear_noise_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="fluid_level_oil_leak" style="color:#37458b">{{__('Fluid Level & Oil Leak')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fluid_level_oil_leak1" name="fluid_level_oil_leak" class="fluid_level_oil_leak" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="fluid_level_oil_leak2" name="fluid_level_oil_leak" class="fluid_level_oil_leak" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fluid_level_oil_leak3" name="fluid_level_oil_leak" class="fluid_level_oil_leak" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="fluid_level_oil_leak_cmnt" name="fluid_level_oil_leak_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-3">
                                                <div class="mb-3"> 
													<label for="transmission_mount" style="color:#37458b">{{__('Transmission Mount (Gear Mount)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="transmission_mount1" name="transmission_mount" class="transmission_mount" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="transmission_mount2" name="transmission_mount" class="transmission_mount" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="transmission_mount3" name="transmission_mount" class="transmission_mount" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="transmission_mount_cmnt" name="transmission_mount_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
										</div>
										
										<!---------------- Electrical--------------->
										<!-- <label for="next_service_due">{{__('Electrical')}}<span class="text-red"> </span></label> <br> -->
										<h5 style="color:#37458b">Electrical</h5>  
										
										<div class="row"> 
										
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="door_locks" style="color:#37458b">{{__('Door locks')}}<span class="text-red"> </span></label> <br> <!--(which side) -->
													<input type="radio" id="door_locks1" name="door_locks" class="door_locks" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="door_locks2" name="door_locks" class="door_locks" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="door_locks3" name="door_locks" class="door_locks" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="door_locks_cmnt" name="door_locks_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="central_locking" style="color:#37458b">{{__('Central Locking')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="central_locking1" name="central_locking" class="central_locking" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="central_locking2" name="central_locking" class="central_locking" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="central_locking3" name="central_locking" class="central_locking" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="central_locking_cmnt" name="central_locking_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="ignitionlock_startsys" style="color:#37458b">{{__('Ignition lock/Starting sys')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="ignitionlock_startsys1" name="ignitionlock_startsys" class="ignitionlock_startsys" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="ignitionlock_startsys2" name="ignitionlock_startsys" class="ignitionlock_startsys" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="ignitionlock_startsys3" name="ignitionlock_startsys" class="ignitionlock_startsys" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="ignitionlock_startsys_cmnt" name="ignitionlock_startsys_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="instrument_panel" style="color:#37458b">{{__('Instrument panel')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="instrument_panel1" name="instrument_panel" class="instrument_panel" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="instrument_panel2" name="instrument_panel" class="instrument_panel" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="instrument_panel3" name="instrument_panel" class="instrument_panel" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="instrument_panel_cmnt" name="instrument_panel_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="headlights" style="color:#37458b">{{__('Headlights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="headlights1" name="headlights" class="headlights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="headlights2" name="headlights" class="headlights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="headlights3" name="headlights" class="headlights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="headlights_cmnt" name="headlights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="sidelights_runlights" style="color:#37458b">{{__('Sidelights / Running lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sidelights_runlights1" name="sidelights_runlights" class="sidelights_runlights" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="sidelights_runlights2" name="sidelights_runlights" class="sidelights_runlights" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sidelights_runlights3" name="sidelights_runlights" class="sidelights_runlights" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="sidelights_runlights_cmnt" name="sidelights_runlights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="rear_lights" style="color:#37458b">{{__('Rear lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="rear_lights1" name="rear_lights" class="rear_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="rear_lights2" name="rear_lights" class="rear_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="rear_lights3" name="rear_lights" class="rear_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="rear_lights_cmnt" name="rear_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="indicator_hazardlights" style="color:#37458b">{{__('Indicator / Hazard lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="indicator_hazardlights1" name="indicator_hazardlights" class="indicator_hazardlights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="indicator_hazardlights2" name="indicator_hazardlights" class="indicator_hazardlights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="indicator_hazardlights3" name="indicator_hazardlights" class="indicator_hazardlights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="indicator_hazardlights_cmnt" name="indicator_hazardlights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="boot_tailgate_lock" style="color:#37458b">{{__('Boot / Tailgate lock')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="boot_tailgate_lock1" name="boot_tailgate_lock" class="boot_tailgate_lock" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="boot_tailgate_lock2" name="boot_tailgate_lock" class="boot_tailgate_lock" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="boot_tailgate_lock3" name="boot_tailgate_lock" class="boot_tailgate_lock" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="boot_tailgate_lock_cmnt" name="boot_tailgate_lock_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="reverse_lights"style="color:#37458b">{{__('Reverse lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="reverse_lights1" name="reverse_lights" class="reverse_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="reverse_lights2" name="reverse_lights" class="reverse_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="reverse_lights3" name="reverse_lights" class="reverse_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="reverse_lights_cmnt" name="reverse_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="fog_lights" style="color:#37458b">{{__('Fog lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="fog_lights1" name="fog_lights" class="fog_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="fog_lights2" name="fog_lights" class="fog_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="fog_lights3" name="fog_lights" class="fog_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="fog_lights_cmnt" name="fog_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="multimedia" style="color:#37458b">{{__('Multimedia')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="multimedia1" name="multimedia" class="multimedia" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="multimedia2" name="multimedia" class="multimedia" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="multimedia3" name="multimedia" class="multimedia" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="multimedia_cmnt" name="multimedia_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="ac_control_cooling" style="color:#37458b">{{__('A/C Control & Cooling')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="ac_control_cooling1" name="ac_control_cooling" class="ac_control_cooling" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="ac_control_cooling2" name="ac_control_cooling" class="ac_control_cooling" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="ac_control_cooling3" name="ac_control_cooling" class="ac_control_cooling" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="ac_control_cooling_cmnt" name="ac_control_cooling_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="side_mirror" style="color:#37458b">{{__('Side Mirror')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="side_mirror1" name="side_mirror" class="side_mirror" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="side_mirror2" name="side_mirror" class="side_mirror" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="side_mirror3" name="side_mirror" class="side_mirror" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="side_mirror_cmnt" name="side_mirror_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="auxiliary_lights" style="color:#37458b">{{__('Auxiliary lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="auxiliary_lights1" name="auxiliary_lights" class="auxiliary_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="auxiliary_lights2" name="auxiliary_lights" class="auxiliary_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="auxiliary_lights3" name="auxiliary_lights" class="auxiliary_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="auxiliary_lights_cmnt" name="auxiliary_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="panel_lights" style="color:#37458b">{{__('Panel lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="panel_lights1" name="panel_lights" class="panel_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="panel_lights2" name="panel_lights" class="panel_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="panel_lights3" name="panel_lights" class="panel_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="panel_lights_cmnt" name="panel_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="horn" style="color:#37458b">{{__('Horn')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="horn1" name="horn" class="horn" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="horn2" name="horn" class="horn" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="horn3" name="horn" class="horn" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="horn_cmnt" name="horn_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="window_operation" style="color:#37458b">{{__('Window operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="window_operation1" name="window_operation" class="window_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="window_operation2" name="window_operation" class="window_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="window_operation3" name="window_operation" class="window_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="window_operation_cmnt" name="window_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="sunroof_operation" style="color:#37458b">{{__('Sunroof  operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="sunroof_operation1" name="sunroof_operation" class="sunroof_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="sunroof_operation2" name="sunroof_operation" class="sunroof_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="sunroof_operation3" name="sunroof_operation" class="sunroof_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="sunroof_operation_cmnt" name="sunroof_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="wipers_jet_washers" style="color:#37458b">{{__('Wipers / Jet washers')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="wipers_jet_washers1" name="wipers_jet_washers" class="wipers_jet_washers" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="wipers_jet_washers2" name="wipers_jet_washers" class="wipers_jet_washers" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="wipers_jet_washers3" name="wipers_jet_washers" class="wipers_jet_washers" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="wipers_jet_washers_cmnt" name="wipers_jet_washers_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="keys_remote_controls" style="color:#37458b">{{__('Keys & remote controls')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="keys_remote_controls1" name="keys_remote_controls" class="keys_remote_controls" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="keys_remote_controls2" name="keys_remote_controls" class="keys_remote_controls" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="keys_remote_controls3" name="keys_remote_controls" class="keys_remote_controls" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="keys_remote_controls_cmnt" name="keys_remote_controls_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="warning_lights" style="color:#37458b">{{__('Warning lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="warning_lights1" name="warning_lights" class="warning_lights" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="warning_lights2" name="warning_lights" class="warning_lights" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="warning_lights3" name="warning_lights" class="warning_lights" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="warning_lights_cmnt" name="warning_lights_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="number_plate_light" style="color:#37458b">{{__('Number plate lights')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="number_plate_light1" name="number_plate_light" class="number_plate_light" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="number_plate_light2" name="number_plate_light" class="number_plate_light" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="number_plate_light3" name="number_plate_light" class="number_plate_light" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="number_plate_light_cmnt" name="number_plate_light_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
							 
										</div>
										
										<!----------- Underbody ----------->
										<hr>
										<h5 style="color:#37458b">Underbody</h5>  
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="steering_ball_joints" style="color:#37458b">{{__('Steering joints & ball joints')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="steering_ball_joints1" name="steering_ball_joints" class="steering_ball_joints" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="steering_ball_joints2" name="steering_ball_joints" class="steering_ball_joints" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="steering_ball_joints3" name="steering_ball_joints" class="steering_ball_joints" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="steering_ball_joints_cmnt" name="steering_ball_joints_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="brakes_lines" style="color:#37458b">{{__('Brakes lines')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="brakes_lines1" name="brakes_lines" class="brakes_lines" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="brakes_lines2" name="brakes_lines" class="brakes_lines" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="brakes_lines3" name="brakes_lines" class="brakes_lines" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="brakes_lines_cmnt" name="brakes_lines_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="subframe" style="color:#37458b">{{__('Subframe')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="subframe1" name="subframe" class="subframe" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="subframe2" name="subframe" class="subframe" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="subframe3" name="subframe" class="subframe" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="subframe_cmnt" name="subframe_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="wheels_hubs_bearings" style="color:#37458b">{{__('Wheels, hubs & bearings')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="wheels_hubs_bearings1" name="wheels_hubs_bearings" class="wheels_hubs_bearings" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="wheels_hubs_bearings2" name="wheels_hubs_bearings" class="wheels_hubs_bearings" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="wheels_hubs_bearings3" name="wheels_hubs_bearings" class="wheels_hubs_bearings" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="wheels_hubs_bearings_cmnt" name="wheels_hubs_bearings_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="dampers_bushes" style="color:#37458b">{{__('Dampers and bushes')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="dampers_bushes1" name="dampers_bushes" class="dampers_bushes" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="dampers_bushes2" name="dampers_bushes" class="dampers_bushes" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="dampers_bushes3" name="dampers_bushes" class="dampers_bushes" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="dampers_bushes_cmnt" name="dampers_bushes_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="power_steering_rack" style="color:#37458b">{{__('Power steering/ rack')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="power_steering_rack1" name="power_steering_rack" class="power_steering_rack" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="power_steering_rack2" name="power_steering_rack" class="power_steering_rack" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="power_steering_rack3" name="power_steering_rack" class="power_steering_rack" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="power_steering_rack_cmnt" name="power_steering_rack_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-3">
                                                <div class="mb-3"> 
													<label for="evidencefloor_chassis" style="color:#37458b">{{__('Evidence of floor/chassis corrosion')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="evidencefloor_chassis1" name="evidencefloor_chassis" class="evidencefloor_chassis" value="1"> 
													<label for="pass">Pass</label>
													  
													<input type="radio" id="evidencefloor_chassis2" name="evidencefloor_chassis" class="evidencefloor_chassis" value="2"> 
													<label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="evidencefloor_chassis3" name="evidencefloor_chassis" class="evidencefloor_chassis" value="3"> 
													<label for="na">N/A</label>
													
													<input type="text" id="evidencefloor_chassis_cmnt" name="evidencefloor_chassis_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											 
										</div>
										
										<!-------------- Test Drive -------------->
										<h5 style="color:#37458b">Test Drive</h5>  
										<div class="row"> 
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="engine_performance" style="color:#37458b">{{__('Engine - Performance')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="engine_performance1" name="engine_performance" class="engine_performance" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="engine_performance2" name="engine_performance" class="engine_performance" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="engine_performance3" name="engine_performance" class="engine_performance" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="engine_performance_cmnt" name="engine_performance_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="gearbox_operation" style="color:#37458b">{{__('Gearbox operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="gearbox_operation1" name="gearbox_operation" class="gearbox_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="gearbox_operation2" name="gearbox_operation" class="gearbox_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="gearbox_operation3" name="gearbox_operation" class="gearbox_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="gearbox_operation_cmnt" name="gearbox_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="clutch_operation" style="color:#37458b">{{__('Clutch operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="clutch_operation1" name="clutch_operation" class="clutch_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="clutch_operation2" name="clutch_operation" class="clutch_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="clutch_operation3" name="clutch_operation" class="clutch_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="clutch_operation_cmnt" name="clutch_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="steering_operation" style="color:#37458b">{{__('Steering Operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="steering_operation1" name="steering_operation" class="steering_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="steering_operation2" name="steering_operation" class="steering_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="steering_operation3" name="steering_operation" class="steering_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="steering_operation_cmnt" name="steering_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="brake_operation" style="color:#37458b">{{__('Brake Operation')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="brake_operation1" name="brake_operation" class="brake_operation" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="brake_operation2" name="brake_operation" class="brake_operation" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="brake_operation3" name="brake_operation" class="brake_operation" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="brake_operation_cmnt" name="brake_operation_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="hand_parking_brake" style="color:#37458b">{{__('Hand brake/ Parking brake')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="hand_parking_brake1" name="hand_parking_brake" class="hand_parking_brake" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="hand_parking_brake2" name="hand_parking_brake" class="hand_parking_brake" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="hand_parking_brake3" name="hand_parking_brake" class="hand_parking_brake" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="hand_parking_brake_cmnt" name="hand_parking_brake_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="drive_train" style="color:#37458b">{{__('DriveTrain(4WD,2WD,AWD)')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="drive_train1" name="drive_train" class="drive_train" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="drive_train2" name="drive_train" class="drive_train" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="drive_train3" name="drive_train" class="drive_train" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="drive_train_cmnt" name="drive_train_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="instru_control_func" style="color:#37458b">{{__('Instrument & cntrl functng')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="instru_control_func1" name="instru_control_func" class="instru_control_func" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="instru_control_func2" name="instru_control_func" class="instru_control_func" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="instru_control_func3" name="instru_control_func" class="instru_control_func" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="instru_control_func_cmnt" name="instru_control_func_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3"> 
													<label for="suspension_noise" style="color:#37458b">{{__('Suspension noise')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="suspension_noise1" name="suspension_noise" class="suspension_noise" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="suspension_noise2" name="suspension_noise" class="suspension_noise" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="suspension_noise3" name="suspension_noise" class="suspension_noise" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="suspension_noise_cmnt" name="suspension_noise_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="road_holding_stability" style="color:#37458b">{{__('Road holding stability')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="road_holding_stability1" name="road_holding_stability" class="road_holding_stability" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="road_holding_stability2" name="road_holding_stability" class="road_holding_stability" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="road_holding_stability3" name="road_holding_stability" class="road_holding_stability" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="road_holding_stability_cmnt" name="road_holding_stability_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="nois" style="color:#37458b">{{__('Nois')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="nois1" name="nois" class="nois" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="nois2" name="nois" class="nois" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="nois3" name="nois" class="nois" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="nois_cmnt" name="nois_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-sm-2">
                                                <div class="mb-3">   
													<label for="shock_absorber" style="color:#37458b">{{__('Shock absorber')}}<span class="text-red"> </span></label> <br>
													
													<input type="radio" id="shock_absorber1" name="shock_absorber" class="shock_absorber" value="1"> <label for="pass">Pass</label>
													  
													<input type="radio" id="shock_absorber2" name="shock_absorber" class="shock_absorber" value="2"> <label for="fail">Fail</label> &nbsp;&nbsp;
													
													<input type="radio" id="shock_absorber3" name="shock_absorber" class="shock_absorber" value="3"> <label for="na">N/A</label>
													
													<input type="text" id="shock_absorber_cmnt" name="shock_absorber_cmnt" class="form-control" placeholder="Comments" style="display:none">
												</div>
											</div>
											
											<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
												<div class="mb-3">
												
													<a href="#"  onclick="backToTab('checklistForm','specification')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a>
												
													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addInspectionChecklist")}}', 'checklistForm', 'add-modal', 'inspectionReportDataTable','',true,'summary')" class="btn btn-info btn-block" value="Save & Next"> 
												</div>
											</div>
										</div>
									</form>
								</div>
								
								<!------------------ FORM 4 - SUMMARY ------------------>
								<!-- <div class="tab-pane fade show active" id="summary_old" role="tabpanel" aria-labelledby="summary-tab_old" style="display:none;">  16-05-2025-->
								<div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
								
									<form class="myform" method="post" id="summaryForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
 
										<div class="row"> 
 
											<div class="table-responsive m-t-40">   
												<label style="margin-top:10px"><b>Inspection Summary </b></label>
 
												<table class="mytable table table-bordered table-hover   no-footer" id="interestItemTable_summary">
													<thead>
														<tr>
															<th style="width: 33px" class="center">#</th>
															<th style="width:250px" class="center">Summary Type</th>
															<th style="width:550px" class="center">Summary Description</th>
															<th style="width:550px" class="center">Summary Desc. in Arabic</th>
															<th style="width: 33px;" class="center"></th>
														</tr>
													</thead>
													<tbody id="interest_tbody_summary">
														<tr id="interest_tr_3">
															<td class="center"> 1 </td>
															<td class="prop">
																<select type="text" class="form-control form-select input-sm input-sm-100 extra_type" data-input_id="1" name="extra_type[]" id="extra_type" placeholder="Extra Type" onchange="SummaryDes(this.value,'')">
																	<option value="">-- Summary Type --</option>
																	<?php 
																	foreach($summ_type as $stype)
																	{ ?>
																		<option value="<?= $stype->summary_type_id; ?>"><?= $stype->summary_type_name; ?></option> <?php 
																	} ?>
																</select>
																<div id="notification1" style="color:red;font-style:italic; font-size:12px;"></div>
																<div style="color:red;font-style:italic;font-size: 12px;display:none;width:100%;margin-top:-5.75rem; padding-left:10px;" id="error_p1";> Please select an option. </div>
															</td>
															
															<td class="prop">
																<select type="text" class="form-control form-select input-sm input-sm-100 select2 extra_name" data-input_id="1" name="extra_name[]" id="extra_name" placeholder="Extra Desc" data-extra_name="" >
																	<option value="">-- Summary Desc --</option>
																</select>
																<!--<input type="text" class="form-control input-sm input-sm-100 extra_name1" data-input_id="1" name="extra_name1[]" id="extra_nam1e" placeholder="Summary Desc">-->
															</td>
															
															<td class="prop">
																<input type="text" class="form-control input-sm input-sm-100 extra_name_ar" data-input_id="1" name="extra_name_ar[]" id="extra_name_ar" placeholder="Summary Desc. in Arabic">
															</td>
															
															<td class="remove_new_row_summary center" title="Remove Name" onclick="remove_row_interest_summary('1')" style="cursor:pointer">
																<i class="fa fa-times" aria-hidden="true"></i>
															</td>
														</tr>
													</tbody>
												</table>
												<div class="add_new_row_summary" id="add_new_edu_items_summary">
													<input type="hidden" id="interest_row_start_summary" value="2">
													<a title="Add New Summary" onclick="add_new_row_interest_summary();" style="cursor: pointer;margin-bottom: 20px;">
														<strong> + Add New  </strong>
													</a>
												</div>
												
												<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
													<div class="mb-3">
														<a href="#"  onclick="backToTab('summaryForm','checklist')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
														
														<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addInspectionSummary")}}', 'summaryForm', 'add-modal', 'inspectionReportDataTable','',true,'overview')" class="btn btn-info btn-block" value="Save & Next"> 
													</div>
												</div> 
											</div>
										</div>
									</form>
								</div>
								
								<!------------------ FORM 5 - OVERVIEW ------------------>
								<!-- <div class="tab-pane fade show active" id="overview_old" role="tabpanel" aria-labelledby="overview-tab_old" style="display:none;">  16-05-2025-->
								<div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab">
								
									<form class="myform" method="post" id="overviewForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id" name="edit_id" >
										<input type="hidden" class="update_id" name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
 
										<div class="row"> 
											<label style="margin-top:10px"><b>Vehicle Overview </b></label>
											<div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Overview')}}<span class="text-red"> </span></label>
                                                    <textarea id="overview_english" type="text" class="form-control @error('overview_english') is-invalid @enderror" name="overview_english" placeholder="Vehicle Overview" data-parsley-required-message="Vehicle Overview" rows="5">  </textarea>
                                                    <div class="help-block with-errors"></div>
                                                    @error('overview_english')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>		
											<div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Overview in Arabic')}}<span class="text-red"> </span></label>
                                                    <textarea id="overview_arabic" type="text" class="form-control @error('overview_arabic') is-invalid @enderror" name="overview_arabic" placeholder="Vehicle Overview in Arabic" data-parsley-required-message="Vehicle Overview in Arabic" rows="5">  </textarea>
                                                    <div class="help-block with-errors"></div>
                                                    @error('overview_arabic')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>	

											<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
												<div class="mb-3">
													<a href="#" onclick="backToTab('overviewForm','summary')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a>
													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addVehicleOverview")}}', 'overviewForm', 'add-modal', 'inspectionReportDataTable','',true,'reports')" class="btn btn-info btn-block" value="Save & Next"> 
												</div>
											</div> 

										</div>
										
									</form>
								</div>
								
								<!------------------ FORM - REPORTS ------------------>
 								<!-- <div class="tab-pane fade show active" id="reports_old" role="tabpanel" aria-labelledby="reports-tab_old" style="display:none;" > 16-05-2025--> 
								<div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
								
									<form class="myform" method="post" id="reportsForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
									 
										<div class="row"> 
										 
											<div class="col-md-6" style="width: 100%;height: auto;">
											
												<div class="table-responsive m-t-40">   
													<label style="margin-top:10px"><b>Reports</b> <span style="color:red;"> </span> </label>
													<table class="mytable table table-bordered table-hover   no-footer" id="interestItemTable_reports">
														<thead>
															<tr>
																<th style="width:33px" class="center">#</th>
																<th style="width:250px" class="center">Reports</th>
																<th style="width: 33px;" class="center"></th>
															</tr>
														</thead>
														<tbody id="interest_tbody_reports">
															<tr id="interest_tr_4">
																<td class="center"> 1 </td>
																<td class="prop">
																	<input type="hidden" name="g_sl_rep[]" id="g_sl_rep1"/>
																	<input type="hidden" id="rep_file" name="rep_file" class="file">
																	<input type="file" class="file form-control input-sm input-sm-100 rep_file" data-input_id="1" name="report_file[]" id="report_file1" placeholder="">
																	<div id="image3" class="image3" style="font-size:10px;"><span class="error-span" style="font-size:10px;"></span></div>
																</td>
																<td class="remove_new_row_reports center" title="Remove Name" onclick="remove_row_interest_reports('1')">
																	<i class="fa fa-times" aria-hidden="true"></i>
																</td>
															</tr>
														</tbody>
													</table>
													<div class="add_new_row_reports" id="add_new_edu_items_reports">
														<input type="hidden" id="interest_row_start_reports" value="2">
														<a title="Add New Area" onclick="add_new_row_interest_reports()" style="cursor: pointer;margin-bottom: 20px;">
															<strong> + Add New  </strong>
														</a>
													</div>
												</div>
											 
												<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
													<div class="mb-3">
														<a href="#"  onclick="backToTab('reportsForm','overview')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a>
														<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addReportsfile")}}', 'reportsForm', 'add-modal', 'inspectionReportDataTable','',true,'gallery')" class="btn btn-info btn-block" value="Save & Next"> 
													</div>
												</div> 
                                            
											</div>
                                        </div>
									</form>
								</div>
								<!--------------- REPORTS END --------------->
								
								<!------------------ FORM 4 - GALLERY ------------------>
 								<!--<div class="tab-pane fade show active" id="gallery_old" role="tabpanel" aria-labelledby="gallery-tab_old" style="display:none;" >  16-05-2025-->
								<div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
							
									<form class="myform" method="post" id="galleryForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
									 
										<div class="row"> 
										
											<div class="col-sm-3" style="display:none;">
                                                <div class="mb-3">
                                                    <label for="video_url">{{ __('Video URL')}}<span class="text-red"> </span></label>
                                                    <input id="video_url" type="text" class="form-control @error('video_url') is-invalid @enderror" name="video_url" placeholder="Video URL" data-parsley-required-message="Seats required">
                                                    <div class="help-block with-errors"></div>
                                                    @error('video_url')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="video_file">{{ __('Video')}}<span class="text-red"> </span></label>
                                                    <input type="file" id="video_file"class="form-control @error('video_file') is-invalid @enderror" name="video_file" placeholder="" data-parsley-required-message="required">
                                                    <div class="help-block with-errors"></div>
                                                    @error('video_file')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
										
											<div class="col-md-6" style="width: 100%;height: auto;">
											
												<div class="table-responsive m-t-40">   
													<label style="margin-top:10px"><b>Gallery</b> <span style="color:red;"> </span> </label>
													<table class="mytable table table-bordered table-hover   no-footer" id="interestItemTable_gallery">
													
														<thead>
															<tr>
																<th style="width:33px" class="center">#</th>
																<th style="width:200px" class="center">Image Type</th>
																<th style="width:200px" class="center">Image</th>
																<th style="width:250px" class="center">Image Description</th>
																<th style="width: 33px;" class="center"></th>
															</tr>
														</thead>
														<tbody id="interest_tbody_gallery">
															<tr id="interest_tr_4">
																<td class="center"> 1 </td>
																<td class="prop">
																	<select class="form-control form-select gallery_image_type" name="gallery_image_type[]" id="gallery_image_type" data-input_id="1"data-parsley-required-message="Image Type Required"> <!--onchange="imgTypeCheckUnique(this.id,1);"-->
																		<option value=""> -- Select Image Type -- </option> 
																		<?php 
																		foreach($gall_type as $gtype)
																		{ ?>
																			<option value="<?= $gtype->gallery_type_id; ?>"><?= $gtype->gallery_type_name; ?></option> <?php 
																		} ?>
																	</select>
																	<div id="galNotification1" style="color: red; font-style:italic; font-size:12px;"></div>
																</td>
																 
																<td class="prop">
																	<input type="hidden" name="g_sl[]" id="g_sl1"/>
																	<input type="hidden" id="img_main_image" name="img_main_image" class="file">
																	<input type="file" class="file form-control input-sm input-sm-100 gall_img" data-input_id="1" name="file[]" id="file1" placeholder="">
																	<div id="image2" class="image2" style="font-size:10px;"><span class="error-span" style="font-size:10px;"></span></div>
																</td>
																
																<td class="prop">
																	<textarea class="form-control input-sm input-sm-100" data-input_id="1" name="gallery_image_desc[]" id="gallery_image_desc" placeholder="Image Description" rows="1">
																	</textarea>
																</td>
																
																<td class="remove_new_row_gallery center" title="Remove Name" onclick="remove_row_interest_gallery('1')" style="cursor:pointer">
																	<i class="fa fa-times" aria-hidden="true"></i>
																</td>
															</tr>
														</tbody>
													</table>
													<div class="add_new_row_gallery" id="add_new_edu_items_gallery">
														<input type="hidden" id="interest_row_start_gallery" value="2">
														<a title="Add New Area" onclick="add_new_row_interest_gallery();" style="cursor: pointer;margin-bottom: 20px;">
															<strong> + Add New   </strong>
														</a>
													</div>
												</div>
											 
                                            <div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
                                                <div class="mb-3">
												
													<a href="#"  onclick="backToTab('galleryForm','reports')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a>
												
 													<input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addInspectionGallery")}}', 'galleryForm', 'add-modal', 'inspectionReportDataTable','',true,'damages')" class="btn btn-info btn-block" value="Save & Next"> 
                                                </div>
                                            </div> 
                                            
                                        </div>
                                        </div>
									</form>
								</div>
								<!--------------- GALLERY END --------------->
								
								<!------------------ FORM - DAMAGES START ------------------>
 								<!--<div class="tab-pane fade show active" id="damages_old" role="tabpanel" aria-labelledby="damages-tab_old" style="display:none;" >  16-05-2025-->
								<div class="tab-pane fade" id="damages" role="tabpanel" aria-labelledby="damages-tab">
								
									<form class="myform" method="post" id="damagesForm" enctype="multipart/form-data">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id">
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
									 
										<div class="row"> 
										
											<div class="col-sm-7" style="overflow:auto">
												<input type='file' id="fileUpload" name="damage_image" />
												<canvas id="canvas" width="512" height="512"></canvas>	 
											</div>
						 				     
											<div class="col-sm-1"><br><br> 
												<div class="colors">
												  <h5> Dent </h5>
												  <div class="color" id="orange" style="background-color:#ff9800" data-hex="#ff9800"></div>
												  
												  <h5> Re-Painted </h5>
												  <div class="color" id="red" style="background-color:#f44336" data-hex="#f44336"></div>
												  
												  <h5> Faded </h5>
												  <div class="color" id="blue" style="background-color:#2196f3" data-hex="#2196f3"></div>
												  
												  <h5> Broken </h5>
												  <div class="color" id="black" style="background-color:black" data-hex="black"></div>
												  
												  <h5> Scratch </h5>
												  <div class="color" id="yellow" style="background-color:#dfcd2b" data-hex="#dfcd2b"></div>
												  
												  <h5> Sticker Work </h5>
												  <div class="color" id="green" style="background-color:green" data-hex="green"></div>
												</div>
											</div>
											
											<div class="col-sm-3" id="damageImg"> </div>
											
										    <!-- <a id="downloadLnk" download="YourFileName.jpg">Download as image</a> -->	
										
											<div class="col-md-3" style="margin-top:25px"> <!-- saveButton -->
                                                <div class="mb-3">
 													<!-- <input type="button" onclick="insertEntry('{{URL::to("inspectionreport/addDamages")}}', 'damagesForm', 'add-modal', 'inspectionReportDataTable','',true,'damages')" class="btn btn-info btn-block" value="Save & Next">  -->
													<a href="#"  onclick="backToTab('damagesForm','gallery')" style="background-color: #10707f;" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back</a> &nbsp;
													
													<input type="button" class="btn btn-info btn-block" id="quotetbn" value="Upload">
                                                 </div>
                                            </div> 
										</div>
										
									</form>
								</div>
								<!--------------------------------------------->
								<!------------------ FORM 3 - ADDITIONAL SPEC ------------------>
						        <div class="tab-pane fade show active" id="spec" role="tabpanel" aria-labelledby="spec-tab" style="display:none;" >  
							
							    <!--<div class="tab-pane fade" id="spec_old" role="tabpanel" aria-labelledby="spec-tab_old">-->
								{!! html()->form('POST')->attributes(['method'=>'post', 'id'=>'specForm', 'class'=>'myform'])->open() !!}
									<input type='hidden' name='_token' value='{{csrf_token()}}'>
                                    <input type="hidden" class="edit_id" name="edit_id" >
									<input type="hidden" class="update_id" name="update_id"> <!-- Edit Report id -->
									<input type="hidden" class="add_edit" name="add_edit" value="0">
								{!! html()->form()->close() !!} 
                                    
								</div> 
								
								<!------------------ FORM 4 - WARRANTY ------------------>
								<div class="tab-pane fade show active" id="warranty" role="tabpanel" aria-labelledby="warranty-tab" style="display:none;" >  
								<!-- <div class="tab-pane fade" id="warranty_old" role="tabpanel" aria-labelledby="warranty-tab">-->
									<form class="myform" method="post" id="warrantyForm">
										<input type='hidden' name='_token' value='{{csrf_token()}}'>
										<input type="hidden" class="edit_id"  name="edit_id" >
										<input type="hidden" class="update_id"  name="update_id"> <!-- Edit Report id -->
										<input type="hidden" class="add_edit" name="add_edit" value="0">
									</form>
								</div>
							
							<!----------------------------------->
							</div>  <!-- myTabContent -->
						</div>  <!-- row authentication-form mx-auto -->
					</div> <!-- card-body -->
				</div> <!-- card -->
			</div>
		</div>
   
		<div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!--  <h4 class="card-title">View clients</h4>
                            <p class="card-title-desc"></p>-->
                            <div class="table-responsive">  
                                <table id="inspectionReportDataTable" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> 
                                    <thead>
                                        <tr>
                                            <th>{{ __('#')}}</th>
                                            <th>{{ __('Reference')}}</th>
                                            <th>{{ __('Name')}}</th>
                                            <th style="width:5%">{{ __('Date of Inspection')}}</th>
                                            <th>{{ __('Plate No.')}}</th>
                                            <th>{{ __('Current Status')}}</th>
                                            <th>{{ __('Change Status')}}</th>
											<th>{{ __('Expired Status')}}</th>
											<th>{{ __('Added By')}}</th>
                                            <th style="text-align:center;width:8%">{{ __('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>      
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
                        
            <!-------------------- Delete Modal -------------------->            
            <div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Delete Reoprt</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
							{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}
                            <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                            <input type='hidden' name='del_id' id="del_id" value=''> 
                            <p>Are you sure,You want to delete the Reoprt?</p>
							{!! html()->form()->close() !!} 
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
			<!------------------- Delete Modal ------------------->      
			
			<!-------------------- View Modal -------------------->
			<div class="modal fade bs-example-modal-xl" id="view_modal"  role="dialog" aria-labelledby="exampleModalCenterLabel" aria-modal="true" style="display:none">
				<div class="modal-dialog modal-dialog-scrollable modal-xl" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"> </span></button>
						</div>
						<div class="modal-body" id="view-modal-body"> </div>
					</div>
				</div>
			</div>
			<!-------------------- View Modal -------------------->

	</div> <!-- container-fluid -->   
</div> <!-- page-content -->   
   
@endsection

@section('js')

<script type="text/javascript">
	var public_path = '<?php echo url('/');?>';
	var url_DivisionDataTable = '{{URL::to("inspectionreport/getDatatable")}}';
	var url_editdivision      = '{{URL::to("inspectionreport/getDivisions")}}';
	var url_viewReport        = '{{URL::to("inspectionreport/viewInspectionReport")}}';
	var url_deleteReport      = '{{URL::to("inspectionreport/deleteInspectionReport")}}';
	
	var url_addDamages        = '{{URL::to("inspectionreport/addDamages")}}';
	var url_statusData        = '{{URL::to("inspectionreport/getstatus")}}'; 
	var url_add_followup      = "{{url('/leads/add_followup')}}";
</script>
  	
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
   
<script src="{{asset('module.js/main.js?ver=1.3')}}"></script>
<script src="{{asset('module.js/InspectionReport/index.js?ver=5.0')}}"></script>
 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"/></script>

<script src="https://cdn.jsdelivr.net/gh/dubrox/Multiple-Dates-Picker-for-jQuery-UI@master/jquery-ui.multidatespicker.js"></script>

<!--<script src="http://multidatespickr.sourceforge.net/jquery-ui.multidatespicker.js"/></script>-->

<!------------ Gallery Start ------------>
<script type="text/javascript">
function add_new_row_interest_gallery() 
{   
    var new_row_data = $('#interest_row_start_gallery').val();
    var test12 = $('#test11').val();
    var test12 = parseFloat(test12);
    if (test12) {new_row_data=test12+1;}
    
    var t1= new_row_data-1;
    var fileId = $('#file'+t1)[0];
    
    var list_id = $('#list_id').val();
    
    if(list_id != '')
    { 
        if($('#file'+t1).length)  // check file  exist or not 
        {
            if(typeof (fileId.files[0]) != "undefined")
            {
                add_gallery(new_row_data);
            }
        }
        else
        {
            add_gallery(1);
        }
    }
    else
    {
        if(typeof (fileId.files[0]) != "undefined")
        {
            add_gallery(new_row_data);
        }
    }
}

function add_gallery(new_row_data)
{
    $('#total_row').val(new_row_data);
    $('#interest_row_start_gallery').val(parseInt(new_row_data) + 1); 
    // var new_row_data = 4;
    $('#interest_tbody_gallery').append('<tr id="interest_tr_4' + new_row_data + '"><td class="center text">' + new_row_data + '</td><td class="prop"><select class="form-control form-select gallery_image_type" name="gallery_image_type[]" id="gallery_image_type' + new_row_data + '" data-parsley-required-message="Image Type Required" data-input_id="' + new_row_data + '"  ><option value=""> -- Select Image Type -- </option><?php foreach($gall_type as $gtype){ ?><option value="<?= $gtype->gallery_type_id; ?>"> <?= $gtype->gallery_type_name;?></option><?php } ?></select><div id="galNotification'+new_row_data+'" style="color:red; font-style:italic; font-size:12px;" ></div></td> <td class="prop"><input type="hidden" name="g_sl[]" id="g_sl'+new_row_data+'"/><input type="hidden" class="file form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" id="img_main_image' + new_row_data + '" name="img_main_image"><input type="file" class="file form-control input-sm input-sm-100 gall_img" data-input_id="' + new_row_data + '" name="file[]" id="file' + new_row_data + '" placeholder=""><div class="image2" id="image2' + new_row_data + '"><span class="error-span"></span></div></td> <td class="prop"><textarea class="form-control input-sm input-sm-100" data-input_id="'+ new_row_data +'" name="gallery_image_desc[]" id="gallery_image_desc" placeholder="Image Description" rows="1"></textarea></td><td class="remove_new_row_gallery center" title="Remove Row" onclick="remove_row_interest_gallery(' + new_row_data + ')" style="cursor:pointer"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
    // onchange="imgTypeCheckUnique(this.id,'+new_row_data+');"
    var rowCount = $('#interestItemTable_gallery tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) 
	{
		$('#interestItemTable_gallery tr:nth-child(' + i + ') td:first').text(i);
	}
}

function remove_row_interest_gallery(row_id) 
{
    var rowCount = $('#interestItemTable_gallery tr').length; // get total rows length
    if (rowCount > 2) 
	{
        $('#interest_tr_4' + row_id).remove();
    }
    var rowCount = $('#interestItemTable_gallery tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) 
	{
        $('#interestItemTable_gallery tr:nth-child(' + i + ') td:first').text(i);
    }
}
</script>
<!------------ Gallery End ------------>

<!------------ REPORTS START ------------>
<script type="text/javascript">
function add_new_row_interest_reports() 
{   //alert("");
    var new_row_data = $('#interest_row_start_reports').val();
    var test12 = $('#test11').val();
    var test12 = parseFloat(test12);
    if (test12) {new_row_data=test12+1;}
    
    var t1= new_row_data-1;
    var fileId = $('#report_file'+t1)[0];
    
    var list_id = $('#list_id').val();
    
    if(list_id != '')
    { 
        if($('#report_file'+t1).length)  // check file  exist or not 
        {
            if(typeof (fileId.files[0]) != "undefined")
            {
                add_reports(new_row_data);
            }
        }
        else
        {
            add_reports(1);
        }
    }
    else
    {
        if(typeof (fileId.files[0]) != "undefined")
        {
            add_reports(new_row_data);
        }
    }
}

function add_reports(new_row_data)
{
    $('#total_row').val(new_row_data);
    $('#interest_row_start_reports').val(parseInt(new_row_data) + 1); 
    // var new_row_data = 4;
    $('#interest_tbody_reports').append('<tr id="interest_tr_4' + new_row_data + '"><td class="center text">' + new_row_data + '</td><td class="prop"><input type="hidden" name="g_sl_rep[]" id="g_sl_rep'+new_row_data+'"/><input type="hidden" class="file form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" id="img_main_image' + new_row_data + '" name="img_main_image"><input type="file" class="file form-control input-sm input-sm-100 rep_file" data-input_id="' + new_row_data + '" name="report_file[]" id="report_file' + new_row_data + '" placeholder=""><div class="image2" id="image2' + new_row_data + '"><span class="error-span"></span></div></td><td class="remove_new_row_reports center" title="Remove  New" onclick="remove_row_interest_reports(' + new_row_data + ')"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
    var rowCount = $('#interestItemTable_reports tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) 
	{
		$('#interestItemTable_reports tr:nth-child(' + i + ') td:first').text(i);
	}
}

function remove_row_interest_reports(row_id) 
{
    var rowCount = $('#interestItemTable_reports tr').length; // get total rows length
    if (rowCount > 2) 
	{
        $('#interest_tr_4' + row_id).remove();
    }
    var rowCount = $('#interestItemTable_reports tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) 
	{
        $('#interestItemTable_reports tr:nth-child(' + i + ') td:first').text(i);
    }
}
</script>
<!------------ REPORTS END ------------>

<!----------- SUMMARY Start ----------->
<script type="text/javascript">
function add_new_row_interest_summary() 
{
    var new_row_data = $('#interest_row_start_summary').val();
    var test12 = $('#test11').val();
    var test12 = parseFloat(test12);
    if (test12) {new_row_data=test12+1;}
    $('#total_row').val(new_row_data);
    $('#interest_row_start_summary').val(parseInt(new_row_data) + 1);
   
    // var new_row_data = 4;
    $('#interest_tbody_summary').append('<tr id="interest_tr_3' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_type" data-input_id="' + new_row_data + '" name="extra_type[]" id="extra_type' + new_row_data + '" placeholder="Extra Type" onchange="SummaryDes(this.value,' + new_row_data + ')"><option value="">-- Select Type --</option><?php foreach($summ_type as $stype){ ?> <option value="<?= $stype->summary_type_id; ?>"><?= $stype->summary_type_name; ?></option> <?php } ?></select><div id="notification'+new_row_data+'" style="color: red;font-style: italic;font-size: 12px;"></div></td><td class="prop"><select class="form-control form-select input-sm input-sm-100 select2 extra_name" data-input_id="' + new_row_data + '" name="extra_name[]" id="extra_name' + new_row_data + '" placeholder="Summary Desc" data-extra_name=""><option value="">-- Summary Desc --</option> </select></td><td class="prop"><input type="text" class="form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" name="extra_name_ar[]" id="extra_name_ar' + new_row_data + '" placeholder="Summary Desc. in Arabic"></td><td class="remove_new_row_summary center" title="Remove New" onclick="remove_row_interest_summary(' + new_row_data + ')" style="cursor:pointer"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
    //$('#interest_tbody_summary').append('<tr id="interest_tr_3' + new_row_data + '"><td class="center">' + new_row_data + '</td><td class="prop"><select class="form-control form-select input-sm input-sm-100 extra_type" data-input_id="' + new_row_data + '" name="extra_type[]" id="extra_type' + new_row_data + '" placeholder="Extra Type" ><option value="">-- Select Type --</option><?php foreach($summ_type as $stype){ ?> <option value="<?= $stype->summary_type_id; ?>"><?= $stype->summary_type_name; ?></option> <?php } ?></select><div id="notification'+new_row_data+'" style="color: red;font-style: italic;font-size: 12px;"></div></td><td class="prop"><select class="form-control form-select input-sm input-sm-100 select2 extra_name" data-input_id="' + new_row_data + '" name="extra_name[]" id="extra_name' + new_row_data + '" placeholder="Extra Desc"><option value="">-- Summary Desc --</option><?php foreach($summ_desc as $desc){ $name = addslashes(htmlspecialchars($desc->sum_desc_name, ENT_QUOTES, 'UTF-8'));?> <option value="<?php echo $name; ?>"><?php echo $name; ?></option> <?php } ?></select></td><td class="prop"><input type="text" class="form-control input-sm input-sm-100" data-input_id="' + new_row_data + '" name="extra_name_ar[]" id="extra_name_ar' + new_row_data + '" placeholder="Summary Desc. in Arabic"></td><td class="remove_new_row_summary center" title="Remove  New" onclick="remove_row_interest_summary(' + new_row_data + ')"><i class="fa fa-times" aria-hidden="true"></i></td></tr>');
	
	// Re-initialize Select2 only on the new select element
	$('#extra_name' + new_row_data).select2({
		tags: true,
		placeholder: "Summary Desc",
		allowClear: true,
		width: '100%' // make sure it expands properly
	});
	
    var rowCount = $('#interestItemTable_summary tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) {
        $('#interestItemTable_summary tr:nth-child(' + i + ') td:first').text(i);
    }
}

function remove_row_interest_summary(row_id) 
{
    var rowCount = $('#interestItemTable_summary tr').length; // get total rows length
    if (rowCount > 2) {
        $('#interest_tr_3' + row_id).remove();
    }
    var rowCount = $('#interestItemTable_summary tr').length; // get total rows length
    for (var i = 1; i < rowCount; i++) {
        $('#interestItemTable_summary tr:nth-child(' + i + ') td:first').text(i);
    }
}
</script>

<script>
$(document).ready(function() {
  $('.select2.extra_name').select2({
    tags: true,
    placeholder: "Summary Desc",
    allowClear: true,
    width: '100%' // make sure it expands properly
  });
});
</script>

<!--------- Summary Description --------->
<script type="text/javascript">
	function SummaryDes(SummaryType,row)
    { 
        if(SummaryType)
        { 
            $.ajax({
                type: 'GET',
                url: 'inspectionreport/SummaryDes',
                data: { SummaryType: SummaryType },
				success: function(res) 
				{
					const $select = $('#extra_name' + row);
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
</script>
<!-------------- SUMMARY END -------------->

<!-------------- To set DAMAGE marking image in canvas -------------->
<script type="text/javascript">
    var canvas = document.getElementById("canvas"),
    ctx = canvas.getContext("2d");

    canvas.width  = 520; // 512;
    canvas.height = 440; // 512; 

    var background = new Image();
    //background.src = "https://auto-assure.com/crm/assets/images/newcar-08022025.png";
    background.src = "https://auto-assure.com/crm/assets/images/newcar-08022025.png";

    // Make sure the image is loaded first otherwise nothing will draw.
    background.onload = function(){
    ctx.drawImage(background,0,0);   
}
</script>
<!-------------- To set DAMAGE marking image in canvas end -------------->
<!-------------- DAMAGES MARK START -------------->
<script type="text/javascript">
var fileUpload = document.getElementById('fileUpload');
var canvas  = document.getElementById('canvas');   
var ctx = canvas.getContext("2d");   

/******** 26/12/24
function readImage() {
    if ( this.files && this.files[0] ) {
        var FR= new FileReader();
        FR.onload = function(e) {
           var img = new Image();
           img.src = e.target.result;
           img.onload = function() {
             ctx.drawImage(img, 0, 0, 512, 512);
           };
        };       
        FR.readAsDataURL( this.files[0] );
    }
}

fileUpload.onchange = readImage;  26/12/24 ********/


/*
canvas.onclick = function(e) 
{
  var x = e.offsetX;
  var y = e.offsetY;
  ctx.beginPath();
  ctx.fillStyle = 'red';
  ctx.arc(x, y, 5, 0, Math.PI * 2);
  ctx.fill();
};*/

var orange  = document.getElementById('orange');
orange.onclick = function(e) 
{  
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'orange';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}

var blue  = document.getElementById('blue');
blue.onclick = function(e) 
{   
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'blue';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}

var yellow  = document.getElementById('yellow');
yellow.onclick = function(e) 
{   
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'yellow';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}

var red  = document.getElementById('red');
red.onclick = function(e) 
{  
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'red';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}

var black  = document.getElementById('black');
black.onclick = function(e) 
{   
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'black';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}

var green  = document.getElementById('green');
green.onclick = function(e) 
{   
	canvas.onclick = function(e) 
	{
		var x = e.offsetX;
		var y = e.offsetY;
		ctx.beginPath();
		ctx.fillStyle = 'green';
		ctx.arc(x, y, 5, 0, Math.PI * 2);
		ctx.fill();
	};
}
</script>

<!------------ Insert Damage Image ------------>
<script> 
$('#quotetbn').on('click',function()
{    
	var canvas = document.getElementById("canvas");
	var editid = document.getElementsByClassName("edit_id")[0].value;  
 
	//var pngUrl = canvas.toDataURL("https://auto-assure.com/crm/uploads/inspectionreport/damages");       
	var pngUrl = canvas.toDataURL("https://auto-assure.com/crm/uploads/inspectionreport/damages");       
	//var newTab = window.open('about:blank','image from canvas');
	//newTab.document.write("<img src='" + pngUrl + "' alt='from canvas'/>");
 
	$.ajax({
		dataType: 'json',
		type: "POST",
		url : url_addDamages,
		data: {'pngUrl' : pngUrl,'_token':token,'editid':editid},  
		success:function(data) 
        {
			if(data.status == 1)
			{
				 Command: toastr["success"](data.msg)
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
				//$('#inspectionReportDataTable').DataTable().ajax.reload();
				window.location.reload();
			}
		}
	});
});
  
</script>
<!--------------- DAMAGES END --------------->

<!----------- Check unique select for addnew ----------->
<script>
function checkUnique(elementID,rowID) 
{
	var current_val  = $("#"+elementID).val();   
    var current_text = $("#"+elementID+" option:selected").text();
	
	var rowVal = 0;
	 
	$('#interest_tbody_summary tr').each(function(index) 
	{		   
		var row  = $(this).find('.extra_type').val();  
		var data = $(this).find('.extra_type').data('input_id');   
		
		if(current_val == row && rowID != data)  
		{
			rowVal = 1;
			return false;
		}			
	});
	 
	if(rowVal == 1)
	{
		$("#notification"+rowID).text(current_text+" Type Already Choosen! ");
	}
	else
	{
		$("#notification"+rowID).text('');
	}
	 
}
</script>

<script>
function imgTypeCheckUnique(elementID,rowID) 
{
	var current_val  = $("#"+elementID).val();   
    var current_text = $("#"+elementID+" option:selected").text();   
	
	var rowVal = 0;
	 
	$('#interest_tbody_gallery tr').each(function(index) 
	{		   
		var row  = $(this).find('.gallery_image_type').val();  
		var data = $(this).find('.gallery_image_type').data('input_id');   
		
		if(current_val == row && rowID != data)  
		{ 
			rowVal = 1;
			return false;
		}			
	});
	  
	if(rowVal == 1)
	{   
		$("#galNotification"+rowID).text(current_text+" Type Already Choosen! ");
	}
	else
	{
		$("#galNotification"+rowID).text('');
	}
	 
}
</script>
<!----------- Check unique select for addnew end ----------->

<script>
function download() 
{    
    var dt = canvas.toDataURL('image/jpeg');  
    this.href = dt;   
	//$('#hidden').val(this.href);
	//$("#hidden").html('<img src="'+dt+'" width="300px" height="450px" />');	
};
//downloadLnk.addEventListener('click', download, false);   
</script>
 
<!--------------- TAB START --------------->
<script>
 
$('#home-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").addClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "");  // To hide
	$("#vehicle").css("display", "none");  // To unhide
});

$('#vehicle-tab').click(function()
{
	$("#vehicle-tab").addClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "");  // To unhide
});


$('#spec-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	$("#spec-tab").addClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#spec").css("display", "");  // To unhide
});


$('#warranty-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").addClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To unhide
	$("#warranty").css("display", "");  // To unhide
});
 
$('#gallery-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").addClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "");  // To unhide
});

$('#reports-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").addClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#reports").css("display", "");  // To unhide
});

$('#specification-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").addClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To
	$("#specification").css("display", "");  // To unhide
});

$('#checklist-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").addClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#checklist").css("display", "");  // To unhide
});

$('#summary-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").addClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#summary").css("display", "");  // To unhide
	$("#overview").css("display", "none");  // To unhide
});

$('#overview-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").removeClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").addClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#damages").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To unhide
	$("#overview").css("display", "");  // To unhide
});

$('#damages-tab').click(function()
{
	$("#vehicle-tab").removeClass("show active");
	$("#reports-tab").removeClass("show active");
	$("#damages-tab").addClass("show active");
	$("#summary-tab").removeClass("show active");
	$("#overview-tab").removeClass("show active");
	$("#checklist-tab").removeClass("show active");
	$("#specification-tab").removeClass("show active");
	$("#warranty-tab").removeClass("show active");
	$("#gallery-tab").removeClass("show active");
	$("#home-tab").removeClass("show active");
	
	$("#reports").css("display", "none");  // To hide
	$("#summary").css("display", "none");  // To hide
	$("#overview").css("display", "none");  // To hide
	$("#warranty").css("display", "none");  // To hide
	$("#spec").css("display", "none");  // To hide
	$("#home").css("display", "none");  // To hide
	$("#vehicle").css("display", "none");  // To hide
	$("#gallery").css("display", "none");  // To hide
	$("#specification").css("display", "none");  // To hide
	$("#checklist").css("display", "none");  // To hide
	$("#damages").css("display", "");  // To unhide
});
 
</script>
<!--------------- TAB END --------------->

<script>
/********** VEHICLE SPECIFICATION START **********/
$('.air_suspension').click(function()
{
	var cmnt = $("input[type='radio'][name='air_suspension']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#air_suspension_cmnt").css("display", "");
	}
	else
	{
		$("#air_suspension_cmnt").css("display", "none");
	}
});

$('.adaptive_air_suspension').click(function()
{
	var cmnt = $("input[type='radio'][name='adaptive_air_suspension']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#adaptive_air_suspension_cmnt").css("display", "");
	}
	else
	{
		$("#adaptive_air_suspension_cmnt").css("display", "none");
	}
});

$('.differential_lock').click(function()
{
	var cmnt = $("input[type='radio'][name='differential_lock']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#differential_lock_cmnt").css("display", "");
	}
	else
	{
		$("#differential_lock_cmnt").css("display", "none");
	}
});

$('.paddle_shifters').click(function()
{
	var cmnt = $("input[type='radio'][name='paddle_shifters']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#paddle_shifters_cmnt").css("display", "");
	}
	else
	{
		$("#adaptive_air_suspension_cmnt").css("display", "none");
	}
});

$('.tiptronic').click(function()
{
	var cmnt = $("input[type='radio'][name='tiptronic']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#tiptronic_cmnt").css("display", "");
	}
	else
	{
		$("#tiptronic_cmnt").css("display", "none");
	}
});

$('.hill_descent_assist').click(function()
{
	var cmnt = $("input[type='radio'][name='hill_descent_assist']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#hill_descent_assist_cmnt").css("display", "");
	}
	else
	{
		$("#hill_descent_assist_cmnt").css("display", "none");
	}
});

$('.hill_start_assist').click(function()
{
	var cmnt = $("input[type='radio'][name='hill_start_assist']:checked").val();  //alert(cmnt);
	if(cmnt == 2)
	{
		$("#hill_start_assist_cmnt").css("display", "");
	}
	else
	{
		$("#hill_start_assist_cmnt").css("display", "none");
	}
});

$('.launch_control').click(function()
{
	var cmnt = $("input[type='radio'][name='hill_start_assist']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#launch_control_cmnt").css("display", "");
	}
	else
	{
		$("#launch_control_cmnt").css("display", "none");
	}
	
});

$('#child_safety_seats2').click(function()
{
	$("#child_safety_seats_cmnt").css("display", "");
});

$('#rear_parking_sensors2').click(function()
{
	$("#rear_parking_sensors_cmnt").css("display", "");
});

$('.auto_hold').click(function()
{
	var cmnt = $("input[type='radio'][name='auto_hold']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#auto_hold_cmnt").css("display", "");
	}
	else
	{
		$("#auto_hold_cmnt").css("display", "none");
	}
});

$('.comfort_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='comfort_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#comfort_seats_cmnt").css("display", "");
	}
	else
	{
		$("#comfort_seats_cmnt").css("display", "none");
	}
}); 

$('.sport_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='sport_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sport_seats_cmnt").css("display", "");
	}
	else
	{
		$("#sport_seats_cmnt").css("display", "none");
	}
});

$('.sport_brakes').click(function()
{
	var cmnt = $("input[type='radio'][name='sport_brakes']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sport_brakes_cmnt").css("display", "");
	}
	else
	{
		$("#sport_brakes_cmnt").css("display", "none");
	}
});

$('.sport_suspension').click(function()
{
	var cmnt = $("input[type='radio'][name='sport_suspension']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sport_suspension_cmnt").css("display", "");
	}
	else
	{
		$("#sport_suspension_cmnt").css("display", "none");
	}
});

$('.sport_exhaust').click(function()
{
	var cmnt = $("input[type='radio'][name='sport_exhaust']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sport_exhaust_cmnt").css("display", "");
	}
	else
	{
		$("#sport_exhaust_cmnt").css("display", "none");
	}
});

$('.lane_change').click(function()
{
	var cmnt = $("input[type='radio'][name='lane_change']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#lane_change_cmnt").css("display", "");
	}
	else
	{
		$("#lane_change_cmnt").css("display", "none");
	}
});

$('.launch_control').click(function()
{
	var cmnt = $("input[type='radio'][name='launch_control']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#launch_control_cmnt").css("display", "");
	}
	else
	{
		$("#launch_control_cmnt").css("display", "none");
	}
});

$('.child_safety_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='child_safety_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#child_safety_seats_cmnt").css("display", "");
	}
	else
	{
		$("#child_safety_seats_cmnt").css("display", "none");
	}
});
$('.front_view_camera').click(function()
{
	var cmnt = $("input[type='radio'][name='front_view_camera']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#front_view_camera_cmnt").css("display", "");
	}
	else
	{
		$("#front_view_camera_cmnt").css("display", "none");
	}
});
$('.rear_view_camera').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_view_camera']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_view_camera_cmnt").css("display", "");
	}
	else
	{
		$("#rear_view_camera_cmnt").css("display", "none");
	}
});

$('.degree_camera').click(function()
{
	var cmnt = $("input[type='radio'][name='degree_camera']:checked").val();  
	if(cmnt == 2)
	{
		$("#degree_camera_cmnt").css("display", "");
	}
	else
	{
		$("#degree_camera_cmnt").css("display", "none");
	}
});

$('.front_parking_sensors').click(function()
{
	var cmnt = $("input[type='radio'][name='front_parking_sensors']:checked").val(); 
	if(cmnt == 2)
	{
		$("#front_parking_sensors_cmnt").css("display", "");
	}
	else
	{
		$("#front_parking_sensors_cmnt").css("display", "none");
	}
});

$('.rear_parking_sensors').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_parking_sensors']:checked").val();  
	if(cmnt == 2)
	{
		$("#rear_parking_sensors_cmnt").css("display", "");
	}
	else
	{
		$("#rear_parking_sensors_cmnt").css("display", "none");
	}
});

$('.lane_departure').click(function()
{
	var cmnt = $("input[type='radio'][name='lane_departure']:checked").val();  
	if(cmnt == 2)
	{
		$("#lane_departure_cmnt").css("display", "");
	}
	else
	{
		$("#lane_departure_cmnt").css("display", "none");
	}
});  

$('.anti_lock_brakes').click(function()
{
	var cmnt = $("input[type='radio'][name='anti_lock_brakes']:checked").val();  
	if(cmnt == 2)
	{
		$("#anti_lock_brakes_cmnt").css("display", "");
	}
	else
	{
		$("#anti_lock_brakes_cmnt").css("display", "none");
	}
});

$('.anti_lock_brakes').click(function()
{
	var cmnt = $("input[type='radio'][name='anti_lock_brakes']:checked").val(); 
	if(cmnt == 2)
	{
		$("#anti_lock_brakes_cmnt").css("display", "");
	}
	else
	{
		$("#anti_lock_brakes_cmnt").css("display", "none");
	}
});  

$('.ebd').click(function()
{
	var cmnt = $("input[type='radio'][name='ebd']:checked").val();  
	if(cmnt == 2)
	{
		$("#ebd_cmnt").css("display", "");
	}
	else
	{
		$("#ebd_cmnt").css("display", "none");
	}
});  
 
$('.alarm').click(function()
{
	var cmnt = $("input[type='radio'][name='alarm']:checked").val();  
	if(cmnt == 2)
	{
		$("#alarm_cmnt").css("display", "");
	}
	else
	{
		$("#alarm_cmnt").css("display", "none");
	}
});

$('.front_airbags').click(function()
{
	var cmnt = $("input[type='radio'][name='front_airbags']:checked").val();  
	if(cmnt == 2)
	{
		$("#front_airbags_cmnt").css("display", "");
	}
	else
	{
		$("#front_airbags_cmnt").css("display", "none");
	}
});


$('.side_airbags').click(function()
{
	var cmnt = $("input[type='radio'][name='side_airbags']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#side_airbags_cmnt").css("display", "");
	}
	else
	{
		$("#side_airbags_cmnt").css("display", "none");
	}
});

$('.traction_control_sys').click(function()
{
	var cmnt = $("input[type='radio'][name='traction_control_sys']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#traction_control_sys_cmnt").css("display", "");
	}
	else
	{
		$("#traction_control_sys_cmnt").css("display", "none");
	}
});

$('.park_assist').click(function()
{
	var cmnt = $("input[type='radio'][name='park_assist']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#park_assist_cmnt").css("display", "");
	}
	else
	{
		$("#park_assist_cmnt").css("display", "none");
	}
});

$('.blind_spot_monitor').click(function()
{
	var cmnt = $("input[type='radio'][name='blind_spot_monitor']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#blind_spot_monitor_cmnt").css("display", "");
	}
	else
	{
		$("#blind_spot_monitor_cmnt").css("display", "none");
	}
});

$('.tire_pressure_monitor').click(function()
{
	var cmnt = $("input[type='radio'][name='tire_pressure_monitor']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#tire_pressure_monitor_cmnt").css("display", "");
	}
	else
	{
		$("#tire_pressure_monitor_cmnt").css("display", "none");
	}
});

$('.anti_glare_rear_view').click(function()
{
	var cmnt = $("input[type='radio'][name='anti_glare_rear_view']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#anti_glare_rear_view_cmnt").css("display", "");
	}
	else
	{
		$("#anti_glare_rear_view_cmnt").css("display", "none");
	}
});

$('.digital_driver_display').click(function()
{
	var cmnt = $("input[type='radio'][name='digital_driver_display']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#digital_driver_display_cmnt").css("display", "");
	}
	else
	{
		$("#digital_driver_display_cmnt").css("display", "none");
	}
});

$('.cd_player').click(function()
{
	var cmnt = $("input[type='radio'][name='cd_player']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#cd_player_cmnt").css("display", "");
	}
	else
	{
		$("#cd_player_cmnt").css("display", "none");
	}
});

$('.dvd_player').click(function()
{
	var cmnt = $("input[type='radio'][name='dvd_player']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#dvd_player_cmnt").css("display", "");
	}
	else
	{
		$("#dvd_player_cmnt").css("display", "none");
	}
});

$('.mp_player').click(function()
{
	var cmnt = $("input[type='radio'][name='mp_player']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#mp_player_cmnt").css("display", "");
	}
	else
	{
		$("#mp_player_cmnt").css("display", "none");
	}
});

$('.sd_card_player').click(function()
{
	var cmnt = $("input[type='radio'][name='sd_card_player']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sd_card_player_cmnt").css("display", "");
	}
	else
	{
		$("#sd_card_player_cmnt").css("display", "none");
	}
});

$('.bluetooth_interface').click(function()
{
	var cmnt = $("input[type='radio'][name='bluetooth_interface']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#bluetooth_interface_cmnt").css("display", "");
	}
	else
	{
		$("#bluetooth_interface_cmnt").css("display", "none");
	}
});

$('.premium_sound_system').click(function()
{
	var cmnt = $("input[type='radio'][name='premium_sound_system']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#premium_sound_system_cmnt").css("display", "");
	}
	else
	{
		$("#premium_sound_system_cmnt").css("display", "none");
	}
});

$('.aux_audio_system').click(function()
{
	var cmnt = $("input[type='radio'][name='aux_audio_system']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#aux_audio_system_cmnt").css("display", "");
	}
	else
	{
		$("#aux_audio_system_cmnt").css("display", "none");
	}
});

$('.usb').click(function()
{
	var cmnt = $("input[type='radio'][name='usb']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#usb_cmnt").css("display", "");
	}
	else
	{
		$("#usb_cmnt").css("display", "none");
	}
});

$('.usb_c').click(function()
{
	var cmnt = $("input[type='radio'][name='usb_c']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#usb_c_cmnt").css("display", "");
	}
	else
	{
		$("#usb_c_cmnt").css("display", "none");
	}
});

$('.touch_screen').click(function()
{
	var cmnt = $("input[type='radio'][name='touch_screen']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#touch_screen_cmnt").css("display", "");
	}
	else
	{
		$("#touch_screen_cmnt").css("display", "none");
	}
});

$('.rear_seat_enter_sys').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_seat_enter_sys']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_seat_enter_sys_cmnt").css("display", "");
	}
	else
	{
		$("#rear_seat_enter_sys_cmnt").css("display", "none");
	}
});

$('.wireless').click(function()
{
	var cmnt = $("input[type='radio'][name='wireless']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#wireless_cmnt").css("display", "");
	}
	else
	{
		$("#wireless_cmnt").css("display", "none");
	}
});

$('.ambient_lighting').click(function()
{
	var cmnt = $("input[type='radio'][name='ambient_lighting']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#ambient_lighting_cmnt").css("display", "");
	}
	else
	{
		$("#ambient_lighting_cmnt").css("display", "none");
	}
});

$('.apple_carplay').click(function()
{
	var cmnt = $("input[type='radio'][name='apple_carplay']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#apple_carplay_cmnt").css("display", "");
	}
	else
	{
		$("#apple_carplay_cmnt").css("display", "none");
	}
});

$('.navigation').click(function()
{
	var cmnt = $("input[type='radio'][name='navigation']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#navigation_cmnt").css("display", "");
	}
	else
	{
		$("#navigation_cmnt").css("display", "none");
	}
});

$('.standard_ac').click(function()
{
	var cmnt = $("input[type='radio'][name='standard_ac']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#standard_ac_cmnt").css("display", "");
	}
	else
	{
		$("#standard_ac_cmnt").css("display", "none");
	}
});

$('.dual_climcont_ac').click(function()
{
	var cmnt = $("input[type='radio'][name='dual_climcont_ac']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#dual_climcont_ac_cmnt").css("display", "");
	}
	else
	{
		$("#dual_climcont_ac_cmnt").css("display", "none");
	}
});

$('.multi_climcont_ac').click(function()
{
	var cmnt = $("input[type='radio'][name='multi_climcont_ac']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#multi_climcont_ac_cmnt").css("display", "");
	}
	else
	{
		$("#multi_climcont_ac_cmnt").css("display", "none");
	}
});

$('.keyless_entry').click(function()
{
	var cmnt = $("input[type='radio'][name='keyless_entry']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#keyless_entry_cmnt").css("display", "");
	}
	else
	{
		$("#keyless_entry_cmnt").css("display", "none");
	}
});

$('.keyless_start').click(function()
{
	var cmnt = $("input[type='radio'][name='keyless_start']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#keyless_start_cmnt").css("display", "");
	}
	else
	{
		$("#keyless_start_cmnt").css("display", "none");
	}
});

$('.power_steering').click(function()
{
	var cmnt = $("input[type='radio'][name='power_steering']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_steering_cmnt").css("display", "");
	}
	else
	{
		$("#power_steering_cmnt").css("display", "none");
	}
});

$('.heads_up_display').click(function()
{
	var cmnt = $("input[type='radio'][name='heads_up_display']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#heads_up_display_cmnt").css("display", "");
	}
	else
	{
		$("#heads_up_display_cmnt").css("display", "none");
	}
});

$('.cruise_control').click(function()
{
	var cmnt = $("input[type='radio'][name='cruise_control']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#cruise_control_cmnt").css("display", "");
	}
	else
	{
		$("#cruise_control_cmnt").css("display", "none");
	}
});

$('.adaptive_cruise_control').click(function()
{
	var cmnt = $("input[type='radio'][name='adaptive_cruise_control']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#adaptive_cruise_control_cmnt").css("display", "");
	}
	else
	{
		$("#adaptive_cruise_control_cmnt").css("display", "none");
	}
});

$('.seat_cooling_front').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_cooling_front']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_cooling_front_cmnt").css("display", "");
	}
	else
	{
		$("#seat_cooling_front_cmnt").css("display", "none");
	}
});

$('.seat_cooling_rear').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_cooling_rear']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_cooling_rear_cmnt").css("display", "");
	}
	else
	{
		$("#seat_cooling_rear_cmnt").css("display", "none");
	}
});

$('.seat_massage_front').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_massage_front']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_massage_front_cmnt").css("display", "");
	}
	else
	{
		$("#seat_massage_front_cmnt").css("display", "none");
	}
});

$('.seat_massage_rear').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_massage_rear']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_massage_rear_cmnt").css("display", "");
	}
	else
	{
		$("#seat_massage_rear_cmnt").css("display", "none");
	}
});

$('.driver_memory_seat').click(function()
{
	var cmnt = $("input[type='radio'][name='driver_memory_seat']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#driver_memory_seat_cmnt").css("display", "");
	}
	else
	{
		$("#driver_memory_seat_cmnt").css("display", "none");
	}
});

$('.passenger_memory_seat').click(function()
{
	var cmnt = $("input[type='radio'][name='passenger_memory_seat']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#passenger_memory_seat_cmnt").css("display", "");
	}
	else
	{
		$("#passenger_memory_seat_cmnt").css("display", "none");
	}
});

$('.power_driver_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='power_driver_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_driver_seats_cmnt").css("display", "");
	}
	else
	{
		$("#power_driver_seats_cmnt").css("display", "none");
	}
});

$('.power_passenger_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='power_passenger_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_passenger_seats_cmnt").css("display", "");
	}
	else
	{
		$("#power_passenger_seats_cmnt").css("display", "none");
	}
});

$('.power_rear_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='power_rear_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_rear_seats_cmnt").css("display", "");
	}
	else
	{
		$("#power_rear_seats_cmnt").css("display", "none");
	}
});

$('.power_front_windows').click(function()
{
	var cmnt = $("input[type='radio'][name='power_front_windows']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_front_windows_cmnt").css("display", "");
	}
	else
	{
		$("#power_front_windows_cmnt").css("display", "none");
	}
});

$('.power_rear_windows').click(function()
{
	var cmnt = $("input[type='radio'][name='power_rear_windows']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_rear_windows_cmnt").css("display", "");
	}
	else
	{
		$("#power_rear_windows_cmnt").css("display", "none");
	}
});

$('.power_trunk').click(function()
{
	var cmnt = $("input[type='radio'][name='power_trunk']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_trunk_cmnt").css("display", "");
	}
	else
	{
		$("#power_trunk_cmnt").css("display", "none");
	}
});

$('.power_locks').click(function()
{
	var cmnt = $("input[type='radio'][name='power_locks']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_locks_cmnt").css("display", "");
	}
	else
	{
		$("#power_locks_cmnt").css("display", "none");
	}
});

$('.power_mirrors').click(function()
{
	var cmnt = $("input[type='radio'][name='power_mirrors']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_mirrors_cmnt").css("display", "");
	}
	else
	{
		$("#power_mirrors_cmnt").css("display", "none");
	}
});

$('.power_folding_mirrors').click(function()
{
	var cmnt = $("input[type='radio'][name='power_folding_mirrors']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#power_folding_mirrors_cmnt").css("display", "");
	}
	else
	{
		$("#power_folding_mirrors_cmnt").css("display", "none");
	}
});

$('.sun_roof').click(function()
{
	var cmnt = $("input[type='radio'][name='sun_roof']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#sun_roof_cmnt").css("display", "");
	}
	else
	{
		$("#sun_roof_cmnt").css("display", "none");
	}
});

$('.panoramic_roof').click(function()
{
	var cmnt = $("input[type='radio'][name='panoramic_roof']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#panoramic_roof_cmnt").css("display", "");
	}
	else
	{
		$("#panoramic_roof_cmnt").css("display", "none");
	}
});

$('.cool_box').click(function()
{
	var cmnt = $("input[type='radio'][name='cool_box']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#cool_box_cmnt").css("display", "");
	}
	else
	{
		$("#cool_box_cmnt").css("display", "none");
	}
});

$('.seat_heated_front').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_heated_front']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_heated_front_cmnt").css("display", "");
	}
	else
	{
		$("#seat_heated_front_cmnt").css("display", "none");
	}
});

$('.auto_park').click(function()
{
	var cmnt = $("input[type='radio'][name='auto_park']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#auto_park_cmnt").css("display", "");
	}
	else
	{
		$("#auto_park_cmnt").css("display", "none");
	}
});

$('.remote_start_engine').click(function()
{
	var cmnt = $("input[type='radio'][name='remote_start_engine']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#remote_start_engine_cmnt").css("display", "");
	}
	else
	{
		$("#remote_start_engine_cmnt").css("display", "none");
	}
});

$('.soft_close_doors').click(function()
{
	var cmnt = $("input[type='radio'][name='soft_close_doors']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#soft_close_doors_cmnt").css("display", "");
	}
	else
	{
		$("#soft_close_doors_cmnt").css("display", "none");
	}
});

$('.adaptive_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='adaptive_lights']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#adaptive_lights_cmnt").css("display", "");
	}
	else
	{
		$("#adaptive_lights_cmnt").css("display", "none");
	}
});

$('.night_vision').click(function()
{
	var cmnt = $("input[type='radio'][name='night_vision']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#night_vision_cmnt").css("display", "");
	}
	else
	{
		$("#night_vision_cmnt").css("display", "none");
	}
});

$('.captain_rear_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='captain_rear_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#captain_rear_seats_cmnt").css("display", "");
	}
	else
	{
		$("#captain_rear_seats_cmnt").css("display", "none");
	}
});

$('.leather_seats').click(function()
{
	var cmnt = $("input[type='radio'][name='leather_seats']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#leather_seats_cmnt").css("display", "");
	}
	else
	{
		$("#leather_seats_cmnt").css("display", "none");
	}
});

$('.leather_fabric').click(function()
{
	var cmnt = $("input[type='radio'][name='leather_fabric']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#leather_fabric_cmnt").css("display", "");
	}
	else
	{
		$("#leather_fabric_cmnt").css("display", "none");
	}
});

$('.body_kit').click(function()
{
	var cmnt = $("input[type='radio'][name='body_kit']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#body_kit_cmnt").css("display", "");
	}
	else
	{
		$("#body_kit_cmnt").css("display", "none");
	}
});

$('.lift_kit').click(function()
{
	var cmnt = $("input[type='radio'][name='lift_kit']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#lift_kit_cmnt").css("display", "");
	}
	else
	{
		$("#lift_kit_cmnt").css("display", "none");
	}
});

$('.front_spoiler').click(function()
{
	var cmnt = $("input[type='radio'][name='front_spoiler']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#front_spoiler_cmnt").css("display", "");
	}
	else
	{
		$("#front_spoiler_cmnt").css("display", "none");
	}
});

$('.rear_spoiler').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_spoiler']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_spoiler_cmnt").css("display", "");
	}
	else
	{
		$("#rear_spoiler_cmnt").css("display", "none");
	}
});

$('.fog_light_front').click(function()
{
	var cmnt = $("input[type='radio'][name='fog_light_front']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#fog_light_front_cmnt").css("display", "");
	}
	else
	{
		$("#fog_light_front_cmnt").css("display", "none");
	}
});

$('.roof_carrier').click(function()
{
	var cmnt = $("input[type='radio'][name='roof_carrier']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#roof_carrier_cmnt").css("display", "");
	}
	else
	{
		$("#roof_carrier_cmnt").css("display", "none");
	}
});

$('.halogen_headlight').click(function()
{
	var cmnt = $("input[type='radio'][name='halogen_headlight']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#halogen_headlight_cmnt").css("display", "");
	}
	else
	{
		$("#halogen_headlight_cmnt").css("display", "none");
	}
});

$('.led_headlight').click(function()
{
	var cmnt = $("input[type='radio'][name='led_headlight']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#led_headlight_cmnt").css("display", "");
	}
	else
	{
		$("#led_headlight_cmnt").css("display", "none");
	}
});

$('.xenon_headlight').click(function()
{
	var cmnt = $("input[type='radio'][name='xenon_headlight']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#xenon_headlight_cmnt").css("display", "");
	}
	else
	{
		$("#xenon_headlight_cmnt").css("display", "none");
	}
});

$('.trailer_hook_coupling').click(function()
{
	var cmnt = $("input[type='radio'][name='trailer_hook_coupling']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#trailer_hook_coupling_cmnt").css("display", "");
	}
	else
	{
		$("#trailer_hook_coupling_cmnt").css("display", "none");
	}
});

$('.winch').click(function()
{
	var cmnt = $("input[type='radio'][name='winch']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#winch_cmnt").css("display", "");
	}
	else
	{
		$("#winch_cmnt").css("display", "none");
	}
});

$('.body_kit_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='body_kit_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#body_kit_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#body_kit_aaa_cmnt").css("display", "none");
	}
});

$('.lift_kit_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='lift_kit_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#lift_kit_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#lift_kit_aaa_cmnt").css("display", "none");
	}
});
 
$('.leather_seats_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='leather_seats_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#leather_seats_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#leather_seats_aaa_cmnt").css("display", "none");
	}
});

$('.rear_seat_enter_sys_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_seat_enter_sys_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_seat_enter_sys_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#rear_seat_enter_sys_aaa_cmnt").css("display", "none");
	}
});

$('.parking_sensors').click(function()
{
	var cmnt = $("input[type='radio'][name='parking_sensors']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#parking_sensors_cmnt").css("display", "");
	}
	else
	{
		$("#parking_sensors_cmnt").css("display", "none");
	}
});

$('.rear_view_camera_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_view_camera_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_view_camera_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#rear_view_camera_aaa_cmnt").css("display", "none");
	}
});

$('.navigation_aaa').click(function()
{
	var cmnt = $("input[type='radio'][name='navigation_aaa']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#navigation_aaa_cmnt").css("display", "");
	}
	else
	{
		$("#navigation_aaa_cmnt").css("display", "none");
	}
});

$('.fire_extinguisher').click(function()
{
	var cmnt = $("input[type='radio'][name='fire_extinguisher']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#fire_extinguisher_cmnt").css("display", "");
	}
	else
	{
		$("#fire_extinguisher_cmnt").css("display", "none");
	}
});
/********** VEHICLE SPECIFICATION END **********/

/********** INSPECTION CHECKLIST START **********/
<!-- Exterior -->
$('.fuel_filler_cover_petrol').click(function()
{
	var cmnt = $("input[type='radio'][name='fuel_filler_cover_petrol']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#fuel_filler_cover_petrol_cmnt").css("display", "");
	}
	else
	{
		$("#fuel_filler_cover_petrol_cmnt").css("display", "none");
	}
});

$('.door_locks_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='door_locks_operation']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#door_locks_operation_cmnt").css("display", "");
	}
	else
	{
		$("#door_locks_operation_cmnt").css("display", "none");
	}
});

$('.glass').click(function()
{
	var cmnt = $("input[type='radio'][name='glass']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#glass_cmnt").css("display", "");
	}
	else
	{
		$("#glass_cmnt").css("display", "none");
	}
});

$('.molding').click(function()
{
	var cmnt = $("input[type='radio'][name='molding']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#molding_cmnt").css("display", "");
	}
	else
	{
		$("#molding_cmnt").css("display", "none");
	}
});

$('.bumper_grills').click(function()
{
	var cmnt = $("input[type='radio'][name='bumper_grills']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#bumper_grills_cmnt").css("display", "");
	}
	else
	{
		$("#bumper_grills_cmnt").css("display", "none");
	}
});

$('.front_bumper').click(function()
{
	var cmnt = $("input[type='radio'][name='front_bumper']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#front_bumper_cmnt").css("display", "");
	}
	else
	{
		$("#front_bumper_cmnt").css("display", "none");
	}
});

$('.rear_bumper').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_bumper']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_bumper_cmnt").css("display", "");
	}
	else
	{
		$("#rear_bumper_cmnt").css("display", "none");
	}
});

$('.front_left_headlights').click(function()
{
	var cmnt = $("input[type='radio'][name='front_left_headlights']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#front_left_headlights_cmnt").css("display", "");
	}
	else
	{
		$("#front_left_headlights_cmnt").css("display", "none");
	}
});

$('.front_right_headlights').click(function()
{
	var cmnt = $("input[type='radio'][name='front_right_headlights']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#front_right_headlights_cmnt").css("display", "");
	}
	else
	{
		$("#front_right_headlights_cmnt").css("display", "none");
	}
});

$('.rear_left_tail_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_left_tail_lights']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_left_tail_lights_cmnt").css("display", "");
	}
	else
	{
		$("#rear_left_tail_lights_cmnt").css("display", "none");
	}
});

$('.rear_right_tail_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_right_tail_lights']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rear_right_tail_lights_cmnt").css("display", "");
	}
	else
	{
		$("#rear_right_tail_lights_cmnt").css("display", "none");
	}
});

$('.general_body_condition').click(function()
{
	var cmnt = $("input[type='radio'][name='general_body_condition']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#general_body_condition_cmnt").css("display", "");
	}
	else
	{
		$("#general_body_condition_cmnt").css("display", "none");
	}
});

<!-- Interior Start -->
$('.seat_belts').click(function()
{
	var cmnt = $("input[type='radio'][name='seat_belts']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#seat_belts_cmnt").css("display", "");
	}
	else
	{
		$("#seat_belts_cmnt").css("display", "none");
	}
});

$('.headliner').click(function()
{
	var cmnt = $("input[type='radio'][name='headliner']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#headliner_cmnt").css("display", "");
	}
	else
	{
		$("#headliner_cmnt").css("display", "none");
	}
});

$('.rearview_mirror').click(function()
{
	var cmnt = $("input[type='radio'][name='rearview_mirror']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#rearview_mirror_cmnt").css("display", "");
	}
	else
	{
		$("#rearview_mirror_cmnt").css("display", "none");
	}
});

$('.steering_wheel').click(function()
{
	var cmnt = $("input[type='radio'][name='steering_wheel']:checked").val(); //alert(cmnt);
	if(cmnt == 2)
	{
		$("#steering_wheel_cmnt").css("display", "");
	}
	else
	{
		$("#steering_wheel_cmnt").css("display", "none");
	}
});

$('.gear_lever').click(function()
{
	var cmnt = $("input[type='radio'][name='gear_lever']:checked").val();  
	if(cmnt == 2)
	{
		$("#gear_lever_cmnt").css("display", "");
	}
	else
	{
		$("#gear_lever_cmnt").css("display", "none");
	}
});

$('.sun_visor').click(function()
{
	var cmnt = $("input[type='radio'][name='sun_visor']:checked").val();  
	if(cmnt == 2)
	{
		$("#sun_visor_cmnt").css("display", "");
	}
	else
	{
		$("#sun_visor_cmnt").css("display", "none");
	}
});

$('.pillar_trim').click(function()
{
	var cmnt = $("input[type='radio'][name='pillar_trim']:checked").val();  
	if(cmnt == 2)
	{
		$("#pillar_trim_cmnt").css("display", "");
	}
	else
	{
		$("#pillar_trim_cmnt").css("display", "none");
	}
});

$('.armrest_console').click(function()
{
	var cmnt = $("input[type='radio'][name='armrest_console']:checked").val();  
	if(cmnt == 2)
	{
		$("#armrest_console_cmnt").css("display", "");
	}
	else
	{
		$("#armrest_console_cmnt").css("display", "none");
	}
});

$('.floor_mats_carpets').click(function()
{
	var cmnt = $("input[type='radio'][name='floor_mats_carpets']:checked").val();  
	if(cmnt == 2)
	{
		$("#floor_mats_carpets_cmnt").css("display", "");
	}
	else
	{
		$("#floor_mats_carpets_cmnt").css("display", "none");
	}
});

$('.trunk_liner').click(function()
{
	var cmnt = $("input[type='radio'][name='trunk_liner']:checked").val();  
	if(cmnt == 2)
	{
		$("#trunk_liner_cmnt").css("display", "");
	}
	else
	{
		$("#trunk_liner_cmnt").css("display", "none");
	}
});

$('.dashboard').click(function()
{
	var cmnt = $("input[type='radio'][name='dashboard']:checked").val();  
	if(cmnt == 2)
	{
		$("#dashboard_cmnt").css("display", "");
	}
	else
	{
		$("#dashboard_cmnt").css("display", "none");
	}
});

$('.glove_compartment').click(function()
{
	var cmnt = $("input[type='radio'][name='glove_compartment']:checked").val();  
	if(cmnt == 2)
	{
		$("#glove_compartment_cmnt").css("display", "");
	}
	else
	{
		$("#glove_compartment_cmnt").css("display", "none");
	}
});

$('.seats').click(function()
{
	var cmnt = $("input[type='radio'][name='seats']:checked").val();  
	if(cmnt == 2)
	{
		$("#seats_cmnt").css("display", "");
	}
	else
	{
		$("#seats_cmnt").css("display", "none");
	}
});

$('.door_trims').click(function()
{
	var cmnt = $("input[type='radio'][name='door_trims']:checked").val();  
	if(cmnt == 2)
	{
		$("#door_trims_cmnt").css("display", "");
	}
	else
	{
		$("#door_trims_cmnt").css("display", "none");
	}
});

$('.ac_grills').click(function()
{
	var cmnt = $("input[type='radio'][name='ac_grills']:checked").val();  
	if(cmnt == 2)
	{
		$("#ac_grills_cmnt").css("display", "");
	}
	else
	{
		$("#ac_grills_cmnt").css("display", "none");
	}
});

$('.sunroof_shade_liner').click(function()
{
	var cmnt = $("input[type='radio'][name='sunroof_shade_liner']:checked").val();  
	if(cmnt == 2)
	{
		$("#sunroof_shade_liner_cmnt").css("display", "");
	}
	else
	{
		$("#sunroof_shade_liner_cmnt").css("display", "none");
	}
});

<!-- Tyre -->

$('.spare_tyre').click(function()
{
	var cmnt = $("input[type='radio'][name='spare_tyre']:checked").val();  
	if(cmnt == 2)
	{
		$("#spare_tyre_cmnt").css("display", "");
	}
	else
	{
		$("#spare_tyre_cmnt").css("display", "none");
	}
});

$('.front_left_tyre').click(function()
{
	var cmnt = $("input[type='radio'][name='front_left_tyre']:checked").val();  
	if(cmnt == 2)
	{
		$("#front_left_tyre_cmnt").css("display", "");
	}
	else
	{
		$("#front_left_tyre_cmnt").css("display", "none");
	}
});

$('.back_right_tyre').click(function()
{
	var cmnt = $("input[type='radio'][name='back_right_tyre']:checked").val();  
	if(cmnt == 2)
	{
		$("#back_right_tyre_cmnt").css("display", "");
	}
	else
	{
		$("#back_right_tyre_cmnt").css("display", "none");
	}
});

$('.front_right_tyre').click(function()
{
	var cmnt = $("input[type='radio'][name='front_right_tyre']:checked").val();  
	if(cmnt == 2)
	{
		$("#front_right_tyre_cmnt").css("display", "");
	}
	else
	{
		$("#front_right_tyre_cmnt").css("display", "none");
	}
});

$('.back_left_tyre').click(function()
{
	var cmnt = $("input[type='radio'][name='back_left_tyre']:checked").val();  
	if(cmnt == 2)
	{
		$("#back_left_tyre_cmnt").css("display", "");
	}
	else
	{
		$("#back_left_tyre_cmnt").css("display", "none");
	}
});

<!-- Engine -->
$('.coolant_level').click(function()
{
	var cmnt = $("input[type='radio'][name='coolant_level']:checked").val();  
	if(cmnt == 2)
	{
		$("#coolant_level_cmnt").css("display", "");
	}
	else
	{
		$("#coolant_level_cmnt").css("display", "none");
	}
});

$('.coolant_leaks').click(function()
{
	var cmnt = $("input[type='radio'][name='coolant_leaks']:checked").val();  
	if(cmnt == 2)
	{
		$("#coolant_leaks_cmnt").css("display", "");
	}
	else
	{
		$("#coolant_leaks_cmnt").css("display", "none");
	}
});

$('.steering_fluid').click(function()
{
	var cmnt = $("input[type='radio'][name='steering_fluid']:checked").val();  
	if(cmnt == 2)
	{
		$("#steering_fluid_cmnt").css("display", "");
	}
	else
	{
		$("#steering_fluid_cmnt").css("display", "none");
	}
});

$('.brake_master_booster').click(function()
{
	var cmnt = $("input[type='radio'][name='brake_master_booster']:checked").val();  
	if(cmnt == 2)
	{
		$("#brake_master_booster_cmnt").css("display", "");
	}
	else
	{
		$("#brake_master_booster_cmnt").css("display", "none");
	}
});

$('.evidence_overheating').click(function()
{
	var cmnt = $("input[type='radio'][name='evidence_overheating']:checked").val();  
	if(cmnt == 2)
	{
		$("#evidence_overheating_cmnt").css("display", "");
	}
	else
	{
		$("#evidence_overheating_cmnt").css("display", "none");
	}
});

$('.coolant_conditions').click(function()
{
	var cmnt = $("input[type='radio'][name='coolant_conditions']:checked").val();  
	if(cmnt == 2)
	{
		$("#coolant_conditions_cmnt").css("display", "");
	}
	else
	{
		$("#coolant_conditions_cmnt").css("display", "none");
	}
});

$('.radiator_cap').click(function()
{
	var cmnt = $("input[type='radio'][name='radiator_cap']:checked").val();  
	if(cmnt == 2)
	{
		$("#radiator_cap_cmnt").css("display", "");
	}
	else
	{
		$("#radiator_cap_cmnt").css("display", "none");
	}
});

$('.radiator_fan').click(function()
{
	var cmnt = $("input[type='radio'][name='radiator_fan']:checked").val();  
	if(cmnt == 2)
	{
		$("#radiator_fan_cmnt").css("display", "");
	}
	else
	{
		$("#radiator_fan_cmnt").css("display", "none");
	}
});

$('.fender_liner').click(function()
{
	var cmnt = $("input[type='radio'][name='fender_liner']:checked").val();  
	if(cmnt == 2)
	{
		$("#fender_liner_cmnt").css("display", "");
	}
	else
	{
		$("#fender_liner_cmnt").css("display", "none");
	}
});

$('.hoses_pipes').click(function()
{
	var cmnt = $("input[type='radio'][name='hoses_pipes']:checked").val();  
	if(cmnt == 2)
	{
		$("#hoses_pipes_cmnt").css("display", "");
	}
	else
	{
		$("#hoses_pipes_cmnt").css("display", "none");
	}
});

$('.cable_harnes_connector').click(function()
{
	var cmnt = $("input[type='radio'][name='cable_harnes_connector']:checked").val();  
	if(cmnt == 2)
	{
		$("#cable_harnes_connector_cmnt").css("display", "");
	}
	else
	{
		$("#cable_harnes_connector_cmnt").css("display", "none");
	}
});

$('.power_steer_fluidlevel').click(function()
{
	var cmnt = $("input[type='radio'][name='power_steer_fluidlevel']:checked").val();  
	if(cmnt == 2)
	{
		$("#power_steer_fluidlevel_cmnt").css("display", "");
	}
	else
	{
		$("#power_steer_fluidlevel_cmnt").css("display", "none");
	}
});

$('.engine_oil_level').click(function()
{
	var cmnt = $("input[type='radio'][name='engine_oil_level']:checked").val();  
	if(cmnt == 2)
	{
		$("#engine_oil_level_cmnt").css("display", "");
	}
	else
	{
		$("#engine_oil_level_cmnt").css("display", "none");
	}
});

$('.external_engine_leaks').click(function()
{
	var cmnt = $("input[type='radio'][name='external_engine_leaks']:checked").val();  
	if(cmnt == 2)
	{
		$("#external_engine_leaks_cmnt").css("display", "");
	}
	else
	{
		$("#external_engine_leaks_cmnt").css("display", "none");
	}
});

$('.engine_mounts').click(function()
{
	var cmnt = $("input[type='radio'][name='engine_mounts']:checked").val();  
	if(cmnt == 2)
	{
		$("#engine_mounts_cmnt").css("display", "");
	}
	else
	{
		$("#engine_mounts_cmnt").css("display", "none");
	}
});

$('.turbo_supercharger').click(function()
{
	var cmnt = $("input[type='radio'][name='turbo_supercharger']:checked").val();  
	if(cmnt == 2)
	{
		$("#turbo_supercharger_cmnt").css("display", "");
	}
	else
	{
		$("#turbo_supercharger_cmnt").css("display", "none");
	}
});

$('.fuel_pump_pipes').click(function()
{
	var cmnt = $("input[type='radio'][name='fuel_pump_pipes']:checked").val();  
	if(cmnt == 2)
	{
		$("#fuel_pump_pipes_cmnt").css("display", "");
	}
	else
	{
		$("#fuel_pump_pipes_cmnt").css("display", "none");
	}
});

$('.cold_starting').click(function()
{
	var cmnt = $("input[type='radio'][name='cold_starting']:checked").val();  
	if(cmnt == 2)
	{
		$("#cold_starting_cmnt").css("display", "");
	}
	else
	{
		$("#cold_starting_cmnt").css("display", "none");
	}
});

$('.fast_idle').click(function()
{
	var cmnt = $("input[type='radio'][name='fast_idle']:checked").val();  
	if(cmnt == 2)
	{
		$("#fast_idle_cmnt").css("display", "");
	}
	else
	{
		$("#fast_idle_cmnt").css("display", "none");
	}
});

$('.noise_level').click(function()
{
	var cmnt = $("input[type='radio'][name='noise_level']:checked").val();  
	if(cmnt == 2)
	{
		$("#noise_level_cmnt").css("display", "");
	}
	else
	{
		$("#noise_level_cmnt").css("display", "none");
	}
});

$('.excess_smoke').click(function()
{
	var cmnt = $("input[type='radio'][name='excess_smoke']:checked").val();  
	if(cmnt == 2)
	{
		$("#excess_smoke_cmnt").css("display", "");
	}
	else
	{
		$("#excess_smoke_cmnt").css("display", "none");
	}
});

$('.inlet_manifold').click(function()
{
	var cmnt = $("input[type='radio'][name='inlet_manifold']:checked").val();  
	if(cmnt == 2)
	{
		$("#inlet_manifold_cmnt").css("display", "");
	}
	else
	{
		$("#inlet_manifold_cmnt").css("display", "none");
	}
});

$('.outlet_manifold').click(function()
{
	var cmnt = $("input[type='radio'][name='outlet_manifold']:checked").val();  
	if(cmnt == 2)
	{
		$("#outlet_manifold_cmnt").css("display", "");
	}
	else
	{
		$("#outlet_manifold_cmnt").css("display", "none");
	}
});

$('.exhaust_pipes').click(function()
{
	var cmnt = $("input[type='radio'][name='exhaust_pipes']:checked").val();  
	if(cmnt == 2)
	{
		$("#exhaust_pipes_cmnt").css("display", "");
	}
	else
	{
		$("#exhaust_pipes_cmnt").css("display", "none");
	}
});

$('.silencer').click(function()
{
	var cmnt = $("input[type='radio'][name='silencer']:checked").val();  
	if(cmnt == 2)
	{
		$("#silencer_cmnt").css("display", "");
	}
	else
	{
		$("#silencer_cmnt").css("display", "none");
	}
});

$('.head_shield_mounting').click(function()
{
	var cmnt = $("input[type='radio'][name='head_shield_mounting']:checked").val();  
	if(cmnt == 2)
	{
		$("#head_shield_mounting_cmnt").css("display", "");
	}
	else
	{
		$("#head_shield_mounting_cmnt").css("display", "none");
	}
});

$('.joints_couplings').click(function()
{
	var cmnt = $("input[type='radio'][name='joints_couplings']:checked").val();  
	if(cmnt == 2)
	{
		$("#joints_couplings_cmnt").css("display", "");
	}
	else
	{
		$("#joints_couplings_cmnt").css("display", "none");
	}
});

$('.engine_underside_leak').click(function()
{
	var cmnt = $("input[type='radio'][name='engine_underside_leak']:checked").val();  
	if(cmnt == 2)
	{
		$("#engine_underside_leak_cmnt").css("display", "");
	}
	else
	{
		$("#engine_underside_leak_cmnt").css("display", "none");
	}
});

$('.catalytic_converter').click(function()
{
	var cmnt = $("input[type='radio'][name='catalytic_converter']:checked").val();  
	if(cmnt == 2)
	{
		$("#catalytic_converter_cmnt").css("display", "");
	}
	else
	{
		$("#catalytic_converter_cmnt").css("display", "none");
	}
});

$('.engine_shield').click(function()
{
	var cmnt = $("input[type='radio'][name='engine_shield']:checked").val();  
	if(cmnt == 2)
	{
		$("#engine_shield_cmnt").css("display", "");
	}
	else
	{
		$("#engine_shield_cmnt").css("display", "none");
	}
});

<!-- Transmission -->
$('.gear_selector').click(function()
{
	var cmnt = $("input[type='radio'][name='gear_selector']:checked").val();  
	if(cmnt == 2)
	{
		$("#gear_selector_cmnt").css("display", "");
	}
	else
	{
		$("#gear_selector_cmnt").css("display", "none");
	}
});

$('.gear_shifting').click(function()
{
	var cmnt = $("input[type='radio'][name='gear_shifting']:checked").val();  
	if(cmnt == 2)
	{
		$("#gear_shifting_cmnt").css("display", "");
	}
	else
	{
		$("#gear_shifting_cmnt").css("display", "none");
	}
});

$('.gear_noise').click(function()
{
	var cmnt = $("input[type='radio'][name='gear_noise']:checked").val();  
	if(cmnt == 2)
	{
		$("#gear_noise_cmnt").css("display", "");
	}
	else
	{
		$("#gear_noise_cmnt").css("display", "none");
	}
});

$('.fluid_level_oil_leak').click(function()
{
	var cmnt = $("input[type='radio'][name='fluid_level_oil_leak']:checked").val();  
	if(cmnt == 2)
	{
		$("#fluid_level_oil_leak_cmnt").css("display", "");
	}
	else
	{
		$("#fluid_level_oil_leak_cmnt").css("display", "none");
	}
});

$('.transmission_mount').click(function()
{
	var cmnt = $("input[type='radio'][name='transmission_mount']:checked").val();  
	if(cmnt == 2)
	{
		$("#transmission_mount_cmnt").css("display", "");
	}
	else
	{
		$("#transmission_mount_cmnt").css("display", "none");
	}
});

<!-- Electrical -->
$('.door_locks').click(function()
{
	var cmnt = $("input[type='radio'][name='door_locks']:checked").val();  
	if(cmnt == 2)
	{
		$("#door_locks_cmnt").css("display", "");
	}
	else
	{
		$("#door_locks_cmnt").css("display", "none");
	}
});

$('.central_locking').click(function()
{
	var cmnt = $("input[type='radio'][name='central_locking']:checked").val();  
	if(cmnt == 2)
	{
		$("#central_locking_cmnt").css("display", "");
	}
	else
	{
		$("#central_locking_cmnt").css("display", "none");
	}
});

$('.ignitionlock_startsys').click(function()
{
	var cmnt = $("input[type='radio'][name='ignitionlock_startsys']:checked").val();  
	if(cmnt == 2)
	{
		$("#ignitionlock_startsys_cmnt").css("display", "");
	}
	else
	{
		$("#ignitionlock_startsys_cmnt").css("display", "none");
	}
});

$('.instrument_panel').click(function()
{
	var cmnt = $("input[type='radio'][name='instrument_panel']:checked").val();  
	if(cmnt == 2)
	{
		$("#instrument_panel_cmnt").css("display", "");
	}
	else
	{
		$("#instrument_panel_cmnt").css("display", "none");
	}
});

$('.headlights').click(function()
{
	var cmnt = $("input[type='radio'][name='headlights']:checked").val();  
	if(cmnt == 2)
	{
		$("#headlights_cmnt").css("display", "");
	}
	else
	{
		$("#headlights_cmnt").css("display", "none");
	}
});

$('.sidelights_runlights').click(function()
{
	var cmnt = $("input[type='radio'][name='sidelights_runlights']:checked").val();  
	if(cmnt == 2)
	{
		$("#sidelights_runlights_cmnt").css("display", "");
	}
	else
	{
		$("#sidelights_runlights_cmnt").css("display", "none");
	}
});

$('.rear_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='rear_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#rear_lights_cmnt").css("display", "");
	}
	else
	{
		$("#rear_lights_cmnt").css("display", "none");
	}
});

$('.indicator_hazardlights').click(function()
{
	var cmnt = $("input[type='radio'][name='indicator_hazardlights']:checked").val();  
	if(cmnt == 2)
	{
		$("#indicator_hazardlights_cmnt").css("display", "");
	}
	else
	{
		$("#indicator_hazardlights_cmnt").css("display", "none");
	}
});

$('.boot_tailgate_lock').click(function()
{
	var cmnt = $("input[type='radio'][name='boot_tailgate_lock']:checked").val();  
	if(cmnt == 2)
	{
		$("#boot_tailgate_lock_cmnt").css("display", "");
	}
	else
	{
		$("#boot_tailgate_lock_cmnt").css("display", "none");
	}
});

$('.reverse_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='reverse_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#reverse_lights_cmnt").css("display", "");
	}
	else
	{
		$("#reverse_lights_cmnt").css("display", "none");
	}
});

$('.fog_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='fog_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#fog_lights_cmnt").css("display", "");
	}
	else
	{
		$("#fog_lights_cmnt").css("display", "none");
	}
});

$('.multimedia').click(function()
{
	var cmnt = $("input[type='radio'][name='multimedia']:checked").val();  
	if(cmnt == 2)
	{
		$("#multimedia_cmnt").css("display", "");
	}
	else
	{
		$("#multimedia_cmnt").css("display", "none");
	}
});

$('.ac_control_cooling').click(function()
{
	var cmnt = $("input[type='radio'][name='ac_control_cooling']:checked").val();  
	if(cmnt == 2)
	{
		$("#ac_control_cooling_cmnt").css("display", "");
	}
	else
	{
		$("#ac_control_cooling_cmnt").css("display", "none");
	}
});

$('.side_mirror').click(function()
{
	var cmnt = $("input[type='radio'][name='side_mirror']:checked").val();  
	if(cmnt == 2)
	{
		$("#side_mirror_cmnt").css("display", "");
	}
	else
	{
		$("#side_mirror_cmnt").css("display", "none");
	}
});

$('.auxiliary_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='auxiliary_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#auxiliary_lights_cmnt").css("display", "");
	}
	else
	{
		$("#auxiliary_lights_cmnt").css("display", "none");
	}
});

$('.panel_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='panel_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#panel_lights_cmnt").css("display", "");
	}
	else
	{
		$("#panel_lights_cmnt").css("display", "none");
	}
});

$('.horn').click(function()
{
	var cmnt = $("input[type='radio'][name='horn']:checked").val();  
	if(cmnt == 2)
	{
		$("#horn_cmnt").css("display", "");
	}
	else
	{
		$("#horn_cmnt").css("display", "none");
	}
});

$('.window_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='window_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#window_operation_cmnt").css("display", "");
	}
	else
	{
		$("#window_operation_cmnt").css("display", "none");
	}
});

$('.sunroof_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='sunroof_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#sunroof_operation_cmnt").css("display", "");
	}
	else
	{
		$("#sunroof_operation_cmnt").css("display", "none");
	}
});

$('.wipers_jet_washers').click(function()
{
	var cmnt = $("input[type='radio'][name='wipers_jet_washers']:checked").val();  
	if(cmnt == 2)
	{
		$("#wipers_jet_washers_cmnt").css("display", "");
	}
	else
	{
		$("#wipers_jet_washers_cmnt").css("display", "none");
	}
});

$('.wipers_jet_washers').click(function()
{
	var cmnt = $("input[type='radio'][name='wipers_jet_washers']:checked").val();  
	if(cmnt == 2)
	{
		$("#wipers_jet_washers_cmnt").css("display", "");
	}
	else
	{
		$("#wipers_jet_washers_cmnt").css("display", "none");
	}
});

$('.keys_remote_controls').click(function()
{
	var cmnt = $("input[type='radio'][name='keys_remote_controls']:checked").val();  
	if(cmnt == 2)
	{
		$("#keys_remote_controls_cmnt").css("display", "");
	}
	else
	{
		$("#keys_remote_controls_cmnt").css("display", "none");
	}
});

$('.warning_lights').click(function()
{
	var cmnt = $("input[type='radio'][name='warning_lights']:checked").val();  
	if(cmnt == 2)
	{
		$("#warning_lights_cmnt").css("display", "");
	}
	else
	{
		$("#warning_lights_cmnt").css("display", "none");
	}
});

$('.number_plate_light').click(function()
{
	var cmnt = $("input[type='radio'][name='number_plate_light']:checked").val();  
	if(cmnt == 2)
	{
		$("#number_plate_light_cmnt").css("display", "");
	}
	else
	{
		$("#number_plate_light_cmnt").css("display", "none");
	}
});

$('.steering_ball_joints').click(function()
{
	var cmnt = $("input[type='radio'][name='steering_ball_joints']:checked").val();  
	if(cmnt == 2)
	{
		$("#steering_ball_joints_cmnt").css("display", "");
	}
	else
	{
		$("#steering_ball_joints_cmnt").css("display", "none");
	}
});

$('.brakes_lines').click(function()
{
	var cmnt = $("input[type='radio'][name='brakes_lines']:checked").val();  
	if(cmnt == 2)
	{
		$("#brakes_lines_cmnt").css("display", "");
	}
	else
	{
		$("#brakes_lines_cmnt").css("display", "none");
	}
});

$('.subframe').click(function()
{
	var cmnt = $("input[type='radio'][name='subframe']:checked").val();  
	if(cmnt == 2)
	{
		$("#subframe_cmnt").css("display", "");
	}
	else
	{
		$("#subframe_cmnt").css("display", "none");
	}
});

$('.wheels_hubs_bearings').click(function()
{
	var cmnt = $("input[type='radio'][name='wheels_hubs_bearings']:checked").val();  
	if(cmnt == 2)
	{
		$("#wheels_hubs_bearings_cmnt").css("display", "");
	}
	else
	{
		$("#wheels_hubs_bearings_cmnt").css("display", "none");
	}
});

$('.dampers_bushes').click(function()
{
	var cmnt = $("input[type='radio'][name='dampers_bushes']:checked").val();  
	if(cmnt == 2)
	{
		$("#dampers_bushes_cmnt").css("display", "");
	}
	else
	{
		$("#dampers_bushes_cmnt").css("display", "none");
	}
});

$('.power_steering_rack').click(function()
{
	var cmnt = $("input[type='radio'][name='power_steering_rack']:checked").val();  
	if(cmnt == 2)
	{
		$("#power_steering_rack_cmnt").css("display", "");
	}
	else
	{
		$("#power_steering_rack_cmnt").css("display", "none");
	}
});

$('.evidencefloor_chassis').click(function()
{
	var cmnt = $("input[type='radio'][name='evidencefloor_chassis']:checked").val();  
	if(cmnt == 2)
	{
		$("#evidencefloor_chassis_cmnt").css("display", "");
	}
	else
	{
		$("#evidencefloor_chassis_cmnt").css("display", "none");
	}
});

<!-- Test Drive -->
$('.engine_performance').click(function()
{
	var cmnt = $("input[type='radio'][name='engine_performance']:checked").val();  
	if(cmnt == 2)
	{
		$("#engine_performance_cmnt").css("display", "");
	}
	else
	{
		$("#engine_performance_cmnt").css("display", "none");
	}
});

$('.gearbox_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='gearbox_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#gearbox_operation_cmnt").css("display", "");
	}
	else
	{
		$("#gearbox_operation_cmnt").css("display", "none");
	}
});

$('.clutch_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='clutch_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#clutch_operation_cmnt").css("display", "");
	}
	else
	{
		$("#clutch_operation_cmnt").css("display", "none");
	}
});

$('.steering_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='steering_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#steering_operation_cmnt").css("display", "");
	}
	else
	{
		$("#steering_operation_cmnt").css("display", "none");
	}
});

$('.brake_operation').click(function()
{
	var cmnt = $("input[type='radio'][name='brake_operation']:checked").val();  
	if(cmnt == 2)
	{
		$("#brake_operation_cmnt").css("display", "");
	}
	else
	{
		$("#brake_operation_cmnt").css("display", "none");
	}
});

$('.hand_parking_brake').click(function()
{
	var cmnt = $("input[type='radio'][name='hand_parking_brake']:checked").val();  
	if(cmnt == 2)
	{
		$("#hand_parking_brake_cmnt").css("display", "");
	}
	else
	{
		$("#hand_parking_brake_cmnt").css("display", "none");
	}
});

$('.drive_train').click(function()
{
	var cmnt = $("input[type='radio'][name='drive_train']:checked").val();  
	if(cmnt == 2)
	{
		$("#drive_train_cmnt").css("display", "");
	}
	else
	{
		$("#drive_train_cmnt").css("display", "none");
	}
});

$('.instru_control_func').click(function()
{
	var cmnt = $("input[type='radio'][name='instru_control_func']:checked").val();  
	if(cmnt == 2)
	{
		$("#instru_control_func_cmnt").css("display", "");
	}
	else
	{
		$("#instru_control_func_cmnt").css("display", "none");
	}
});

$('.suspension_noise').click(function()
{
	var cmnt = $("input[type='radio'][name='suspension_noise']:checked").val();  
	if(cmnt == 2)
	{
		$("#suspension_noise_cmnt").css("display", "");
	}
	else
	{
		$("#suspension_noise_cmnt").css("display", "none");
	}
});

$('.road_holding_stability').click(function()
{
	var cmnt = $("input[type='radio'][name='road_holding_stability']:checked").val();  
	if(cmnt == 2)
	{
		$("#road_holding_stability_cmnt").css("display", "");
	}
	else
	{
		$("#road_holding_stability_cmnt").css("display", "none");
	}
});

$('.nois').click(function()
{
	var cmnt = $("input[type='radio'][name='nois']:checked").val();  
	if(cmnt == 2)
	{
		$("#nois_cmnt").css("display", "");
	}
	else
	{
		$("#nois_cmnt").css("display", "none");
	}
});

$('.shock_absorber').click(function()
{
	var cmnt = $("input[type='radio'][name='shock_absorber']:checked").val();  
	if(cmnt == 2)
	{
		$("#shock_absorber_cmnt").css("display", "");
	}
	else
	{
		$("#shock_absorber_cmnt").css("display", "none");
	}
});

/********** INSPECTION CHECKLIST END **********/
</script>

@endsection