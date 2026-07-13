@extends('layouts.myfudapp')
@section('content')
 
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Privilege</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Manage Privilege </li>
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
												<label class="form-label">Privilege Name<span class="star_required" style="color:red"> *</span></label> </label>
												{!! html()->text('privilege_name','')->attributes(['required'=>'required','placeholder'=>"Privilege Name",'data-parsley-required-message'=>'Privilege Name Required', 'class'=>'form-control', 'id'=>'privilege_name'])!!}														  
 												<span class="highlight"></span>
											</div>

											<div class="col-md-3">
												<label class="form-label">Short Code<span class="star_required" style="color:red"> *</span></label></label>
												{!! html()->text('short_code','')->attributes(['required'=>'required','placeholder'=>"Short Code",'data-parsley-required-message'=>'Short Code Required','class'=>'form-control','id'=>'short_code'])!!}
												<span class="highlight"></span>
											</div>
			 
    										<div class="col-md-3" > 
                                                <div class="flex-wrap gap-3  editButton" style="display:none;margin-top:10%">
        									        <a href="#" class="btn btn-primary waves-effect waves-light" onclick="createOrUpdate('{{URL::to("privileges/editPrivilege")}}', 'createForm', 'add-modal', '','{{URL::to("privileges/")}}')" class="btn btn-info btn-block">
        											    <!--i class="fas fa-pencil-alt"></i-->Update
        											 </a>
        								        </div>
        								        <div class="flex-wrap gap-3  saveButton" style="margin-top:10%">
        									        <a href="#" class="btn btn-primary waves-effect waves-light" onclick="createOrUpdate('{{URL::to("privileges/addprivilege")}}', 'createForm', 'add-modal', '','{{URL::to("privileges/")}}')" class="btn btn-info btn-block">
        									            <!--i class="fas fa-save" title="Save"></i--> Save </a>&nbsp;
        										    <a href="#" onclick="clearForm()" class="btn btn-danger waves-effect waves-light"> Reset </a>
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
 
							<div class="table-responsive m-t-40">
								<table  id="privilegeDataTable" class="mytable table table-bordered table-hover">
									<thead>
										<tr>
											<th class="sno" style="text-align: center !important;">#</th>
											<th width="">Privilege Name</th>
											<th width="">Code </th>
											<th width="">Branches </th>
											<th class="actionwidth" style="text-align:center;">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php  $i = 1  ?>     
										@foreach($privileges as $privilege)
								  
											<?php  
											//$division_array = json_decode($privilege['alloted_divisions']);
											$branch_array     = json_decode($privilege->alloted_branches);
								  
											if(empty($branch_array)){ $branch_array = array(); } ?>
												<tr>
													<td style="text-align: center !important;">{{ $i }} </td>
													<td>{{ $privilege->privilege_name }} </td>
													<td>{{ $privilege->privilege_code }} </td>
													<td><?php
														foreach($divisionData as $division){
														    if(!empty($division['branches_data'])){   
    												            $j=1 ; 
														        foreach($division['branches_data'] as $branch){
															        if($branch['branch_status'] == 0)
															        {  ?>
                														<div>
                    														<ul style="list-style-type: none;">
                        														<li class="branch_div">
                        															<input type="checkbox"  class="allocate_branch" <?php if(in_array($branch['branch_id'],$branch_array)){ echo "checked"; }  ?> name="branch_data{{ $privilege->id }}" data-priv_id="{{ $privilege->id }}" data-div_id="{{ $division['company_id'] }}" data-branch_id="{{  $branch['branch_id'] }}" value="{{  $branch['branch_id'] }}"> {{  $branch['branch_name'] }} 
                        														</li>
                    														</ul>
                														</div>
														                <?php 
													                }
																    //if($j%1 == 0) { echo "<br>"; } //$j++;
														        }
															}  ?>
													        <?php
													  } ?>
									                </td>
													<td align="center">
														<a href="#" onclick="javascript:editCurrency({{ $privilege->id }})" title="Edit"><i class="far fa-edit" style="color:green"></i> </a>
									                    &nbsp <a href="javascript:privilegeDelete({{ $privilege->id }})" title="Delete" ><i class="far fa-trash-alt" style="color:red"></i></a>
													</td>
												</tr>
											    <?php $i++ ?>
									   									   
									    @endforeach  
								  
								    </tbody>
							    </table>
							</div><!-- End Form -->
						</div>
					</div>
				</div>
			</div>
		
			<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="staticBackdropLabel">Delete Privilege</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post','id'=>'deleteForm','class'=>'myform'])->open() !!}
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

    <div class="modal fade" id="delete_entry" role="dialog">
        <div class="modal-dialog">
    
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
    			    {!! html()->form('POST')->attributes(['url' =>'','method'=>'post'])->open() !!}
                        <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                        <input type='hidden' name='privilege_id' id="privilege_id" value=''> 
                        <div class="form-group col-md-12">
                            Are you sure,You want to delete the privilage ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btn-closing-date"  onclick="deleteEntry()" class="btn btn-success">Delete</button>                       
                        </div>
                    {!! html()->form()->close() !!}
                </div>
            </div>
    
        </div>
    </div>

@endsection

@section('js')
<script type="text/javascript">
    // var url_privilegeDataTable= '{{URL::to("privileges/getDatatable")}}';
    var url_editprivilege        = '{{URL::to("privileges/getPrivilege")}}';
    var url_deleteprivilege      = '{{URL::to("privileges/deletePrivilege")}}';
</script>

<script type="text/javascript">
    $('body').on('click','.allocate_branch',function(e){
        var priv_id   = $(this).data('priv_id');
        var div_id    = $(this).data('div_id');
        var branch_id = $(this).data('branch_id');
	    var branches  = [];

	    $("input:checkbox[name=branch_data"+priv_id+"]:checked").each(function(){
                branches.push($(this).val());
		});
	    // console.log(branches);
	    var token = $('meta[name="csrf-token"]').attr('content');
	 
	    $.ajax({
            url: '{{URL::to("privileges/assignPrivilege")}}',
    		type: 'POST',
            data: {priv_id: priv_id,branches:JSON.stringify(branches),_token:token },  //prop_pre_values:JSON.stringify(prop_pre_values)
           	async: false,
    		cache: false,
    		timeout: 30000,
    		error: function(){
				return true;
			},
		    success: function(data) {}
        });
	 
    	/* if($(this).is(':checked'))
                alert('checked'); */
    });
</script>
	<script src="{{asset('public/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{asset('public/module.js/Privilege/index.js?ver=9')}}"></script>
	<script src="{{asset('public/module.js/main.js?ver=55')}}"></script>
@endsection
