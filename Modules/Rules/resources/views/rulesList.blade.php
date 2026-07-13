@extends('layouts.myfudapp')
@section('content')

	<div class="page-content">
        <div class="container-fluid"> 

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Rules</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Rules</li>
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
									{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'createForm','class'=>'myform'])->open() !!}
 									 
										<input type='hidden' name='privilege_id' id='privilege_id'> 
										<input type='hidden' name='_token' value='{{csrf_token()}}'> 
										<div class="row"> 
											
											<div class="col-md-3" > 
												<div class="mb-3">

												    <select name="privilege_name" id="privilege_name" class="form-control privilege_name" required="required" data-parsley-errors-container="#privilege_name-parsley-error" data-parsley-required-message="Privilege Name Required">
														<option value="" disabled selected>Pick a Privilege...</option>
														<?php 
														$rule = DB::table('privilege')
															->select('id', 'privilege_name')
															->where('status', '0')
															//->where('id','!=', '1')  //$privilegeData
															->orderBy('id')
															->get(); ?>
															 
														@foreach($rule as $val)
															<option value="{{ $val->id }}">{{ $val->privilege_name }}</option>
														@endforeach
													</select>
													<span id="privilege_name-parsley-error"></span>
													<span class="highlight"></span>
												</div>	
											</div>	

                                            <!--div class="col-md-2 saveButton"><a href="#" onclick="LoadResources('{{URL::to("rules/loadresources")}}', 'createForm', 'add-modal', 'ruleDataTable','{{URL::to("rules/")}}')" class="btn btn-info btn-block"><i class="fa fa-gear"></i>Set Rules</a></div>
											<div style="margin-left:-40px" class="col-md-2 cancelButton"><a href="#" onclick="clearForm()" class="btn btn-warning btn-block"><i class="fa fa-refresh"></i>Reset</a></div-->
											<div class="col-md-3" > 
												<div class="mb-3">
												    <div class="flex-wrap gap-3  saveButton" style="">
        												<a href="#" class="btn btn-primary waves-effect waves-light" onclick="LoadResources('{{URL::to("rules/loadresources")}}', 'createForm', 'add-modal', 'ruleDataTable','{{URL::to("rules/")}}')" class="btn btn-info btn-block" style="width:100px">
        													Set Rules </a>&nbsp;
													    <a href="#" onclick="clearForm()" class="btn btn-danger waves-effect waves-light" style="width:70px">Reset</a>
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
            </div><!-- End Form Layout -->

			<div class="" id="dataarea"> </div>			
			
			<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="staticBackdropLabel">Delete Privilege</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}
 							<input type='hidden' name='_token' value='{{csrf_token()}}'> 
							<input type='hidden' name='del_id' id="del_id" value=''> 
							 <p>Are you sure,You want to delete the Privilege?</p>
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

@endsection

@section('js')
<script type="text/javascript">
    // var url_privilegeDataTable ='{{URL::to("privileges/getDatatable")}}';
    var url_editprivilege   = '{{URL::to("privileges/getPrivilege")}}';
    var url_deleteprivilege =  '{{URL::to("privileges/deletePrivilege")}}';
</script>

<script type="text/javascript">
 $('body').on('click','.allocate_sub_menu,.allocate_main_menu',function(e){
               var privilege = $('#choosen_privilege').val();
	           var mainmenus = [];
	           var submenus = [];
			   
	  $("input:checkbox[name=main_data]:checked").each(function(){
                mainmenus.push($(this).val());
		});

		$("input:checkbox[name=sub_data]:checked").each(function(){
                submenus.push($(this).val());
		});
		
	    var token = $('meta[name="csrf-token"]').attr('content');
	 	$.ajax({
        url: '{{URL::to("rules/assignSubmenus")}}',
		type: 'POST',
        data: {privilege: privilege,submenus:JSON.stringify(submenus),mainmenus:JSON.stringify(mainmenus),_token:token },  //prop_pre_values:JSON.stringify(prop_pre_values)
       	async: false,
		cache: false,
		timeout: 30000,
		error: function(){
							return true;
						},
		success: function(data) { }
   }); 
	 
	 /* if($(this).is(':checked'))
            alert('checked'); */
});

$('body').on('click','.allocate_option_menu',function(e){
        var optionpriv_id = $(this).data('optionpriv_id');
        var optionmain_id = $(this).data('optionmain_id');
        var optionsub_id = $(this).data('optionsub_id');
        var option_id = $(this).val();
		
	    var token = $('meta[name="csrf-token"]').attr('content');
	 	$.ajax({
        url: '{{URL::to("rules/assignOptions")}}',
		type: 'POST',
        data: {optionpriv_id: optionpriv_id,optionmain_id: optionmain_id,optionsub_id: optionsub_id,option_id: option_id,_token:token },
       	async: false,
		cache: false,
		timeout: 30000,
		error: function(){
							return true;
						},
		success: function(data) {
			}
   }); 

});

 $('body').on('click','.select_all',function(e){
	    var check_status = $(this).is(":checked") ;
	//	alert(check_status);
        var selall_pri_id = $(this).data('selall_pri_id');
        var op_id = $(this).data('op_id');
	    var submenus = [];
	    var mainmenus = [];
		var token = $('meta[name="csrf-token"]').attr('content');		
		  $("input:checkbox[name=main_data]:checked").each(function(){
						mainmenus.push($(this).val());
				});
          $("input:checkbox[name=sub_data]:checked").each(function(){
                submenus.push($(this).val());
		  });
		  
	 	$.ajax({
        url: '{{URL::to("rules/assignallOptions")}}',
		type: 'POST',
        data: {selall_pri_id: selall_pri_id,op_id: op_id,check_status: check_status,submenus:JSON.stringify(submenus),mainmenus:JSON.stringify(mainmenus),_token:token },
       	async: false,
		cache: false,
		timeout: 30000,
		error: function(){
							return true;
						},
		success: function(data) {
				  if( check_status == true ) {
					$(":checkbox[name='option_data"+op_id+"']").attr("checked", true);
				  }
					else {
						$(":checkbox[name='option_data"+op_id+"']").attr("checked", false);
					}		  
			}
   }); 
});
</script>

<script src="{{asset('public/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/module.js/Rule/index.js?ver=1')}}"></script>
<script src="{{asset('public/module.js/main.js')}}"></script>
@endsection
