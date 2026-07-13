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
                        <h4 class="mb-0">Manage Company</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Manage Company</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div><!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">           
                            <div class="row">      
                                <div class="">
									{!! html()->form('POST')->attributes(['class' => 'myform', 'id' => 'createForm','files' => true])->open() !!} 
				   
									<input type="hidden" name="company_id" id="company_id">
								<div class="row"> 		
								
									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()-> text('company_name','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Company Name Required', 'class'=>'form-control company_name', 'id'=>'company_name'])!!}     
											<span class="highlight"></span>
											<label>Company Name</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()-> text()->attributes(['name'=>'company_shortcode', 'required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Short Code Required', 'class'=>'form-control', 'id'=>'company_shortcode'])!!}
											<span class="highlight"></span>
											<label>Short Code</label>
										</div>
									</div>

								   <div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->text('company_person','',)->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Contact Person Required', 'class'=>'form-control', 'id'=>'company_person'])!!}
											<span class="highlight"></span>
											<label>Contact Person</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->text('company_design','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Design Required', 'class'=>'form-control', 'id'=>'company_design'])!!}
											<span class="highlight"></span>
											<label>Designation</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()-> text('company_mob','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Mobile No. Required', 'data-parsley-length'=>'[0,10]', 'data-parsley-length'=>'[0,10]', 'data-parsley-minlength'=>'10', 'data-parsley-minlength-message'=>'Invalid Mobile No.' , 'class'=>'form-control', 'id'=>'company_mob'])!!}
											<span class="highlight"></span>
											<label>Mobile No.</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->text('company_land','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_land'])!!}
											<span class="highlight"></span>
											<label>Landline No.</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->text('company_email','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Email ID Required', 'class'=>'form-control', 'id'=>'company_email'])!!}
											<span class="highlight"></span>
											<label>Email ID</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->text('company_web','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Website Required', 'class'=>'form-control', 'id'=>'company_web'])!!}
											<span class="highlight"></span>
											<label>Website</label>
										</div>
									</div>

									<div class="col-md-3"> 
													<div class="mb-3"> 
											{!! html()->textarea('company_address','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Address  Required', 'class'=>'form-control', 'id'=>'company_address', 'style'=>'height:45px'])!!}
											<span class="highlight"></span>
											<label>Address</label>
										</div>
									</div>
									
									<div class="col-md-3" style="margin-bottom:6px" > 
										<div class="mb-3"> 
											<select type="text" name="company_state" required='required' data-parsley-required-message='Select State' class='form-control' id='company_state' data-parsley-errors-container='#company_state-parsley-error'>
												<option value=""> Select State </option>
													<?php 
													foreach ($state as $val)
													{ ?>
														<option value="<?=$val->id;?>"><?=$val->name;?></option>
												   <?php 
												   } ?>
											</select>
											<span style="float:right;" id="company_state-parsley-error"></span>
										</div>
								   </div>
					   
								   <div class="col-md-3" style="margin-bottom:6px" > 
										<div class="mb-3"> 
												<select type="text" name="company_district" required='required' data-parsley-required-message='Select City' class='form-control' id='company_district' data-parsley-errors-container='#company_district-parsley-error' data-company_district="">
												<option value=""> Select City </option>
											</select>
											<span style="float:right;" id="company_district-parsley-error"></span>
										</div>
									</div>

									<div class="col-md-3" style="margin-bottom:6px" > 
										<div class="mb-3"> 
											{!! html()->text('company_pin','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Pincode Required', 'class'=>'form-control', 'id'=>'company_pin', "oninput"=>"this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..?)\../g, '$1')"])!!}
											<span class="highlight"></span>
											<label>Pincode</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_gstin','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'GSTIN required','class'=>'form-control', 'id'=>'company_gstin', "oninput"=>"this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..?)\../g, '$1')"])!!}
											<span class="highlight"></span>
											<label>GSTIN</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_pan','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'PAN No.required','class'=>'form-control', 'id'=>'company_pan', "oninput"=>"this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..?)\../g, '$1')"])!!}
											<span class="highlight"></span>
											<label>PAN No.</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_cin','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'CIN No. required','class'=>'form-control', 'id'=>'company_cin', "oninput"=>"this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..?)\../g, '$1')"])!!}
											<span class="highlight"></span>
											<label>CIN No.</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_tds','')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'TDS No. required', 'class'=>'form-control', 'id'=>'company_tds', "oninput"=>"this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..?)\../g, '$1')"])!!}
											<span class="highlight"></span>
											<label>TDS No.</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->file('company_logo')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Logo required', 'class'=>'form-control', 'id'=>'company_logo'])!!}
											<!--  <span class="highlight"></span> -->
											<label>Company Logo 300x400</label>
										</div>
									</div>
			 
									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_latitude','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_latitude', "oninput"=>"this.value = this.value = this.value.replace(/[^0-9.]/g, '')"])!!}
											<span class="highlight"></span>
											<label>Latitude</label>
										</div>
									</div>

									<div class="col-md-3"> 
										<div class="mb-3"> 
											{!! html()->text('company_longitude','')->attributes(['placeholder'=>"", 'class'=>'form-control', 'id'=>'company_longitude', "oninput"=>"this.value = this.value = this.value.replace(/[^0-9.]/g, '')"])!!}
											<span class="highlight"></span>
											<label>Longitude</label>
										</div>
									</div>

									<div class="col-md-2 editButton" style="display: none;">
										<a href="#" onclick="createOrUpdate('{{URL::to("company/editDivision")}}', 'createForm', 'add-modal', 'divisionDataTable','{{URL::to("company/")}}')" class="btn btn-info btn-block"><i class="fa fa-save"></i>Update</a>
									</div>

									<div class="col-md-4 saveButton">
										<a href="#" onclick="createOrUpdate('{{URL::to("company/addcompany")}}', 'createForm', 'add-modal', 'divisionDataTable','')" class="btn btn-info btn-block"><i class="fa fa-save"></i> Save</a>
										<a href="#" onclick="clearForm(),reset()" class="btn btn-warning btn-block"><i class="fa fa-refresh"></i> Reset</a>
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
							<div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
																											
                                        <!--h4 class="card-title">View Centre</h4>
                                        <p class="card-title-desc"></p-->
										<div class="table-responsive m-t-40">
										   <table id="divisionDataTable" class="mytable table table-bordered table-hover">
											<thead>
											 <tr>
													<th class="sno">#</th>
													<th>Company ID</th>
													<th>Company Name</th>
													<th>Code </th>
													<th>Person Name </th>
														  
													<th>Designation</th>
													<th>Mobile No. </th>
													<th>Landline</th>
													<th>Email ID </th>
													<th>State </th>
													<th>District </th>
													<th>Added By </th>
													<th class="actionwidth">Action</th>
											</tr>
											</thead>
											<tbody>
											 </tbody>
										</table>
									  </div>
										  <!-- End Form --> 
									</div><!-- End Form -->
								</div>
							</div>
						</div>
				</div>
								
						
			<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="staticBackdropLabel">Delete Branch</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							{!! html()->form('POST', '')->attributes(['class' => 'myform', 'id' => 'deleteForm'])->open() !!}
							<input type='hidden' name='_token' value='{{csrf_token()}}'> 
							<input type='hidden' name='delete_id' id="delete_id" value=''> 
							
							 <p>Are you sure, You want to delete the branch?</p>
						</div>
						<div class="modal-footer">
							<button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button>
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							{!! html()->form()->close() !!} 
						</div>
					</div>
				</div>
			</div>
                        
        </div><!-- container-fluid -->
    </div><!-- End Page-content -->



