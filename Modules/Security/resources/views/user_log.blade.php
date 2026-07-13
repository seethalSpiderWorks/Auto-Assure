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
                        <h4 class="mb-0">User Log / Activity Log</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <!--<li class="breadcrumb-item active"><a href="javascript: void(0);">All User Log</a></li>
                                <li class="breadcrumb-item active">Manage User Log</li>-->
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
										
											<div class="col-md-2"> 
												<div class="mb-3">
													<input type="date" class="form-control datetimepicker-input" placeholder="From Date" id="from_date" data-toggle="datetimepicker" data-target="#from_date" value="<?php echo session('from'); ?>" onchange="filtering(this.value,to_date.value)">
												</div>
											</div>
											<div class="col-md-2"> 
												<div class="mb-3">
													<input type="date" class="form-control datetimepicker-input"  placeholder="To Date" id="to_date" data-toggle="datetimepicker" data-target="#to_date" value="" onchange="filtering(from_date.value,to_date.value)">
												 </div>
											</div>
                        
											<div class="col-md-2"> 
												<div class="mb-3"> 
													<button type="button" class="btn btn-danger waves-effect waves-light" onclick="filtering('unset')"> Reset</button>
												</div>
											</div>
										<div style="margin-top:1%" class="table-responsive">	
											
                                        <table id="branchDataTable1"  class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
											<thead>
												<tr>
												    <th class="sno" style="text-align:center;">#</th>
                               					    <th style="text-align:center;">Date</th>
													<th style="text-align:center;">Time</th>
													<th>Created IP</th>
													<th>Added By</th>
													<th>Activity</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
										</div>	
										</div>
										
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
					
		<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete Course</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'confirmForm', 'class'=>'myform'])->open() !!}	  
					
						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_id' id="del_id" value=''> 
						 <p>Are you sure,You want to delete the Course?</p>
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

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
       
 <div id="view_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myModalLabel">View Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                </button>
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

<!-- Required datatable js -->
        <script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>				
        <!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>      
	   	<!-- parsleyjs -->
		<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>
		<script src="{{asset('module.js/main.js?ver=1.2')}}"></script>          
       
<script type="text/javascript">
  var url_userDataTable ='{{URL::to("userlog/getDatatable")}}';
  var url_userview  = '{{URL::to("course/course_view")}}';   
  var url_edituser  = '{{URL::to("course/getCourse")}}';  
  var url_deleteuser =  '{{URL::to("course/deletecourse")}}';
  var url_passwordreset =  '{{URL::to("users/resetUser")}}';
  var url_get_branch = '{{URL::to("users/get_branch")}}';
  var assign_cert = '{{URL::to("course/assign_cert")}}';
  var assigndept = '{{URL::to("users/assigndept")}}';
</script>
<!-- parsleyjs -->
<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>	
<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/UserLog/index_log_view.js?ver=9.6')}}"></script>

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
</script>

<script>
    function filtering(from_date,to_date)
	{
        var token = $('meta[name="csrf-token"]').attr('content');    
        date=from_date;
		
	 	if(from_date=='unset')
		{
		  var date="";
		}
		
        $.ajax({
        type: 'post',
        url: "userlog/filter",
	    data:{'_token':token,'from_date':from_date,'to_date':to_date},
        success: function (data) 
		{
			$('#branchDataTable1').DataTable().ajax.reload();
			
		  if(from_date=='unset')
		  {
		      window.location.reload();
		  }

        }
    })
  } 
 </script>
  @endsection     
