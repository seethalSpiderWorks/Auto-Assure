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
                        <h4 class="mb-0">Blocked IP</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Blocked IP</li>
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
									{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'createForm','class'=>'myform'])->open() !!}
      
                                        <input type='hidden' name='_token' value='{{csrf_token()}}'> 
										<div class="row"> 
													
                                            <div class="col-md-3" > 
												<div class="mb-3">
													<label class="form-label">IP Address
														<!-- <span class="star_required" style="color:red">*</span> -->
													</label>
												<input type='hidden' name='edit_id' id="edit_id">								
 
												{!! html()->text('ip_address','')->attributes(['required'=>'required','placeholder'=>"",'data-parsley-required-message'=>'IP Address required','class'=>'form-control','id'=>'ip_address','data-parsley-minlength'=>'10'])!!}		

														<span id="user_name-parsley-error"></span>
													<span class="highlight"></span>
												</div>
											</div>	
									
											<div class="col-md-2 saveButton" style="margin-top:25px;">
												<div class="mb-3">
													<a href="#" onclick="SearchIp()" class="btn btn-info btn-block">Filter</a> &nbsp;
													<a href="#" onclick="clearForm()" class="btn btn-danger waves-effect waves-light"><i class="fa fa-refresh"></i>Reset</a>
												</div>
											</div>
											
									</div>
								
							</div>
        							<div style="" class="table-responsive">
                                        <table id="ipaddressDataTable" class="mytable table table-bordered table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
											<thead>
												<tr>
													<th style="width:3px">#</th>
													<th style="text-align:center">Date & Time</th>
													<th>IP Address</th>
													<th style="text-align:center">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
					 				</div>
					 
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

<div class="modal fade" id="unblock_ip" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header"> 
                        <h5 class="modal-title" id="staticBackdropLabel">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {!! html()->form('POST')->attributes(['url' =>'','method'=>'post'])->open() !!}
						 <input type='hidden' name='_token' value='{{csrf_token()}}'> 
						 <input type='hidden' name='ip_id' id="ip_id" value=''> 
						 <p id="unblockip" style="color:green"></p>
						 
                    </div>
                    <div class="modal-footer">
                    	<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    	<button type="button" id="btn-closing-date"  onclick="unlockIp()" class="btn btn-success">Unblock</button>                                </div>
                {!! html()->form()->close() !!}
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
    var url_ipaddressDataTable ='{{URL::to("blockip/getDatatable")}}';
    var url_unblockIp  = '{{URL::to("blockip/unblockIp")}}';
 
</script>

<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Security/index.js?v3.2')}}"></script>

@endsection