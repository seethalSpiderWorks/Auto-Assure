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
    a.disabled {
        pointer-events: none;
        cursor: default;
    }

</style>
  <?php // $option1 = json_decode($option->opset_options);
  //if(in_array('1',$option1) || in_array('2',$option1)){ ?>
  
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Users</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Manage User</li>
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
									
									<input type='hidden' name='_token' value='{{csrf_token()}}'> 
										<div class="row"> 

                                            <div class="col-md-3" > 
												<div class="mb-3">
													<label class="form-label">First Name<span class="star_required" style="color:red">*</span></label>
													<input type='hidden' name='edit_id' id="edit_id">
													<input type="text" name="user_fname" required='required' data-parsley-required-message='First Name Required' class='form-control' id='user_fname' data-parsley-minlength='3' onkeypress='alphaOnly(this)' placeholder="First Name"> 
													<span id="user_name-parsley-error"></span>
													<span class="highlight"></span>
												</div>
											</div>

											<div class="col-md-3">
												<div class="mb-3">
													<label class="form-label">Last Name</label>
													 
													{!! html()->text('user_lname','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Last Name Required', 'class'=>'form-control', 'id'=>'user_lname','onkeypress'=>'alphaOnly(this.value)'])!!}  													
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3" > 
												<div class="mb-3">
													<label class="form-label" style="">Email <span class="star_required" style="color:red">*</span></label>
													{!! html()->email('user_email','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Email Required', 'class'=>'form-control', 'id'=>'user_email'])!!}
													<span id="user_name-parsley-error"></span>
													<span class="highlight"></span>
													<!--<label>Email<span class="star_required">*</span></label>-->
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label class="form-label">Mobile<span class="star_required" style="color:red">*</span></label>
													{!! html()-> number('user_mobile','')->attributes(['required'=>'required','placeholder'=>"",'data-parsley-required-message'=>'Mobile Number Required', 'data-parsley-length'=>'[0,11]', 'data-parsley-minlength'=>'8', 'data-parsley-minlength-message'=>'Invalid Mobile No.' ,'class'=>'form-control', 'id'=>'user_mobile','oninput'=>"this.value = this.value.replace(/[^0-9]{10}/g, '').replace(/(\..?)\../g, '$1');"])!!}   
													                  
													<span id="user_mobile-parsley-error"></span>
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label class="form-label">Address</label>
													{!! html()->textarea('user_address','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Address Required', 'class'=>'form-control', 'id'=>'user_address', 'rows'=>"1", 'cols'=>"5", "style"=>"resize:none"])!!} 
													          
													<span class="highlight"></span>
												</div>
											</div> 
											
											<div class="col-md-3">
												<div class="mb-3">
													<label class="form-label">Designation</label>
													{!! html()->text('user_designation','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Designation Required', 'class'=>'form-control', 'id'=>'user_designation', 'onkeypress'=>'alphaOnly(this.value)'])!!}  		
													<span class="highlight"></span>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="mb-3">
													<label class="form-label" for="formrow-email-input">Image <span style="color:red;font-size:87.5%">&nbsp;(Size:400*500)</span> </label>
													<!-- <input type="file" name='branch_logo' class='form-control' id='branch_logo'> -->
													{!! html()->file('user_img')->attributes(['placeholder'=>"", 'data-parsley-required-message'=>'Logo required', 'class'=>'form-control', 'id'=>'user_img'])!!}
													<div id="image1" style="text-align:right"><span class="highlight" style="color:red;font-size:87.5%;"></span></div>
												</div>
											</div>
											 
											<div class="col-md-3">
												<div class="mb-3">
												<label>Privilage </label>
													<select name="user_privilage" id='user_privilage' class='form-select form-control' required='required' data-parsley-errors-container='#user_privilage-parsley-error' data-parsley-required-message='Privilege Required'>
														<option value="">Select Privilage</option>    
														@foreach($privilage as $pre)
															<option value="{{ $pre->id }}">{{ $pre->privilege_name }}</option>           
														@endforeach
													</select>
												<span class="highlight"></span>                        
											  </div>
											</div> 
											
											<div class="col-md-3">
												<div class="mb-3">
												<label> Company</label>
													<select name="user_company" id='user_company' class='form-select form-control' required='required' data-parsley-errors-container='#user_company-parsley-error' data-parsley-required-message='Company Required'>
														<option value="">Select Company</option>    
														@foreach($company as $comp)
														<?php 
															if($comp->company_id == 1)
																$selected = 'selected';
															else
																$selected = '';
														?>
														<option value="{{ $comp->company_id }}" {{ $selected }}>{{ $comp->company_name }}</option>           
														@endforeach
													</select>
												<span class="highlight"></span>                        
												</div>
											</div>  
											
                                        @if(Auth::user()->previlage == 2)   
											
											<div class="col-md-3">
												<div class="mb-3 mt-4" style="margin-left: 60px;">
													<div class="form-check form-check-right">
														 <input class="form-check-input" type="checkbox" id="multiple_branch" name="multiple_branch" value="1">
                                                        <label class="form-check-label" for="multiple_branch">Need Multiple Branch</label>
													</div>
												</div>
											</div>
											
											<div class="col-md-3" id="center_div_main">
												<div class="mb-3">
												<label> Centre</label>
													<select name="user_branch" id='user_branch' class='form-select form-control' required='required' data-parsley-errors-container='#user_branch-parsley-error' data-parsley-required-message='Branch Required'>
														<option value="">Select Centre</option>    
														@foreach($branch as $val)
														<option value="{{ $val->branch_id }}">{{ $val->branch_name }}</option>           
														@endforeach
													</select>
													<span class="highlight"></span>                        
												</div>
											</div> 
											
											 <div class="col-md-3" style="display:none;" id="center_div" >
												<div class="mb-3">
												<label> Centre</label>
													<select name="user_branch[]" id='user_branch1' class="select2 form-control select2-multiple"  data-parsley-errors-container='#user_branch-parsley-error' data-parsley-required-message='Branch Required'  multiple="multiple" data-placeholder="Select Centre">
														<option value="">Select Centre</option>    
														@foreach($branch as $val)
														<option value="{{ $val->branch_id }}">{{ $val->branch_name }}</option>           
														@endforeach
													</select>
													<span class="highlight"></span>                        
												</div>
											</div> 

                                        @else
                                            <input type="hidden" name="user_branch[]" id="user_branch" value ="{{ $centre }}" /> 
                                        @endif
											
											<div class="col-md-3 user_password"> 
												<div class="mb-3">
													<label> Password</label>
													<?php //$user_password = '123456'; ?> 
													{!! html()->text('user_password','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Password Required', 'class'=>'form-control date_picker_first', 'id'=>'user_password', 'data-parsley-errors-container'=>'#user_password-parsley-error'])!!}  	
													<span id="user_password-parsley-error"></span>                    
													<span class="highlight"></span>
												</div>
											</div>
										
										<div class="col-md-3">
											<div class="flex-wrap gap-3 mb-3 editButton" style="margin-top:10%;display:none;">
												<a href="#" class="btn btn-primary waves-effect waves-light w-md" onclick="createOrUpdate('{{URL::to("users/edit_user")}}', 'createForm', 'update-modal', 'usersDataTable','{{URL::to("users")}}')" class="btn btn-info btn-block">
													<!--i class="fas fa-pencil-alt"></i-->Update </a>
											</div>
											<div class="flex-wrap gap-3  saveButton" style="margin-top:10%">
												<a href="#" class="btn  btn-primary waves-effect waves-light" onclick="createOrUpdate('{{URL::to("users/add_users")}}', 'createForm', 'add-modal', 'usersDataTable','')" class="btn btn-info btn-block">
													<!--i class="fas fa-save" title="Save"></i--> Submit </a>&nbsp;
												<a href="#" onclick="clearForm()" class="btn btn-danger"> Reset</a>
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
                                        <!--h4 class="card-title">View Users</h4>
                                        <p class="card-title-desc"></p-->
        							<div class="table-responsive">
                                        <table id="usersDataTable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width:100%">
											<thead>
												<tr>
												  <th class="sno" style="width:5% !important;">#</th>
												  <th>User ID</th>
												  <th>Username</th>
												  <th>First Name</th>
												  <!--th>Last Name</th-->
												  <th>Mobile</th>
												  <th style="width:10% !important;">Email</th>
												  <th style="width:10% !important;">Privilege</th>
												  <th style="width:10%!important; word-wrap: break-word;">Added By</th> 
												  <!--th>Added By </th-->
												  <th class="actionwidth">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
        							 </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
			
