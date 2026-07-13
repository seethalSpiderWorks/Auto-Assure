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
// if(in_array('1',$option1) || in_array('2',$option1)){
?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Branch</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Manage Branch</li>
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
								    {!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'createForm', 'class'=>'myform', 'files'=>'true'])->open() !!}
									    <input type='hidden' name='edit_id' id='edit_id'> 
 									    <input type='hidden' name='_token' value='{{csrf_token()}}'> 
										
										<div class="row"> 

											<div class="col-md-3">
												<div class="mb-3">
												    <label class="form-label">Company<span class="star_required" style="color:red"> *</span></label></label>	
													<select class="form-select form-control select2 " id="company_i" name="company_i" data-company="" data-parsley-required-message="Company Required" data-placeholder="Select ..."  required>
														<option value="">-- Select Company --</option>
														<?php 
														foreach ($company as $value) 
														{ ?>
														    <option value="<?=$value->company_id;?>" ><?=$value->company_name;?></option>
														    <?php 
														} ?>  
													</select>
													<span class="highlight" ></span>
												</div>
											</div>
											
											<div class="col-md-3" > 
												<div class="mb-3"> 
													<label>Branch Name<span class="star_required" style="color:red"> *</span></label></label>
													{!! html()->text('branch_name')->required()->placeholder('Branch Name')->attribute('data-parsley-required-message', 'Branch Name Required')->class('form-control')->id('branch_name') !!}														
													<span class="highlight"></span>
													<span id="branch_name-parsley-error"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Branch Code<span class="star_required" style="color:red"> *</span></label></label>
													 {!! html()->number('branch_code')->required()->placeholder('Branch Code')->attribute('data-parsley-required-message', 'maxlength', 10,'Branch Name Required')->class('form-control')->id('branch_code') !!}	
													<span class="highlight"></span>
													<span id="branch_code-parsley-error"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Contact Person</label>
													{!! html()->text('branch_person','')->attributes(['class'=>'form-control','id'=>'branch_person','onkeypress'=>'return alphaOnly(event)', 'placeholder'=>'Contact Person']) !!}
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Contact Mobile</label>
													{!! html()->text('branch_mob','')->attributes(['class'=>'form-control','id'=>'branch_mob','onkeypress'=>'return isNumberKey(event)', 'maxlength="10"', 'placeholder'=>'Contact Mobile'])!!}
													<span class="highlight"></span>
												</div>
											</div>
										
											<div class="col-md-3">
												<div class="mb-3">
												    <label>Contact Landline</label>
											        {!! html()->text('branch_lan','')->attributes(['class'=>'form-control','id'=>'branch_lan','onkeypress'=>'return isNumberKey(event)', 'maxlength="15"','placeholder'=>'Contact Landline'])!!}
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Whatsapp No.</label>
													{!! html()->text('branch_whatsapp','')->attributes(['class'=>'form-control','id'=>'branch_whatsapp','onkeypress'=>'return isNumberKey(event)', 'maxlength="15"','placeholder'=>'Whatsapp No.'])!!}
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Email</label>
													{!! html()->text('branch_email','')->attributes(['class'=>'form-control', 'id'=>'branch_email','placeholder'=>'Email'])!!}
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label>Website</label>
													{!! html()->text('branch_web','')->attributes(['class'=>'form-control', 'id'=>'branch_web','placeholder'=>'Website'])!!}
													<span class="highlight"></span>
												</div>
											</div>
											 
											<div class="col-md-3">
												<div class="mb-3">
													<label>Address</label>
													<textarea name="branch_address" id="branch_address" class="form-control" placeholder="Address" style="height:37px;resize: none;"></textarea>
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3" style=""><!--margin-top:-1%-->
												<div class="">
													<label class="form-label">PIN Code</label>
													{!! html()->text('branch_pincode','')->attributes(['class'=>'form-control', 'id'=>'branch_pincode', 'placeholder'=>'PIN Code','onkeypress'=>'return pin(event)'])!!}
													<span class="highlight"></span>
												</div>
											</div> 
											
										    <div class="col-md-3" style="margin-bottom:-12px">
    											<div class="mb-3">	
                                                    <div class="flex-wrap gap-3 mt-4 editButton" style="display:none;">
        												<a href="#" class="btn btn-primary waves-effect waves-light " onclick="createOrUpdate('{{URL::to("branch/edit_branch")}}', 'createForm', 'edit-modal', 'branchDatatable','')" class="btn btn-info btn-block">
        													<!--i class="fas fa-pencil-alt"></i-->Update
        												</a>
        											</div>
    											    <div class="flex-wrap gap-3 mt-4 saveButton" >
    													<a href="#" class="btn btn-primary waves-effect waves-light "  onclick="createOrUpdate('{{URL::to("branch/add_branch")}}', 'createForm', 'add-modal', 'branchDatatable','')" class="btn btn-info btn-block">
    														<!--i class="fas fa-save" title="Save"></i--> Save
    													</a>&nbsp;
    													<a href="#" onclick="clearForm()" class="btn btn-danger  waves-effect waves-light">
    														Reset
    													</a>
    											    </div>  
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
				<div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!--h4 class="card-title">View Centre</h4>
                            <p class="card-title-desc"></p-->
							<div class="table-responsive">
								<table id="branchDatatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
									<thead>
										<tr>
											<th class="sno">#</th>
											<th>Branch Name</th>
											<th style="text-align:center">Branch Code </th>
											<th style="text-align:center">Branch ID</th>
											<th style="text-align:center">Mobile</th>
											<th>Email</th>
											<th>Added By</th>
											<!--th>Logo </th-->
											<th class="actionwidth">Action</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
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
							{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'deleteForm','class'=>'myform'])->open() !!}
 							<input type='hidden' name='_token' value='{{csrf_token()}}'> 
							<input type='hidden' name='del_id' id="del_id" value=''> 
							 <p>Are you sure, You want to delete the Branch?</p>
							 {!! html()->form()->close() !!} 
						</div>
						<div class="modal-footer">
							<button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button>
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							
						</div>
					</div>
				</div>
			</div>

        </div><!-- container-fluid -->
    </div><!-- End Page-content -->
