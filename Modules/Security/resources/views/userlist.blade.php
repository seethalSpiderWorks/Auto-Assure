@extends('layouts.myfudapp')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Blocked/Unblocked User List</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}"> Home</a></li>
                                <li class="breadcrumb-item active">Block/Unblocked Users</li>
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
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'createForm','class'=>'myform'])->open() !!}	  
 					<div class="row">  
						<div class="col-md-3">
							<div class="mb-3">
								<label>User ID</label>
								{!! html()->text('user_id','')->attributes(['required'=>'required','placeholder'=>"User ID",'data-parsley-required-message'=>'User ID required','class'=>'form-control','id'=>'user_id'])!!}	
								
 								<span class="highlight"></span>
							</div>
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label>User Name</label>
								{!! html()->text('user_name','')->attributes(['required'=>'required','placeholder'=>"User Name",'data-parsley-required-message'=>'User Name required', 'class'=>'form-control', 'id'=>'user_name','data-parsley-minlength'=>'3'])!!}	
							<span class="highlight"></span>
							</div>
						</div>

						<div class="col-md-2" style="margin-top:-1px;">
							<div class="mb-3">
							 <label>Status</label>
							 <select class ="form-control form-select select2 status" id="status" name="status"  >
								<option value="">Select Status</option>
									<option value="all">Select All</option>
									<option value="0">  Un Blocked</option>
									<option value="2">  Blocked</option>
							</select>
							 <span class="highlight"></span>
							</div>
						</div>

						<div class="col-md-2" style="margin-left:18px;margin-top:25px">
							<div class="mb-0">
								<a href="#" onclick="searchUser()" class="btn btn-info btn-block saveButton">Search</a> &nbsp;
								<a href="#" onclick="clearForm()" class="btn btn-danger waves-effect waves-light cancelButton">Reset</a>
							</div>
						</div>
						<!-- <div class="col-md-2 cancelButton"></div> -->
					                  
					</div>
					{!! html()->form()->close() !!}

					<div class="table-responsive">
						<table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                             <thead>
                                  <tr>
										<th style="text-align:center;width:4px;">#</th>
										<th style="text-align:center">ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Privilege</th>
                                        <th>Action</th>
                                   </tr>
                            </thead>
							<tbody></tbody>
						 </table>
					</div>
				</div><!-- End Form -->
			</div>
		</div>
	</div>

                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
          
<div class="modal fade" id="unblock_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Block/Unblock User </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'confirmForm','class'=>'myform'])->open() !!}	  
			
				<input type='hidden' name='_token' value='{{csrf_token()}}'> 
				  <input type='hidden' name='user_id' id="user_id" value=''> 
				  <input type='hidden' name='status' id="status" value=''> 
					<p id="blktxt"> </p>
				{!! html()->form()->close() !!}
			</div>
			<div class="modal-footer">
				<button type="button" onclick="confirmBlock()" class="btn btn-primary">Confirm</button>
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

 @endsection
 
 @section('js')
<script type="text/javascript">
  var public_path = '<?php echo url('/');?>';
  var url_dataTable = '{{URL::to("blockuser/getDatatable")}}';
  var url_unblockuser = '{{URL::to("blockuser/block_orunblock")}}';
</script>

<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>     
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Security/user_block.js?ver=1')}}"></script>

  @endsection     