@endsection

@section('js')
<script type="text/javascript">
    var url_DivisionDataTable ='{{URL::to("company/getDatatable")}}';
    var url_editdivision  = '{{URL::to("company/getDivisions")}}';
    var url_deletedivision =  '{{URL::to("company/deleteDivision")}}';
    var url_viewenquery = '{{URL::to("company/getEnqueryData")}}';
    var get_city = '{{URL::to("company/get_city")}}';
</script>
<script>
        var public_path = '<?php echo url('/');?>';
    </script>
    
        <script type="text/javascript">
//var get_state = '{{URL::to("Branch/get_state")}}';

$('#branch_state').change(function()
 {
 var branch_state = $(this).val();    
 if(branch_state)
    {
    $.ajax({
            type:"GET",
            url:get_state+"?id="+branch_state,
            success:function(res)
              {               
              if(res)
                {
                $("#branch_city").empty();
                $("#branch_city").append('<option>Select</option>');
                $.each(res,function(key,value)
                  {
                  $("#branch_city").append('<option value="'+key+'">'+value+'</option>');
                  });
                }
              else
                {
                $("#branch_city").empty();
                }
              }
        });
   }
  else
    {
    $("#branch_city").empty();
    $("#city").empty();
    }      
  });
$('#company_state').on('change',function()
 {
 var company_state = $(this).val();    
 if(company_state)
   {
   $.ajax({
          type:"GET",
          url:get_city+"?id="+company_state,
          success:function(res)
           {              
           if(res)
             {
             $("#company_district").empty();
             $("#company_district").append('<option value="">Select City</option>');
             var b = $('#company_district').attr('data-company_district'); 
             $.each(res,function(key,value)
               {
                var x = "";
                if(b === key)
                  {
                  x="selected";
                  }  
               $("#company_district").append('<option value="'+key+'"'+x+'>'+value+'</option>');
               });
             }
           else
             {
             $("#company_district").empty();
             }
           }
       });
   }
 else
   {
   $("#company_district").empty();
   }
 });
 
 function reset()
 {
     $("#company_district").empty();
     $("#company_district").append('<option value="">Select City</option>');
 }
</script>
    
<script src="{{asset('assets/material/js/toastr.js')}}"></script>
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/toast-master/js/jquery.toast.js')}}"></script>
<!-- start - This is for export functionality only -->
<script src="{{asset('assets/material/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/material/js/parsley.js')}}"></script>

<script src="{{asset('module.js/Company/index.js?ver=4')}}"></script>
<script src="{{asset('module.js/main.js?ver=20')}}"></script>
@endsection