<?php // } ?>

    <!---------------------------  Extra Large modal --------------------->
    <div id="view_modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="view-modal-body"> </div>
				    <div class="modal-footer">
						<button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">Close</button>
					</div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!----------------------------------------------->

	<div id="view_modal_old" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    //  var token = $('meta[name="csrf-token"]').attr('content');
    var public_path         = '<?php echo url('/');?>';
    var url_branchDataTable = '{{URL::to("branch/get_datatable")}}';
    var url_editbranch      = '{{URL::to("branch/get_details")}}';
    var url_viewbranch      = '{{URL::to("branch/view_branch")}}';
    var url_deletebranch    = '{{URL::to("branch/delete_branch")}}';
</script>

<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>     
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<!-- init js -->
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
<!-- Datatable init js -->
<script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Branch/index.js?ver=1.1')}}"></script>

<script type="text/javascript">
$( document ).ready(function() 
  {
  $('#branch_country').trigger('change');
  });
	
$('#branch_country').change(function()
  {
  var country = $(this).val();
  if(country)
    { 
    $.ajax({
          type: 'GET',
          url: 'branch/get_state',
          data: { country: country },
          success:function(res)
            {              
            $("#branch_state").empty();
            $("#branch_state").append('<option value="">--- State ---</option>');
            $("#branch_district").empty();
            $("#branch_district").append('<option value="">--- District ---</option>');
            if(res)
              {
              var a = $('#branch_state').attr('data-state'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#branch_state").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#branch_state").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              $('#branch_state').trigger('change');
              }
            }
         });
    }
  else
    {
    $("#branch_district").empty();
    $("#branch_district").append('<option value="">--- District ---</option>');
    }      
  });
$('#branch_state').change(function()
  {
  var state = $(this).val();
  if(state)
    { 
    $.ajax({
          type: 'GET',
          url: 'branch/get_district',
          data: { state: state },
          success:function(res)
            {              
            $("#branch_district").empty();
            $("#branch_district").append('<option value="">--- District ---</option>');
            if(res)
              {
              var a = $('#branch_district').attr('data-district'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#branch_district").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#branch_district").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              }
            }
         });
    }
  else
    {
    $("#branch_district").empty();
    $("#branch_district").append('<option value="">--- District ---</option>');
    } 
  });

</script>
@endsection