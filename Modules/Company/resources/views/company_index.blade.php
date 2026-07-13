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
  <?php
 // $option1 = json_decode($option->opset_options);
  //if(in_array('1',$option1) || in_array('2',$option1)){
  ?>
                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-flex align-items-center justify-content-between">
                                    <h4 class="mb-0">Manage Company</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                            <li class="breadcrumb-item active">Manage Company</li>
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
                                        
                                            <div class="">
												{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'createForm', 'class'=>'myform', 'files'=>'true'])->open() !!}
												
											        <input type="hidden" name="company_id" id="company_id">
											        <input type='hidden' name='_token' value='{{csrf_token()}}'> 
													 
													<div class="row"> 
													
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="formrow-email-input">Company Name<span class="star_required" style="color:red"> *</span></label>
																<div class="form-group">
																{!! html()-> text('company_name','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Company Name Required', 'class'=>'form-control company_name', 'id'=>'company_name'])!!}     		
																</div>
																<span class="highlight"></span>
																<span id="company_name-parsley-error"></span>
															</div>
                                                        </div>
														
														<div class="col-md-3">
															<div class="mb-3">
															<label class="form-label" for="formrow-email-input">Company Code<span class="star_required" style="color:red"> *</span></label>
															{!! html()-> text()->attributes(['name'=>'company_code', 'required'=>'required', 'placeholder'=>"Company Code", 'data-parsley-required-message'=>'Company Code Required', 'class'=>'form-control', 'id'=>'company_code'])!!}
															<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-3">
																<label class="form-label" for="formrow-email-input">Contact Person</label>
																{!! html()->text('company_person','',)->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Contact Person Required', 'class'=>'form-control', 'id'=>'company_person'])!!}																
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-3">
																<label class="form-label" for="formrow-email-input">Contact Mobile<span class="star_required" style="color:red"> *</span></label>
																{!! html()-> text('company_mob','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Mobile No. Required', 'data-parsley-length'=>'[0,10]', 'data-parsley-length'=>'[0,10]', 'data-parsley-minlength'=>'10', 'data-parsley-minlength-message'=>'Invalid Mobile No.' , 'class'=>'form-control', 'id'=>'company_mob'])!!}
																 
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-3">
																<label class="form-label" for="formrow-email-input">Contact Landline</label>
																{!! html()->text('company_lan','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_lan','onkeypress'=>'return isNumberKey(event)','maxlength'=>'15'])!!}
																 
																<span class="highlight"></span>

															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-3">
																<label class="form-label" for="formrow-email-input">Email</label>
																{!! html()->text('company_email','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Email ID Required', 'class'=>'form-control', 'id'=>'company_email'])!!}
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-3">
																<label class="form-label" for="formrow-email-input">Website</label>
																{!! html()->text('company_web','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Website Required', 'class'=>'form-control', 'id'=>'company_web'])!!}
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3">
															<div class="mb-1" >
																<label class="form-label" for="formrow-email-input">Logo <span style="color:red;font-size:87.5%">&nbsp;(Size:415*99)</span></label>
																{!! html()->file('company_logo')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Logo required', 'class'=>'form-control', 'id'=>'company_logo'])!!}
																<div id="image1" style="text-align:right"><span class="highlight" style="color:red;font-size:87.5%;"></span></div>
															</div>
															
														</div>
														
														<div class="col-md-3" style="margin-top:-1%">
														    <div class="">
    															<label class="col-form-label">Country</label>
    															<select  class="form-select form-control select2" id="company_country" name="company_country" data-placeholder="Select ...">
    																<option value="">--Select Country--</option>
    																<?php 
    																foreach ($country as $value) 
    																{ ?>
    															        <option value="<?=$value->id;?>" <?php if($value->id == 101) { ?>Selected <?php } ?>><?=$value->name;?></option>
    																	<?php 
    																 } ?>  
    															</select>
    															<span class="highlight"></span>
														    </div>
														</div>
														
														<div class="col-md-3" style="margin-top:-1%">
    														<div class="">
    																<label class="col-form-label">State</label>
    																<select  class="form-select form-control select2" id="company_state" name="company_state" data-state="19" data-placeholder="Select ...">
    																	<option value="">--Select State--</option>
    																	
    																</select>
    															<span class="highlight"></span>
    												        </div>
														</div>
														
														<div class="col-md-3" style="margin-top:-1%">
															<div class="">
																<label class="col-form-label">District</label>
																<select name="company_city" id="company_city" class="form-select form-control select2" data-city="" data-placeholder="Select ..." >
																	<option value="">-- Select District---</option>
																</select>
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3" style="margin-top:-1px">
															<div class="">
																<label class="form-label" for="formrow-email-input">Address</label>
																<textarea name="company_address" id="company_address" rows="1" class="form-control" placeholder="Address" style="height:37px;resize:none;"></textarea>
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3" style="margin-top:1%;display:none;">
															<div class="mb-3">
																<label class="form-label" >Latitude</label>
																{!! html()->text('company_lat','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_lat', "oninput"=>"this.value = this.value = this.value.replace(/[^0-9.]/g, '')", 'onkeypress'=>'return lat(event)'])!!}
																<span class="highlight"></span>
															</div>
														</div>
														
														<div class="col-md-3" style="margin-top:1%;display:none;">
															<div class="mb-3">
																<label class="form-label" >Longitude</label>
																{!! html()->text('company_long','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_long', "oninput"=>"this.value = this.value = this.value.replace(/[^0-9.]/g, '')",'onkeypress'=>"return lat(event)"])!!}
																<span class="highlight"></span>
															</div>
														</div>
			
        												<div class="col-md-3" > 	
                                                            <div class="flex-wrap gap-3 editButton" style="display:none;margin-top:8%">
        														<a href="#" class="btn btn-primary waves-effect waves-light " onclick="createOrUpdate('{{URL::to("company/edit_company")}}', 'createForm', 'edit-modal', 'companyDatatable','')" class="btn btn-info btn-block">
        															<!--i class="fas fa-pencil-alt"></i-->Update
        														</a>
        													</div>
        													<div class="flex-wrap gap-3  saveButton" style="margin-top:8%">
        														<a href="#" class="btn btn-primary waves-effect waves-light " onclick="createOrUpdate('{{URL::to("company/add_company")}}', 'createForm', 'add-modal', 'companyDatatable','')" class="btn btn-info btn-block">
        															<!--i class="fas fa-save" title="Save"--></i> Save
        														</a>&nbsp;
        														<a href="#" onclick="clearForm()" class="btn btn-danger waves-effect waves-light ">
        															Reset
        														</a>
        													</div>   
                                                        </div>        
                                                    </div>           
                                                {!! html()->form()->close() !!}
                                            </div>
                                          
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Form Layout -->
						
						<div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
        
                                        <!--h4 class="card-title">Added Company</h4-->
                                        <!--p class="card-title-desc"></p-->
										<!--table id="companyDatatable" class="mytable table table-bordered table-hover"-->
    									<div class="table-responsive">	
                                            <table id="companyDatatable" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="sno" style="width:5%">#</th>
        												<th style="width:10%">Unique ID</th>
        												<th style="width:20%">Company Name</th>
        												<th style="width:20%">Company Code </th>
        												<th style="width:10%">Mobile</th>
        												<th style="width:20%">Email</th>
        												<!--<th style="width:10%">Logo </th>-->
        												<th style="width:5%">Added By</th>
        												<th class="actionwidth">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody> </tbody>
                                            </table>
    									</div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

        <!-------- Delete Modal ------------->						
		<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete Company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!} 
 						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_id' id="del_id" value=''> 
						 <p>Are you sure, you want to delete the Company?</p>
						 {!! html()->form()->close() !!} 
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button>
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
			</div>
		</div>

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php //} ?>

    <!-------------    view modal  ------------------>

    <div id="view_modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body" id="view-modal-body">
                    <!--h5 class="font-size-16">Overflowing text to show scroll behavior</h5-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

 @endsection
 
 @section('js')
