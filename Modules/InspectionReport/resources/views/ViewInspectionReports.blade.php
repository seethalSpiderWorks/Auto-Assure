@extends('layouts.myfudapp')
@section('content')
 
<div class="page-content">
	<div class="container-fluid">

		<!-- start page title -->
		<div class="row">
			<div class="col-12">
				<div class="page-title-box d-flex align-items-center justify-content-between">
					<h4 class="mb-0">View Inspection Reports</h4>
                    <div class="page-title-right">
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
							<li class="breadcrumb-item active"> View Inspection Reports </li>
						</ol>
                   </div>
                </div>
			</div>
		</div> <!-- end page title -->                      

		<div class="row"> 	</div>
   
		<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!--  <h4 class="card-title">View clients</h4>
                        <p class="card-title-desc"></p>-->
                        <div class="table-responsive">  
                            <table id="viewReportDataTable" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{ __('#')}}</th>
                                        <th>{{ __('Reference')}}</th>
                                        <th>{{ __('Name')}}</th>
                                        <th style="width:5%">{{ __('Date of Inspection')}}</th>
                                        <th>{{ __('Plate No')}}</th>
                                        <th>{{ __('Current Status')}}</th>
                                        <th>{{ __('Expired Status')}}</th>
                                        <!-- <th>{{ __('Added By')}}</th> -->
                                        <th style="text-align:center">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
	var url_DivisionDataTable = '{{URL::to("viewInspectionreports/viewReportDatatable")}}';
 	var url_viewReport        = '{{URL::to("inspectionreport/viewInspectionReport")}}';
	var url_deleteReport      = '{{URL::to("inspectionreport/deleteInspectionReport")}}';
</script>
  	
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
   
<script src="{{asset('module.js/main.js?ver=1')}}"></script>
<script src="{{asset('module.js/InspectionReport/indexView.js?ver=2.5')}}"></script>
 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"/></script>
 
@endsection