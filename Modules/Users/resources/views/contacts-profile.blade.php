@extends('layouts.myfudapp')
@section('content')

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-flex align-items-center justify-content-between">
                                    <h4 class="mb-0">User - <?php echo $data->user_id; ?></h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                            <li class="breadcrumb-item active"><?php echo $data->user_id; ?></li>
                                        </ol> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row mb-4">
                            <div class="col-xl-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="float-end">
                                                <a class="text-body font-size-18" href="javascript:changemypass('<?php  echo $privilege = $data->user_id; ?>')" role="button" aria-haspopup="true">
                                                  <i class="fas fa-key"></i>
                                                </a>
                                            </div>
                                           
                                            <div>
												<?php 
												$user_img = $data->user_img;
												if($user_img != null)
												{?>
													<img src="{{asset($user_img)}}" alt="" class="avatar-lg rounded-circle img-thumbnail">
												    <?php 
												}
												else
												{ ?>
													<img src="{{asset('assets/images/users/avatar.png')}}" alt="" class="avatar-lg rounded-circle img-thumbnail">
												<?php } ?>
											</div>
                                            <h5 class="mt-3 mb-1"><?php  echo $privilege = $data->name; ?></h5>
											<!--<button class="btn btn-success" style="border-radius: 40px;  margin-right: 10px; padding: 0px; width: 28px;"><i class="uil-phone-alt" style="font-size: 15px;"></i></button>-->
                                            <?php  echo $privilege = $data->mobile; ?>

											<?php $privilege1 = $data->previlage;
											$privilege_name = DB::table('privilege')
                        							->select('privilege_name')
                        							->where('status',0)
                        							->where('id',$privilege1)
                        							->first(); ?>
											 <div class="mt-1" >
												 <p style="text-align: center; font-family: &quot;IBM Plex Sans&quot;, sans-serif; font-size: 15px; margin-bottom: 0px;"> <?php echo $privilege_name->privilege_name; ?> </p>
											 </div>
											
                                            <!--div class="mt-4">
                                                <button type="button" class="btn btn-light btn-sm"><i class="uil uil-envelope-alt me-2"></i> Message</button>
                                            </div-->
                                        </div>
										
                                        <hr class="my-4 mt-1 mb-2">

                                        <div class="text-muted">
                                            <!--h5 class="font-size-16">About</h5>
                                            <p> </p-->
											
											<?php $user_branch = $data->user_branch;
											/*$data = DB::table('tbl_branch')
													->leftjoin('users','users.user_branch','=','tbl_branch.branch_id')
													->where('branch_id',$user_branch)
													->first();*/  ?>
											
                                            <div class="mt-4">
											 
                                                <div class="row mt-2">
                                                    <p class="mb-1">Name :</p>
                                                    <h5 class="font-size-14"><?php  echo $data->name; ?></h5>
                                                </div>
                                                <div class="row mt-2">
                                                    <p class="mb-1">Mobile :</p>
                                                    <h5 class="font-size-14"><?php  echo $data->mobile; ?></h5>
                                                </div>
                                                <div class="row mt-2">
                                                    <p class="mb-1">Email :</p>
                                                    <h5 class="font-size-14"><?php  echo $data->user_email; ?></h5>
                                                </div>
                                                <div class="row mt-2">
                                                    <p class="mb-1">Designation :</p>
                                                    <h5 class="font-size-14"><?php  echo $data->user_designation; ?></h5>
                                                </div>
												<div class="row mt-2">
                                                   <p class="mb-1">Address :</p>
                                                   <h5 class="font-size-14"><?php  echo $data->user_perm_address; ?></h5>
                                                </div>
												<div class="row mt-2">
                                                   <p class="mb-1">Company :</p>
                                                   <h5 class="font-size-14"><?php  
													   $user_company = $data->user_company;
													   $company = DB::table('tbl_company')
																->select('company_name','company_code','company_mob','company_email','company_address')
																->where('company_status',0)
																->where('company_id',$user_company)
																->first();
													   echo $company->company_name; ?></h5>
                                                </div>
												
												<?php  $previlage = $data->previlage; 
												//if($previlage == 48) { ?> 
												
												<div class="row mt-2">
                                                   <p class="mb-1">Centre :</p>
                                                   <h5 class="font-size-14">
													   <?php  
													   $user_branch = Auth::user()->user_branch;
													   $branch_name = DB::table('tbl_branch')
																->select('branch_name','branch_code','branch_mob','branch_email','branch_address','branch_gmb_link','branch_gmb_id')
																->where('branch_status',0)
																->where('branch_id',$user_branch)
																->first();
													   echo $branch_name->branch_name;  ?>
													</h5>
                                                </div>
												<?php //} ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-9">
                                <div class="card mb-0">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
										<li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="contacts-profile.html#profile" role="tab">
                                                <i class="uil uil-clipboard-notes font-size-20"></i>
                                                <span class="d-none d-sm-block">Profile</span> 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-bs-toggle="tab" href="contacts-profile.html#about" role="tab">
                                                <i class="uil uil-user-circle font-size-20"></i>
                                                <span class="d-none d-sm-block">Activities</span> 
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- Tab content -->
                                    <div class="tab-content p-4">
										
										 <div class="tab-pane active" id="profile" role="tabpanel">
                                            <div>
                                               
												<div class="row">
													<div class="col-md-6">
														
														  <h5 class="font-size-16 mb-3">Personal Details</h5>
														   <table id="product_available_day" class="table table-bordered dataTable">
															   <tr>
																   <td>Name</td>
																   <td>:</td>
																   <td>{{ $data->name }} {{ $data->lname}}</td>
															   </tr>
															   <tr>
																   <td>Email</td>
																   <td>:</td>
																   <td>{{ $data->user_email }} </td>
															   </tr>
															   <tr>
																   <td>Mobile</td>
																   <td>:</td>
																   <td>{{ $data->mobile }} </td>
															   </tr>
															   <tr>
																   <td>Address</td>
																   <td>:</td>
																   <td>{{ $data->user_perm_address}} </td>
															   </tr>
															    <tr>
																   <td>Company</td>
																   <td>:</td>
																   <td>{{ $company->company_name }} </td>
															   </tr>
															   <tr>
																   <td>Designation</td>
																   <td>:</td>
																   <td>{{ $data->user_designation }} </td>
															   </tr>
															   <tr>
																   <td>Privilege</td>
																   <td>:</td>
																   <td>{{ $privilege_name->privilege_name }} </td>
															   </tr>
															   
														   </table>
													</div>
													<div class="col-md-6">
															<h5 class="font-size-16 mb-3">Company Details</h5>
															<table id="product_available_day" class="table table-bordered dataTable">
															  <tr>
																   <td>Company Name</td>
																   <td>:</td>
																   <td>{{ $company->company_name }} </td>
															   </tr>
															   <tr>
																   <td>Company Code</td>
																   <td>:</td>
																   <td>{{ $company->company_code }}</td>
															   </tr>
															  <tr>
																   <td>Company Mobile</td>
																   <td>:</td>
																   <td>{{ $company->company_mob }}</td>
															   </tr>
															   <tr>
																   <td>Company Email</td>
																   <td>:</td>
																   <td>{{ $company->company_email }}</td>
															   </tr>
															   <tr>
																   <td>Company Address</td>
																   <td>:</td>
																   <td>{{ $company->company_address }}</td>
															   </tr>
															</table>
													</div>
													
													<div class="col-md-12">
															<h5 class="font-size-16 mb-3">Branch Details</h5>
															<table id="product_available_day" class="table table-bordered dataTable">
															  <tr>
																   <td>Branch Name</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_name }} </td>
																   <td>Branch Code</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_code }}</td>
															   </tr>
															   <tr>
																  <td>Branch Mobile</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_mob }}</td>
																   <td>Branch Email</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_email }}</td>
															   </tr>
															   <tr>
																   <td>GMB ID</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_gmb_id }}</td>
																   <td>GMB Link</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_gmb_link }}</td>
															   </tr>
																 <tr>
																   <td>Branch Address</td>
																   <td>:</td>
																   <td>{{ $branch_name->branch_address }}</td>
															   </tr>
															</table>
													</div>
												</div>

                                            </div>
                                        </div>
                                        <div class="tab-pane " id="about" role="tabpanel">
                                            <div>
                                          
                                                <div>
                                                    <h5 class="font-size-16 mb-4">Activities</h5>
                                                    <div class="table-responsive">
                                                    <table id="actDataTable" class="mytable table table-bordered table-hover" style="width:100%!important">									<input type="hidden" id="user_id" name="user_id" value="{{$data->id}}" >
                                                            <thead>
                                                                <tr>
                                                                    <th class="sno" style="width:5%;">#</th>
                                                                    <th >Date</th>
                                                                    <th >Time</th>
																	<th >IP</th>
                                                                    <th >Activity</th>
                                                                </tr>
                                                            </thead>
															<tbody> </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="tab-pane" id="messages" role="tabpanel">
                                            <div>
                                                <div data-simplebar style="max-height: 430px;"> </div>
        
                                                <div class="border rounded mt-4">
                                                    <form action="contacts-profile.html#">
                                                        <div class="px-2 py-1 bg-light">
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-sm btn-link text-dark text-decoration-none"><i class="uil uil-link"></i></button>
                                                                <button type="button" class="btn btn-sm btn-link text-dark text-decoration-none"><i class="uil uil-smile"></i></button>
                                                                <button type="button" class="btn btn-sm btn-link text-dark text-decoration-none"><i class="uil uil-at"></i></button>
                                                            </div>
                                                        </div>
                                                        <textarea rows="3" class="form-control border-0 resize-none" placeholder="Your Message..."></textarea>
                                                    </form>
                                                </div> <!-- end .border-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                        
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
 @endsection
    
@section('js')
		<script>
		 var url_activity = '{{URL::to("users/getActivity")}}';
        </script>
        <!-- JAVASCRIPT -->
       	<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
		<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('assets/libs/waypoints/lib/jquery.waypoints.min.js')}}"></script>
        <script src="{{asset('assets/libs/jquery.counterup/jquery.counterup.min.js')}}"></script>
		<script src="{{asset('module.js/Users/activity.js?ver=2')}}"></script>
  @endsection    