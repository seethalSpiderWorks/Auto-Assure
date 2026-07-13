<!doctype html> 
<html lang="en">

    <head>
		    		
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <title> Dashboard | Auto Assure </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Admin & Dashboard" name="description" />
        <meta content="Themesbrand" name="author" />
		
		@yield('css')
		
		  <style>
            .badge-soft-primary{color:#fff!important;background-color:#556ee6!important}
            .badge-soft-secondary{color:#fff!important;background-color:#74788d!important}
            .badge-soft-success{color:#fff!important;background-color:#34c38f!important}
            .badge-soft-info{color:#fff!important;background-color:#50a5f1!important}
            .badge-soft-warning{color:#212529!important;background-color:#f1b44c!important}
            .badge-soft-danger{color:#fff!important;background-color:#f46a6a!important}
            .badge-soft-dark{color:#fff!important;background-color:#343a40!important}
            .badge-soft-purple{color:#fff!important;background-color:#6f42c1!important}
        </style>
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.svg')}}">
		
		<link rel="stylesheet" type="text/css" href="{{asset('assets/libs/toastr/build/toastr.min.css')}}">

        <!-- Bootstrap Css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />		
		
		<!-- plugin css -->
        <link href="{{asset('assets/libs/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />            	
        <!-- App Css-->
        <link href="{{asset('assets/css/app.min.css?ver=1.2')}}" id="app-style" rel="stylesheet" type="text/css" />
        <!-- Brand button colors (from logo) -->
        <link href="{{asset('assets/css/brand.css?ver=1.3')}}" rel="stylesheet" type="text/css" />
		<!-- DataTables -->
        <link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
		
		<style>
			.input-drop
			{
				box-shadow: 0 2px 4px rgba(15,34,58,.12);
    			animation-name: DropDownSlide;
				animation-duration: .3s;
				position: absolute;    
				background: white;
				width: 100%;
				padding: 10px 10px;
			}
			.input-drop ul
			{
			    margin-bottom: 0;
				list-style: none;
				padding-left: 0;
			}
			.input-drop ul li
			{
				padding: 5px 0;
			}
		
		</style>
		
    </head>
    
    <body>

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="#" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('assets/images/logo.svg')}}" alt="" height="40" class="resp-logo"> <!-- resp -->
                                </span>
                                <!--<span class="logo-lg">-->
                                <!--    <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="20">-->
                                <!--</span>-->
                            </a>

                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                      
						<div class="d-flex">
							<!--------- Search ---------->
							<form class="form1" style="display:none;"> 
								<div class="position-relative mob-in1" style="">
                            		<input type="text" id="student_name" style="line-height:1.8" name="student_name" class="form-control" placeholder="Search..." data-action="input_field_customers_name_search">
									<!--span class="uil-search"></span-->
									<input type="hidden" id="reg_id" name="reg_id">
									<input type="hidden" id="stud_id" name="stud_id">
									
									<div class="input-drop" style="display:none;">
									  <ul id="input_search">
										<!--  <li>aaaaaaaaaaaaaaaaaaaa</li>  
										  <li>bbbbbbbbbbbbbbbbbb</li>   -->
									  </ul>
									 
									</div>
                        		</div>
							</form>
							<!--------- Search ---------->
							
							<!--------- Seleact Branch ---------->
							<form class="form2"> 
								<div class="position-relative  mob-in2" style="">
                            
								<?php
								$branchData = Auth::user()->dashboardbranchAction_new();
								$branch = Auth::user()->user_branch; 
								if(session('application_branch'))
								{
									$branch = session('application_branch');
								}
								else
								{ 
									if(session('branch'))
									{  
										$branch = session('branch'); 
										session(['application_branch' => $branch]);
									}
									else
									{ 
										session(['branch' =>$branch ]);
										session(['application_branch' => $branch]);
									}
								} ?>
						 {!! html()->select('branch_name_data', $branchData, $branch)->attributes( ['id'=>'branch_name_data','class'=>'form-select form-control select2 branch_name_data','required'=>'required','data-parsley-errors-container'=>'#branch_name_data-parsley-error','data-parsley-required-message'=>'Branch Name required']); !!}
						 
						<!--{!! html()->select('branch_name_data', $branch, session('application_branch'))->attributes(['id' => 'branch_name_data','class' => 'form-select form-control select2 branch_name_data','required' => 'required']) !!}-->
							
                        </div>
						</form>
						<!--------- Seleact Branch ---------->
					
						<div class="position-relative  mob-in2" style="display:none" id="notification_div">	
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<b><a href="javascript:startFCM()" style="color:#1f7556">Click here </a></b> to enable browser notification
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>	
						</div>
						</div>
                    </div>
	
                    <div class="d-flex">
						
						<div class="mob-logo">
							<span class="logo-lg">
                           	 	<!-- <img src="{{url('/assets/images/logo.svg')}}" alt="" style="">  -->
                        	</span>
						</div>

                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-search"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    
                                <form class="p-3">
                                    <div class="m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
						
						 <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-apps"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <div class="px-lg-2">
								
                                    <div class="row g-0">
                                       
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="{{url('leads')}}">
                                                <img src="{{asset('assets/images/brands/Account icon svg.svg')}}" alt="bitbucket">
                                                <span>Leads</span>
                                            </a>
                                        </div>
                                       
                                    </div>

                                </div>
                            </div>
                        </div>
                       
                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                                <i class="uil-minus-path"></i>
                            </button>
                        </div>
					
                        <div class="dropdown d-none"><!--d-inline-block-->
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-bell"></i>
								<span class="badge bg-danger rounded-pill"></span>
                            </button>
						
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="m-0 font-size-16"> Notifications </h5>
                                        </div>
                                        <!--div class="col-auto">
                                            <a href="#" class="small"> Mark all as read</a>
                                        </div -->
                                    </div>
                                </div>
								
                                <div data-simplebar style="max-height: 230px;"> </div>
							
                                <div class="p-2 border-top">
                                    <div class="d-grid">
                                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ url('/notification')}}">
                                            <i class="uil-arrow-circle-right me-1"></i> View More..
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
					
						<?php  $division = Auth::user()->user_division; ?>
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								
								<?php 
								$user_img = Auth::user()->user_img;
								
								if($user_img != null)
								{ ?>
									<img class="rounded-circle header-profile-user" src="{{url($user_img)}}" alt="Header Avatar"> <?php } 
								else
								{ ?>	
                                    <img class="rounded-circle header-profile-user" src="{{asset('assets/images/users/avatar.png')}}" alt="Header Avatar">
								    <?php 
								} ?>
								
                                <span class="d-none d-xl-inline-block ms-1 fw-medium font-size-15"><?php  echo $privilege = Auth::user()->name; ?> </span>
                                <i class="uil-angle-down d-none d-xl-inline-block font-size-15"></i>
                            </button>
							
                            <div class="dropdown-menu dropdown-menu-end">
                                
                                <a class="dropdown-item" href="{{url('users/profile/'.Auth::user()->id)}}"><i class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span class="align-middle">View Profile</span></a>
								<a class="dropdown-item" href="javascript:changemypass('<?php  echo $privilege = Auth::user()->user_id; ?>')"><i class="fas fa-key font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Reset Password</span></a>
								<a class="dropdown-item" href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="uil uil-sign-out-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Sign out</span></a>
								
								<form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                </form>
							</div>
                        </div>
						
						 <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item ">
								<a class="dropdown-item" href="{{url('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
									<i class="fas fa-power-off" style="font-size: 1.2em;"></i>
								</a>
								<form id="logout-form" action="{{url('logout')}}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                </form>
                            </button>
                        </div>

                    </div>
                </div>
            </header>
			
			<?php  
			    $allmenadata = Auth::user()->allmenuData();
                $mymenus = Auth::user()->menuData();                
                $my_main_menus = json_decode($mymenus['alloted_mainmenus']);
                $my_sub_menus =  json_decode($mymenus['alloted_submenus']);
                
                // Getting active menu
                $segments = Request::segments();
                // print_r($segments);
                $url_segment =''; 
                foreach($segments as $segment){
                    $url_segment = $url_segment."".$segment."/";                   
                }
                $url_segment = rtrim($url_segment,'/');
                $menuRules = Auth::user()->menuRules($url_segment);  
                ?>
            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="{{url('')}}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{asset('assets/images/logo.svg')}}" style="margin-left: -16px; width:63px;" height="52" alt="">  <!-- style="margin-left: -16px; width:63px;" height="22" -->
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset('assets/images/logo.svg')}}" style="width:140px; height:80px;" alt=""><!-- style="width:140px; height:40px;" -->
                        </span>
                    </a>

                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <div data-simplebar class="sidebar-menu-scroll">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="sub-menu metismenu list-unstyled" id="side-menu">
                            
                           
                         
                            
						 @php
                        $badgeActivateOn = [
                            'order-menu',
                            'pending-p-menu',
                            'customer-menu',
                            'retailer-menu',
                            'enquiry-menu',
                        ];
                        @endphp 
						
						<?php
                        foreach($allmenadata as $main){
                        if(empty($my_main_menus)){ $my_main_menus = array(); }
                        if(in_array($main->id,$my_main_menus)){   
                            if(count($main->Submenus) == 0){ // menu doesnt have submenus
                                ?>   
									
    						<li>
                                <a href="{{URL::to($main->main_link)}}">
                                    <i class="<?php echo $main->main_icon; ?>"></i>
                                    <span>
                                        <?php echo $main->main_menuname ?></span> 
                                    @if( in_array($main->badge_class, $badgeActivateOn) )
                                        <span style="display: none;" class="{{$main->badge_class}} mybadge badge badge-light"> </span>
                                    @endif
                                </a>
                            </li>
                            <?php                           
                        }
                        else{ ?>
								   
						<li>
                        <a href="#" class="has-arrow waves-effect" aria-expanded="false" data-toggle="collapse"><i class="<?php echo $main->main_icon; ?>"></i><span>
                           <?php echo $main->main_menuname; ?></span>
                         
                        @if (in_array($main->badge_class, $badgeActivateOn))
                            <span style="display: none;" class="{{$main->badge_class}} mybadge badge badge-light"> </span>
                        @endif
                        </a>
                          
                            <ul id="dropdown-<?php echo $main->main_menuname; ?>" class="sub-menu collapse list-unstyled pt-0 <?php if($menuRules){if($main->id==$menuRules['sub_main_id']){ echo "show";}else{ if($main->id==2 && $menuRules['sub_main_id'] =='') { echo "show";} }} ?>">
                                <?php 
                                    
                                    foreach($main->Submenus as $sub)
                                    {
                                        if(empty($my_sub_menus)){ $my_sub_menus = array(); }
                                            if(in_array($sub->sub_id,$my_sub_menus))
                                            {   ?>
                                <li>
                                    <a href="{{URL::to($sub->sub_link)}}"> <i class="<?php echo $sub->sub_icon; ?>"></i>
                                        <?php echo $sub->sub_name; ?>
									<!------- Count ------->	
                                        <?php 
                                        if($sub->sub_id == 40)
                                        { 
										    $total_lead = DB::table('tbl_lead')
                                 					->select('tbl_lead.lead_reg_id')
                                  					->where('lead_status',0)
                               						//->where('lead_branch_id',session('application_branch'))
											        ->where('lead_date',date('Y-m-d'))
                                					->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id');
                                  				 
										/**if(Auth::user()->previlage != 2) 
										{ **/	   
									    	    /*** $total_lead->where('lead_branch_id',session('application_branch')); ***/
										/**} **/
										
										if(Auth::user()->previlage == 48 )
										{
											$total_lead->where('lead_added_by',Auth::user()->id);
										}
											   
										//$total_lead = $total_lead->groupBy('tbl_lead.lead_reg_id')->get()->count();
										$total_lead = $total_lead->get()->count();	   
											   
										?>
											<span class="badge rounded-pill bg-info float-end">
										     {{$total_lead}}
											</span>
                                            <?php 
                                        } ?>
										
									<!---------------------->
									<?php 
								    if($sub->sub_id == 39)
									{ 
										    $total_leads = DB::table('tbl_lead')
                                 					->select('tbl_lead.lead_reg_id')
                                  					->where('lead_status',0)
                               						//->where('lead_branch_id',session('application_branch'))
                                					->join('tbl_basic_registration','tbl_basic_registration.breg_id','tbl_lead.lead_reg_id');
										
										if(Auth::user()->previlage == 48 )
										{
											$total_leads->where('lead_added_by',Auth::user()->id);
										}
											   
										// $total_leads = $total_leads->groupBy('tbl_lead.lead_reg_id')->get()->count();
										   $total_leads = $total_leads->get()->count();    ?>
											<span class="badge rounded-pill bg-primary float-end">
										     {{$total_leads}}
											</span>
                                            <?php 
                                    } ?>
                                     <!------- Count ------->	
										
									</a>
								</li>
                                <?php 
                                }
                              }  ?>
                            </ul>
                           
                        </li>
						<?php    
                             }
                        }
                        } ?>
						
						<?php  $privilege = Auth::user()->previlage; ?>
                        <?php if ($privilege == 1 || $privilege == 2 || $privilege == 10) { ?>
                        <!--li><a href="#dropdown-marketing" aria-expanded="false" data-toggle="collapse" class="has-arrow waves-effect"><i class="fa fa-cog"></i><span>Master Settings</span></a>
                            <ul id="dropdown-marketing" class="sub-menu collapse list-unstyled pt-0" style="margin-bottom: 500px">
                                <li>
                                    <a href="{{URL::to('company') }}"> <i class="far fa-building"></i> Company</a>
                                </li>
								<li>
                                    <a href="{{URL::to('branch') }}"> <i class="fa fa-cubes"></i> Branch</a>
                                </li>
                                <li>
                                    <a href="{{URL::to('privileges') }}"> <i class="fas fa-key"></i> Privileges
                                    </a>
                                </li>
                                <li>
                                    <a href="{{URL::to('rules') }}"> <i class="fas fa-exclamation-triangle"></i> Rules
                                    </a>
                                </li>
                                
                            </ul>
                        </li-->
                        <?php } ?>
                        
                           <li>
                                <a href="{{ url('inspections') }}">
                                    <i class="bx bx-clipboard"></i>
                                    <span>Inspections</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('templates.index') }}">
                                    <i class="bx bx-list-check"></i>
                                    <span>Inspection Templates</span>
                                </a>
                            </li>
                         
                        </ul>
                    </div>
					
                    <!-- Sidebar -->
                </div>
            </div> <!-- Left Sidebar End -->
			<!-- ========== Left Sidebar End ========== -->
			
            <div class="main-content">
                @yield('content')
            </div>
			
			<!-- ========== Footer ========== -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">                            
                        <div class="col-sm-6">                                
							<p style="color:#74788d;">©<script>document.write(new Date().getFullYear())</script> 
								<a href="https://www.auto-assure.com/" target="_blank" style="color:#74788d;">
								Auto Assure.</a>  </p>
						</div>
						<div class="col-sm-6">
							<div class="text-sm-end d-none d-sm-block">
                                Powered by :  <a href="https://srvinfotech.com/" target="_blank" class="text-reset">SRV InfoTech</a> 
                            </div>
                        </div>
						<!--div class="col-sm-6 text-sm-end">
                            <a href="{{url('version')}}" target="_blank" class="text-reset">Version</a> 
                        </div-->
                    </div>
                </div>
            </footer>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
           
        </div><!-- END layout-wrapper -->
        
    <!-------------------------- Reset Password Start -------------------------->
		
	<div class="modal fade" id="change_mypass" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reset Password</h4>
					<button type="button" class="btn-close" onclick="close_reload()"  data-bs-dismiss="modal"> </button>
                </div>
                <div class="modal-body">
				{!! html()->form('POST')->attributes(['url' =>'','method'=>'post'])->open() !!}
                     <input type='hidden' name='_token' value='{{csrf_token()}}'>
                    <input type='hidden' name='user_id_mypasschange' id="user_id_mypasschange" value=''>
                    <div class="form-group col-md-12">
                        <div class="col-md-12" style="margin-bottom:10px">
                            <div class="group material-input">
							<label style="margin-left:10px;">Current Password</label>
								<input type="password" name="user_mypass_current" class="form-control date_picker_first" id="user_mypass_current" data-parsley-required-message="Password Required" data-parsley-errors-container="#user_mypass_current-parsley-error" required>
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
                        <button type="button" id="btn-closing-datepass" onclick="Change_My_Password()" class="btn btn-success">Confirm</button>
                    </div>
                    {!! html()->form()->close() !!}
                </div>
            </div>
        </div>
    </div>
	<!-------------------------- Reset Password End -------------------------->

        <!-- JAVASCRIPT -->
        <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
		<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
        <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('assets/libs/waypoints/lib/jquery.waypoints.min.js')}}"></script>
        <script src="{{asset('assets/libs/jquery.counterup/jquery.counterup.min.js')}}"></script>

        <script src="{{asset('assets/js/pages/dashboard.init.js')}}"></script>
		
		<!-- parsleyjs -->
		<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>		        
        <script src="{{asset('assets/js/pages/form-validation.init.js')}}"></script>
		
		<!-- toastr plugin -->
        <script src="{{asset('assets/libs/toastr/build/toastr.min.js')}}"></script>
		 <!-- plugins -->
        <script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>   

        <!-- toastr init -->
        <script src="{{asset('assets/js/pages/toastr.init.js')}}"></script>
		<!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
        <!-- App js -->
        <script src="{{asset('assets/js/app.js')}}"></script>

 @yield('js')

    </body>

