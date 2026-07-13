@extends('layouts.myfudapp')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Followup</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}"> Home</a></li>
                                <li class="breadcrumb-item active">Followup</li>
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
										<input type="date" class="form-control datetimepicker-input"  placeholder="From Date" id="from_date" data-toggle="datetimepicker" data-target="#from_date" value="<?php echo session('filter_lead_fdate'); ?>" onchange="filtering(this.value,to_date.value,filter_source.value)">
									</div>
								</div>
								<div class="col-md-2"> 
									<div class="mb-3">
										<input type="date" class="form-control datetimepicker-input"  placeholder="To Date" id="to_date" data-toggle="datetimepicker" data-target="#to_date" value="<?php echo session('filter_lead_ldate'); ?>" onchange="filtering(from_date.value,to_date.value,filter_source.value)">
									</div>
								</div>
								<!----------------- Staff ----------------->
								<div class="col-md-2"> 
									<div class="mb-3">
										<?php 
										$flr_staff='';
										if(@session('filter_staff'))
										$flr_staff = session('filter_staff'); ?>
										{!! html()->select('filter_staff', $users , $flr_staff)->attributes([ 'class'=>'form-control select2','id'=>'filter_staff','required'=> 'required','placeholder'=>'Select Staff'])->placeholder('Select Staff') !!}
									</div>
								</div>
								<!----------------- Source ----------------->
								<div class="col-md-2"> 
									<div class="mb-3">
										<?php $flr_source='';
										if(@session('filter_lead_source'))
							 		    $flr_source = session('filter_lead_source'); ?>
										{!! html()->select('filter_source', $sources , $flr_source)->attributes([ 'class'=>'form-control select2','id'=> 'filter_source', 'placeholder'=>'Select Source','required'=> 'required',"onchange"=>"filtering(from_date.value,to_date.value,filter_source.value)"])->placeholder('Select Source') !!}
									</div>
								</div>

								@include("leads::filter_form")                          
								<div class="col-md-2" style="margin-bottom:10px;"> 
									<div class="mb-3">
										<button type="button" class="btn btn-danger waves-effect waves-light" onclick="filtering('unset')"> Reset</button>
									</div>
								</div>

							</div>
							<?php 
								$current_route = \Route::current()->uri(); 
								$privilage = Auth::user()->previlage;
							?>
		
			                <!---- Today's Followup -------->
                			<div class="accordion mb-3" id="accordionExample">
                				<div class="accordion-item">
                					<h2 class="accordion-header" id="heading1">
                					    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                						    <?php  echo "Today's Followup"; ?>
                					    </button>
                					</h2>
                					  
                					<div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                					    <div class="accordion-body">
                				
                							<div style="margin-top:-10px" class="table-responsive">
                								<table id="lead_table" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                									<thead>
                										<tr>
                											<th class="sno">#</th>
                											<th style="text-align:center;" width="9%!important">Next Followup Date</th>
                											
                											<th>ID</th>
                											<th>Name</th>
                											<th style="text-align:center;">Mobile</th>
                											
                											<th>Source</th>
                											<!-- <th>Campaign</th> -->
                											<th>Remarks</th>
                											<th>Staff</th>
                											<!--th>Assign Date</th-->
                											<th>Status</th>
                											<th class="actionwidth">Action</th>
                										</tr>
                									</thead>
                									<tbody> </tbody>
                								</table>
                							</div>
                				 	    </div>
                			        </div>
                		        </div>
	                            <!---------------------- All Followup -------------------->	
                			    <div class="accordion-item">
                					<h2 class="accordion-header" id="heading2">
                    					<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                    					    <?php  echo "All Followup"; ?>
                    					</button>
                					</h2>
                				
                					<div id="collapse2" class="accordion-collapse show " aria-labelledby="heading2" data-bs-parent="#accordionExample">
                					    <div class="accordion-body">
                		
                							<div style="margin-top:-10px" class="table-responsive">
                								<table id="lead_table_all" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                									<thead>
                										<tr>
                											<th class="sno">#</th>
                											<th style="text-align:center;" width="9%!important">Next Followup Date</th>
                											<th>ID</th>
                											<th>Name</th>
                											<th style="text-align:center;">Mobile</th>
                											<th>Source</th>
                											<th>Remarks</th>
                											<th>Staff</th>
                											<!--th>Assign Date</th-->
                											<th>Status</th>
                											<th class="actionwidth">Action</th>
                										</tr>
                									</thead>
                									<tbody> </tbody>
                								</table>
                							</div>
                				 	    </div>
                			        </div>
                		        </div>
	                            <!---------------------- All Followup -------------------->		
	                        </div> <!-- accordion -->
				  
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

        </div> <!-- container-fluid -->
    </div><!-- End Page-content -->
    
    <!----------------------- Delete confirmation modal ----------------------->
	<div class="modal fade show" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" style="display: none" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure want to delete the lead?<span id="delete_lead"></span> ?
                    <input type="hidden" id="del_lead_id" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="deleteLead(del_lead_id.value)">Delete</button>
                </div>
            </div>
        </div>
	</div>         
    <!----------------------- Delete confirmation modal ----------------------->       
    
    <!---------------------------- followup modal ----------------------------->
	<div class="modal fade bs-example-modal-xl" id="modal_followup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel"  aria-modal="true" style="display:none">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document" >
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"> </span></button>
                </div>
                <div class="modal-body">
                    <div id="view_modal_body"> </div> <!-- lead_popupview -->
                        
                        <div class="card">
                            <div class="card-body">
                                <form id="followup_form">
                                    <input type="hidden" id="modal_lead_id" name="modal_lead_id">
                                    <input type="hidden" name="statususer" id="statususer" >
                            		<div class="row">
								        <div class="col-md-3">
                                    	    <div class="mb-3">   
                                                <label class="form-label" >Status<span class="text-red">*</span></label>
                                                <select name="follow_status" id="follow_status" required class="form-select form-control select2"  style="width:240px;" onchange="changeStatus(this.value,this.name)"  > </select>
                                   	        </div>
                                		</div>
										<!---------------------------------------->
										<div class="col-md-2" id="div_follow4" style="display:none">
											<div class="mb-2">  
											   <label class="form-label">Staff&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=""></span></label>
											  {!! html()->select('assign_staff', $users , null)->attributes([ 'class'=>'form-control select2','id'=> 'assign_staff', 'placeholder'=>'Select Staff','style'=>'width:160px', 'data-parsley-errors-container'=>"#error_assign"])->placeholder('Select Staff') !!}
											  <div id="error_assign"></div>
											</div>
										</div>    

										<div class="col-md-2" id="div_follow1" style="display:none">
											<div class="mb-3">
												<label class="form-label" >Next Followup Date<span class="text-red"></span></label>
												<input type="date"  value="" class="form-control datetimepicker-input " id="follow_next_date" name="follow_next_date" data-toggle="datetimepicker" data-target="#follow_next_date" style="">
											</div>
										</div>
											
										<div class="col-md-2" id="div_follow3" style="">
											<div class="mb-3">
												<label class="form-label">Comments<span class="text-red"></span></label>
											    <textarea name="followup_remark" id="followup_remark" class="form-control" style="height: 39px;resize:none"></textarea>
											</div>
										</div>
                                 
										<div class="col-md-1" id="btn_followup_div" style="margin-top:28px">
											<div class="mb-3">                                   
											  <button type="button" onclick="addFollowUp(modal_lead_id.value,follow_next_date.value,follow_status.value,followup_remark.value,assign_staff.value)"  class="btn btn-primary">Submit</button>
											</div>
										</div>   
                            		</div>
                                </form>
                        
								<div class="row">
									<div class="col-md-12">
										<div class="table-responsive">	
											<table id="followup_table_in" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
												<thead>
													<tr>
														<th>#</th>
														<th style="text-align:center">Date</th>
														<th style="text-align:center">Next Followup</th>
														<th>Comments</th>
														<th>Staff</th>
														<th>Assigned To</th>
														<th>Status</th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</div>
									</div>
								</div>
               				</div> <!-- card-body -->
           				</div> <!-- card -->
     			</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
             </div>
         </div>
    </div>  
    <!------------------------ view modal ---------------------->
    <!------------------------ Comments modal ---------------------->
    <div class="modal fade show" id="modal_comments" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel"  aria-modal="true" style="display:none">
        <div class="modal-dialog modal-lg mt-0 mb-0" role="document" >
            <div class="modal-content" style="width:980px">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterLabel">Comments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                </div>
                <div class="modal-body" id="comments_modal_body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

 @endsection
 
 @section('js')