<!--------------------- Delete modal ------------------->   						
		<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}
 						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_id' id="del_id" value=''> 
						 <p>Are you sure,You want to delete the User?</p>
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
    <!--------------------- View modal ------------------->     
    <div id="view_modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">View Details</h5>
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
    
    <!----------------- pswdreset Modal ------------------------------>
	<div class="modal fade" id="change_mypass_user" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reset Password</h4>
                    <!--button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button-->
					<button type="button" onclick="close_reload()" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
					{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','class'=>'myform'])->open() !!}
                     <input type='hidden' name='_token' value='{{csrf_token()}}'>
                    <input type='hidden' name='user_id_mypasschange' id="user_id_mypasschange" value=''>
                    <div class="form-group col-md-12">
                        <div class="col-md-12" style="margin-bottom:10px;display:none">
                            <div class="group material-input">
							<label style="margin-left:10px;">Current Password</label>
								<input type="password" name="user_mypass_current" class="form-control date_picker_first" id="user_mypass_current" data-parsley-required-message="Password Required" data-parsley-errors-container="#user_mypass_current-parsley-error" >
                                <span style="display:none;color:red;font-size:10px" id="user_mypass_current-parsley-error">Required Field</span>
                                <span class="highlight"></span>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom:10px">
                            <div class="group material-input">
							<label style="margin-left:10px;">New Password</label>
                                <input type="password" name="user_mypass_new" class="form-control date_picker_first" id="user_mypass_new" data-parsley-required-message="Password Required" data-parsley-errors-container="#user_mypass_new-parsley-error" required>
                                <span style="display:none;color:red;font-size:10px" id="user_mypass_new-parsley-error">Required Field</span>
                                <span class="highlight"></span>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom:10px">
                            <div class="group material-input">
							<label style="margin-left:10px;">Confirm Password</label>
                                <input type="password" name="user_mypass_conf" class="form-control date_picker_first" id="user_mypass_conf" data-parsley-required-message="Password Required" data-parsley-errors-container="#user_mypass_conf-parsley-error" required>
                                <span style="display:none;color:red;font-size:10px" id="user_mypass_conf-parsley-error">Required Field</span>
                                <span class="highlight"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" onclick="close_reload()" data-dismiss="modal">Close</button>
                        <button type="button" id="btn-closing-datepass" onclick="Change_My_Password1()" class="btn btn-success">Confirm</button>
                    </div>
                    {!! html()->form()->close() !!}
                </div>
            </div>
        </div>
    </div>

 @endsection
 
 @section('js')
<!-- Required datatable js -->
<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>  
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script type="text/javascript">
  var url_userDataTable ='{{URL::to("users/getuserDatatable")}}';
  var url_userview      = '{{URL::to("users/user_view")}}';  
  // var url_resetpswd  = '{{URL::to("users/resetpswd")}}';  
  //var url_profile1    = '{{URL::to("users/profile1")}}';
  var url_edituser      = '{{URL::to("users/getUsers")}}';  
  var url_deleteuser    =  '{{URL::to("users/deleteUser")}}';
  var url_passwordreset =  '{{URL::to("users/resetUser")}}';
  var url_get_branch    = '{{URL::to("users/get_branch")}}';
  var assigndept        = '{{URL::to("users/assigndept")}}';
</script>
<script src="{{asset('module.js/main.js?ver=1.9')}}"></script>
<script src="{{asset('module.js/Users/index.js?ver=1.41')}}"></script>

<script type="text/javascript">
$( document ).ready(function() 
{
    $('#user_type').trigger('change');
});

$('#user_type').change(function()
{
    var show = $(this).val();
    if(show==10)
    { 
        // $('#dep').show();
          $('#dep1').show();
        $('#ddd').hide();
    }
    else{
        $('#dep1').hide();
        $('#ddd').show();
        
    }
});
  
$(document).ready(function() {
  $('#user_company').on('change', function() {
      var company_id = this.value;
      $.ajax({
        url: url_get_branch,
        type: "POST",
        data: {
          'company_id': company_id,'_token':token
        },
        cache: false,
        success: function(dataResult){
          $("#user_branch").html('');
          $.each(dataResult,function(i,value)
          {
              $("#user_branch").append('<option value="'+value.branch_id+'">'+value.branch_name+'</option>');
          });
          
        }
      });
  });
});
	
<!------------ Pswd reset ------------->
    function Change_My_Password1() 
	{
        var token = $('meta[name="csrf-token"]').attr('content');
        var id = $('#user_id_mypasschange').val();
        //var user_mypass_current = $('#user_mypass_current').val();
        var user_mypass_new = $('#user_mypass_new').val();
        var user_mypass_conf = $('#user_mypass_conf').val();
        var url_resetpswd = '{{URL::to("users/resetpswd")}}';
        
       // if(user_mypass_current === '')
       // {
          // $("#user_mypass_current-parsley-error").show(); 
       // }
        if(user_mypass_new === '')
        {
           $("#user_mypass_new-parsley-error").show();  
        }
        if(user_mypass_conf === '')
        {
           $("#user_mypass_conf-parsley-error").show();  
        }
        
        if(user_mypass_conf !== '' && user_mypass_new !== '' )
        {
            $.ajax({
            type: 'POST',
            data: { '_token': token, 'id': id, 'user_mypass_new': user_mypass_new, 'user_mypass_conf': user_mypass_conf },
            url: url_resetpswd,
            success: function(result) {
                if (result.status == 1) 
				{
					Command: toastr["success"]("Password Changed Successfully!")
					toastr.options = {
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
					
                    $('#change_mypass').modal('hide');
                }
				else {
					Command: toastr["error"]("Password Does Not Match!")
					toastr.options = {
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
				
                }
                
              location.reload();
            }
          }) 
        }
    }
</script>

<script>
$(document).ready(function() {
	
  $('#multiple_branch').on('change', function() {
	   if(this.checked) {
			$('#center_div_main').hide();
			$('#center_div').show();
			
			$('#user_branch1').attr('required','required');
			$('#user_branch').removeAttr('required');
			$("#user_branch1").select2();
	   }else{
		   $('#center_div_main').show();
		   $('#center_div').hide();
		   
		   $('#user_branch').attr('required','required');
		   $('#user_branch1').removeAttr('required');
	   }
  });
});
</script>

@endsection     