</html>

 <script>
    function changemypass(id) {
        $("#user_mypass_current").val('');
        $("#user_mypass_new").val('');
        $("#user_mypass_conf").val('');
        
        $('#change_mypass').modal('show');
        $('#user_id_mypasschange').val(id);
    }
    
    function close_reload()
    {
        location.reload();
    }
	
    function Change_My_Password() {
        var token = $('meta[name="csrf-token"]').attr('content');
        var id    = $('#user_id_mypasschange').val();
        var user_mypass_current = $('#user_mypass_current').val();
        var user_mypass_new  = $('#user_mypass_new').val();
        var user_mypass_conf = $('#user_mypass_conf').val();
        var url_mypassreset  = '{{URL::to("users/resetmyUser")}}';
        
        if(user_mypass_current === '')
        {
           $("#user_mypass_current-parsley-error").show(); 
        }
        if(user_mypass_new === '')
        {
           $("#user_mypass_new-parsley-error").show();  
        }
        if(user_mypass_conf === '')
        {
           $("#user_mypass_conf-parsley-error").show();  
        }
        
        if(user_mypass_conf !== '' && user_mypass_new !== '' && user_mypass_current !== '')
        {
            $.ajax({
            type: 'POST',
            data: { '_token': token, 'id': id, 'user_mypass_current': user_mypass_current, 'user_mypass_new': user_mypass_new, 'user_mypass_conf': user_mypass_conf },
            url: url_mypassreset,
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
$("select[name='branch_name_data']").change(function() 
{
    var branch_id = $("select[name='branch_name_data']").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
            type: 'POST',
            data: { '_token': token, 'id': branch_id },
            url: '<?php echo url('company/set_branch') ?>',
            success: function(result) 
            {
                location.reload();
            }
        });
});
</script>

 <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
 <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script>
  var url_get_customer_data='{{URL::to("customer_data")}}';
</script>

<script src="{{asset('module.js/dashboard/search.js?ver=1.1')}}"></script>

<script>
function getProfile(regid,stud_id)
{
}
</script>

<script>
	
    var firebaseConfig = {
        apiKey: "AIzaSyCJNj8-kgb00Oc_rWPSgNixqQFmqCoTN6c",
        authDomain: "srvinfotech-31f88.firebaseapp.com",
        projectId: "srvinfotech-31f88",
        storageBucket: "srvinfotech-31f88.appspot.com",
        messagingSenderId: "324346137022",
        appId: "1:324346137022:web:45bb2791d44dc22a0111ef",
        measurementId: "G-TNRFBZ5XSN"
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
	
	messaging.requestPermission().then(function () {
                return messaging.getToken()
    }).then(function (response) {
		var user_token = "<?php echo Auth::user()->device_token?>";
		if(user_token != response)
		{
			$('#notification_div').show();
		}
		else{
			$('#notification_div').hide();
		}
	});

    function startFCM() {
        
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (response) {
			
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function (response) {
						$('#notification_div').hide();
                       // alert('Token stored.');
                    },
                    error: function (error) {
						
                    },
                });
            }).catch(function (error) {
				
            });
    }
    messaging.onMessage(function (payload) {
        const title = payload.notification.title;
        const options = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(title, options);
    });
</script>
	