<!-- Required datatable js -->
        <script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>				
		<!-- Buttons examples -->
        <script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
        <script src="{{asset('assets/libs/jszip/jszip.min.js')}}"></script>
        <script src="{{asset('assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
        <script src="{{asset('assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>            
        <!-- plugins -->
        <script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>       
        <!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>      
	   	<!-- parsleyjs -->
		<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>
		
		<script src="{{asset('module.js/main.js?ver=1.2')}}"></script>

	<script type="text/javascript">
		var public_path = '<?php echo url('/');?>';
		var url_view_followup = "{{url('/leads/set_lead_session')}}";
		var url_get_followup  = "{{url('/leads/get-followup')}}";
		var url_add_followup  = "{{url('/leads/add_followup')}}";
		var url_get_comments  = "{{url('/leads/get_comments')}}";
		var url_gettoday      = '{{URL::to("leads/gettoday")}}';
		var url_setfilter_campaign = "{{url('/leads/setFilterCampaign')}}";
       
       	var url ='<?php $current_route = \Route::current()->uri(); echo $current_route;?>';
		
		if(url=="myfollowup")
		{
		    var url_datatable_followup = "{{url('/myfollowup/get-list')}}";
			var url_datatable_followup_all = "{{url('/myfollowup/get-list-all')}}";
		}
       
        $(document).ready(function()
		{
			$('select').select2();
			 <?php
			if($export_option==1)
			{
				?>
				var exporting=1;
				<?php
			}
			else
			{
				?>
				var exporting=0;
				<?php
			} ?>
			var searchable = [];
			var selectable = []; 
			if(exporting==1)
			{
				var btns=[
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Leads',
                    header: true,
                    footer: true,
                    exportOptions: {
                         columns: [ 1, 2, 3, 4, 5],
                       
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Followup',
                    header: true,
                    footer: true,
                    exportOptions: {
                          columns: [ 0, 1, 2, 3, 4, 5],
                        
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Leads',
                    pageSize: 'A4',
                    header: true,
                    footer: true,
                    exportOptions: {
                         columns: [ 0, 1, 2, 3, 4, 5],
                       
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Leads',
                    // orientation:'landscape',
                    pageSize: 'A4',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                         
                          columns: function (idx, data, node) {
                                    if (node.innerHTML == "Action")
                                        return false;
                                        return true;
                                    },
                        stripHtml: false
                    }
                }
                ];
        }
        else
        {
            var btns=[];
        }

        $("#modal_followup").on('hide.bs.modal', function(){
        $("#lead_table").DataTable().ajax.reload( null,false );
    });
    });
    </script>
        <!--get role wise permissiom ajax script-->
        <script src="{{asset('module.js/Leads/followup.js?ver=3.5')}}"></script>
		<script src="{{asset('module.js/Leads/main.js?ver=1.2') }}"></script>  
		
<script>
	$(function(){
	  $('#follow_status').select2({
		dropdownParent: $('#modal_followup')
	  });
	}); 

	$(function(){
	  $('#assign_staff').select2({
		dropdownParent: $('#modal_followup')
	  });
	}); 
 
 function changeStatus(status,name)
 {     
	 //alert(status);
      $("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
      $("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
     
      $("#btn_followup_div").attr('class','col-md-3');
      $("#div_follow3").show();
      
      //$("#div_follow3").find('textarea').attr('required','required');
	 
      if(status=="1")
     {
         $("#div_follow1").show();
         $("#div_follow4").show();
        //  $("#div_follow1").find('input').attr('required','required');
         $("#div_follow4").find('select').attr('required','required');
     }
     else if(status=="2")
     {
         $("#div_follow1").show();
         $("#div_follow4").show();
         $("#div_follow4").find('select').attr('required','required');
     }
   
     else if(status=="3")
     {
         $("#div_follow1").show();
         //$("#div_follow4").show();
        // $("#div_follow4").find('select').attr('required','required'); 
     }
	 
	 else if(status=="4")
     {
         $("#div_follow1").hide();
         //$("#div_follow4").show();
        // $("#div_follow4").find('select').attr('required','required');
     }
     else if(status=="5")
     {
         //$("#div_follow4").show();
     }
     else if(status=="6")
     {
         //$("#div_follow4").show();
     }
     else
     {
        $("#div_follow1").hide();
     }
 } 

function viewFollowuppopup(id,status)
{
	//$('#followup_table_in').DataTable().ajax.reload(); 
    $.ajax({
        type: 'GET',
        url: url_view_followup,
        dataType:'html',
        data:{'id':id},
        success: function (data) 
		{
			$("#modal_followup").modal('show');
			$("#modal_lead_id").val(id);
			$("#view_modal_body").html(data);
			$("#statususer").val(status);
			  
			if(status == 0 )
			{
				$('follow_status').empty();
				jQuery.ajax({
						url : 'leads/followup_type_assign/',
						type : "GET",
						dataType : "json",
						success:function(data)
						{
							console.log(data);
							jQuery('select[name="follow_status"]').empty();
									
							$('select[name="follow_status"]').append('<option value="">-- Select --</option>');
							jQuery.each(data, function(key,value)
							{
								$('follow_status').empty();
								$('select[name="follow_status"]').append('<option name="'+ value +'" value="'+ key +'">'+ value +'</option>');
							});
						}
					});
			}
			else if(status !="" )
			{
				$('follow_status').empty();
				jQuery.ajax({
						url : 'leads/followup_type_reassign/',
						type : "GET",
						dataType : "json",
						success:function(data)
						{
							console.log(data);
							jQuery('select[name="follow_status"]').empty();
							$('select[name="follow_status"]').append('<option value="">-- Select --</option>');
							jQuery.each(data, function(key,value)
							{
								$('follow_status').empty();
								$('select[name="follow_status"]').append('<option name="'+ value +'" value="'+ key +'">'+ value +'</option>');
							});
						}
					});
			}
    
			$("#followup_table").DataTable().ajax.reload();
    	} ,
    	error:function(data)
    	{
    	   alert(data.responseText);
    	}
    });

	followup_in(id); 			
}
</script>

<script type="text/javascript">

function addFollowUp(id,date,status,assigned_user)
{
	var name = $('#follow_status option:selected').text();
	var staff = $("#assign_staff").val();
	var remarks = $("#followup_remark").val();
    $('#followup_form').parsley().validate();
   
    $.ajax({
            type: 'POST',
            url: url_add_followup,
            dataType:'json',
            data:{'id':id,'date':date,'status':status,'name':name,'_token':"{{csrf_token()}}",'assinged_user':assigned_user,'staff':staff,'remarks':remarks},

            success: function (data) 
		 	{
                if(data.status == 0) 
				{
					Command: toastr["success"](data.text)
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

					$('#followup_form')[0].reset();

					$('.select2').val(null).trigger("change");

					$("#followup_table").DataTable().ajax.reload();

					if(data.cu_status == "2" )
					{
					 $('follow_status').empty();
					 $('#follow_status').html('<option value="">Select</option><option value="Assigned">Assign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option>Registered</option><option >Maybe</option><option >Converted</option><option >Pickup and Reject</option><option >Less Likely</option>')

					}
					else if(status !="1" )
					{
						$('follow_status').empty();
						$('#follow_status').html('<option value="">Select</option><option  value="Reassigned">Reassign</option><option >Interested</option><option >Not Picked Up</option><option>Rejected</option><option >Registered</option><option>Maybe</option><option >Converted</option><option >Pickup and Reject</option><option >Less Likely</option>')
					}

					$("#followup_table").DataTable().ajax.reload();
				 }
				 else 
				 {
					Command: toastr["error"](data.text)
					toastr.options = {
					  "closeButton": true,
					  "debug": false,
					  "heading": "data.heading",
					  "text": "data.msg",
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
           } //success
     }); 
 }
</script>

<script type="text/javascript">
    function currentdate(today)
    {
		var token = $('meta[name="csrf-token"]').attr('content');
      
        $.ajax({
			type: 'get',
			data: {'_token':token, 'date': date},
			url: url_gettoday,
			success: function (data) {
					//$('#enqueryDataTable'+datetime).DataTable().ajax.reload();
					test(date);
                }
            }); 
    }
	
    function setFiltercampaign(campaign)
    {
       $.ajax({
                   type: 'GET',
                   url: url_setfilter_campaign,
                   dataType:'JSON',
                   data:{'campaign':campaign},
                   success: function (res) 
		   		   {
                    	$('#lead_table').DataTable().ajax.reload();
						$('#lead_table_all').DataTable().ajax.reload();
                   }
                });
    } 
</script>

<script>
    var url_setfilter_staff = '{{URL::to("leads/setFilterStaff")}}'; 
    var token = $('meta[name="csrf-token"]').attr('content');
    //function set_filter_staff(staff)
    //  {
    $('#filter_staff').on('change', function(e)
    {
		var staff = $(this).val();
		$.ajax({
                   type: 'POST',
                   url: url_setfilter_staff,           	
            	   data:{'_token':token,'staff':staff},
                   success: function (data) 
					{
						$('#lead_table').DataTable().ajax.reload();
						$('#lead_table_all').DataTable().ajax.reload();
					}
				});
    });
</script>

@endsection     