<script type="text/javascript">
    var public_path = '<?php echo url('/');?>';
    var url_companyDataTable = '{{URL::to("company/get_datatable")}}';
    var url_editcompany      = '{{URL::to("company/get_details")}}';
    var url_viewcompany      = '{{URL::to("company/view_company")}}';
    var url_deletecompany    = '{{URL::to("company/delete_company")}}';
</script>
	  
	 <script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>     
        <!-- Required datatable js -->
        <script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        <!-- Buttons examples -->
        <script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
  
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
        
		<!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
        <!-- Datatable init js -->
        <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
		<script src="{{asset('module.js/main.js?ver=1.2')}}"></script>
		<script src="{{asset('module.js/Company/index.js?ver=2.3')}}"></script>
		
<script type="text/javascript">
$( document ).ready(function() 
  {
  $('#company_country').trigger('change');
  });
$('#company_country').change(function()
  {
  var country = $(this).val();
  if(country)
    { 
    $.ajax({
          type: 'GET',
          url: 'company/get_state',
          data: { country: country },
          success:function(res)
            {              
            $("#company_state").empty();
            $("#company_state").append('<option value="">--- State ---</option>');
            $("#company_city").empty();
            $("#company_city").append('<option value="">--- City ---</option>');
            if(res)
              {
              var a = $('#company_state').attr('data-state'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#company_state").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#company_state").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              $('#company_state').trigger('change');
              }
            }
         });
    }
  else
    {
    $("#company_city").empty();
    $("#company_city").append('<option value="">--- City ---</option>');
    }      
  });
$('#company_state').change(function()
  {
  var state = $(this).val();
  if(state)
    { 
    $.ajax({
          type: 'GET',
          url: 'company/get_district',
          data: { state: state },
          success:function(res)
            {              
            $("#company_city").empty();
            $("#company_city").append('<option value="">--- City ---</option>');
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
    $("#company_city").append('<option value="">--- City ---</option>');
    } 
  });
</script>    

@endsection     
