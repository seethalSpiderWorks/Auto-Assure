@extends('layouts.myfudapp')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">View Registrations </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}"> Home</a></li>
                                <li class="breadcrumb-item active"> Registration </li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
          
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
										 
							</div>
							<!----------------------- ----------------------->								
							<div style="margin-top:1px" class="table-responsive">
								<!--h4 class="card-title">Added Company</h4-->
								<!--p class="card-title-desc"></p-->
			
								<table id="viewRegDatatable" class="table table-bordered dt-responsive " style="border-collapse: collapse; border-spacing: 0; width: 100%;">  <!-- nowrap -->
									<thead>
										<tr> 
											<th class="sno">#</th>
											<th style="text-align:center" width="9%!important">Date</th>
											<th>Name</th>
											<th>Mobile</th>
											<th>Email </th>
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
						
		</div> <!-- container-fluid -->
	</div> <!-- End Page-content -->
 
	<!---------------------- Delete Registration -------------------------------->
	<div class="modal fade" id="delete_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Delete Registration</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}   
						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_reg_id' id="del_reg_id" value=''> 
						Are you sure want to delete the Registration <span id="delete_reg"></span>
					{!! html()->form()->close() !!} 
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="deleteLead(del_reg_id.value)">Delete</button>
					<!--button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button-->
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<!---------------------- Delete Registration -------------------------------->

 @endsection
 
 @section('js')
<!-- Required datatable js -->
<script src="{{asset('public/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<!-- Datatable init js -->
<script src="{{asset('public/assets/js/pages/datatables.init.js')}}"></script>
<!-- plugins -->
		
<script type="text/javascript">
	var public_path   = '<?php echo url('/');?>';
	var url_datatable = "{{url('registration/datatable')}}";
	var url_deleteReg = "{{url('registration/delete')}}";
</script>

<script src="{{asset('public/module.js/Registration/indexView.js?ver=1')}}"></script>	
<script src="{{asset('public/module.js/main.js?ver=1.3')}}"></script>
 
@endsection     

