@extends('layouts.myfudapp')
@section('content')

@php $isLeadsList = request()->segment(1) === 'leadslist'; @endphp

    <div class="page-content @if($isLeadsList) ll-page @endif">
        <div class="container-fluid">

            @if($isLeadsList)
                {{-- Distinct hero for the Leads (leadslist) page --}}
                <div class="ll-hero">
                    <div class="ll-hero__left">
                        <span class="ll-hero__icon"><i class="bx bxs-user-detail"></i></span>
                        <div>
                            <div class="ll-hero__eyebrow">Lead Management</div>
                            <h4 class="ll-hero__title">Leads</h4>
                            <p class="ll-hero__sub">Browse, filter and assign leads</p>
                        </div>
                    </div>
                    <ol class="breadcrumb ll-hero__crumb m-0">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Leads</li>
                    </ol>
                </div>
            @else
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Leads</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{url('dashboard')}}"> Home</a></li>
                                    <li class="breadcrumb-item active">Leads</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->
            @endif
          
						<div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
										<div class="lead-controls">

											{{-- Filters --}}
											<div class="lead-filterbar">
												<div class="lead-field">
													<label>Date type</label>
													<?php $flr_datetype = session('filter_date_type') ?: 'added'; ?>
													<select class="form-control select2" id="filter_date_type">
														<option value="added" <?php echo $flr_datetype=='added' ? 'selected' : ''; ?>>Lead added date</option>
														<option value="scheduled" <?php echo $flr_datetype=='scheduled' ? 'selected' : ''; ?>>Scheduled date</option>
													</select>
												</div>

												<div class="lead-field">
													<label>From date</label>
													<input type="date" class="form-control datetimepicker-input" placeholder="From Date" id="from_date" data-toggle="datetimepicker" data-target="#from_date" value="<?php echo session('filter_lead_fdate'); ?>">
												</div>

												<div class="lead-field">
													<label>To date</label>
													<input type="date" class="form-control datetimepicker-input" placeholder="To Date" id="to_date" data-toggle="datetimepicker" data-target="#to_date" value="<?php echo session('filter_lead_ldate'); ?>">
												</div>

												<div class="lead-field">
													<label>Source</label>
													<?php $flr_source=''; if(@session('filter_lead_source')) $flr_source = session('filter_lead_source'); ?>
													{!! html()->select('filter_source', $sources , $flr_source)->attributes([ 'class'=>'form-control form-select select2','id'=> 'filter_source'])->placeholder('Select Source') !!}
												</div>

												<div class="lead-field">
													<label>Staff</label>
													<?php $flr_staff=''; if(@session('filter_staff')) $flr_staff = session('filter_staff'); ?>
													{!! html()->select('filter_staff', $users , $flr_staff)->attributes([ 'class'=>'form-control select2','id'=>'filter_staff','required'=> 'required'])->placeholder('Select Staff') !!}
												</div>

												<div class="lead-field">
													<label>Status</label>
													<select required class="form-control select2" id="filter_status" name="filter_status">
														<option value="">Select Status</option>
														<?php foreach($status as $row) { ?>
														<option value="<?php echo $row->followup_type_name; ?>"><?php echo $row->followup_type_name; ?></option>
														<?php } ?>
													</select>
												</div>

												@if($isLeadsList)
													<div class="lead-field">
														<label>Assignment</label>
														<select id="assign_filter" class="form-control">
															<option value="">All leads</option>
															<option value="assigned">Assigned</option>
															<option value="unassigned">Unassigned</option>
														</select>
													</div>
												@endif

												<div class="lead-field lead-field--btn">
													<button type="button" class="btn btn-primary lead-apply"><i class="bx bx-filter-alt"></i> Apply Filters</button>
													<button type="button" class="btn btn-light lead-reset"><i class="bx bx-reset"></i> Reset</button>
												</div>
											</div>

											@include("leads::filter_form")

											{{-- Bulk actions --}}
											<form id="assignForm" class="lead-actionbar">
												<div class="lead-field lead-field--assign">
													<label>Assign to staff</label>
													{!! html()->select('assign_lead_staff', $users , null)->attributes([ 'class'=>'form-select form-control select2','id'=> 'assign_lead_staff','data-parsley-errors-container'=>"#error_assigns",'required'=>'required','data-parsley-required-message'=>'Please Select a Staff'])->placeholder('Select Staff') !!}
													<div id="error_assigns" style="color:red;font-size:12px"></div>
													<div id="error_assigns_lead" style="color:red;font-size:12px"></div>
												</div>
												<div class="lead-actionbar__btns">
													<a type="button" id="assign_btn" class="btn btn-primary"><i class="bx bx-user-check"></i> Assign</a>
													<a type="button" id="assign_btn_delete" class="btn btn-warning"><i class="bx bx-trash"></i> Delete</a>
													<a href="{{url('leads/exportLeads')}}" id="excelexport" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel Export</a>
													@if($isLeadsList)
														<button type="button" id="multiAssignBtn" class="btn btn-brand"><i class="bx bx-user-plus"></i> Multiple Lead Assignment</button>
													@endif
													<span class="highlight"></span>
												</div>
											</form>

										</div>
									<!-------------------------- Assign -------------------------->
										<div style="margin-top:1px" class="table-responsive">
											<!--h4 class="card-title">Added Company</h4-->
											<!--p class="card-title-desc"></p-->
			
											<table id="lead_table" class="table table-bordered dt-responsive " style="border-collapse: collapse; border-spacing: 0; width: 100%;">  <!-- nowrap -->
												<thead>
												<tr>
													<th><input name="select_all" value="1" type="checkbox" style="display:none;"></th>
													<th class="sno">#</th>
													<th style="text-align:center" width="9%!important">Date</th>
													<th>ID</th>
													<th>Name</th>
													<th>Mobile</th>
													<th>Package</th> 
													<th>Form Type</th> 
													<th>Source </th>
													<th style="text-align:center">Assign To</th>
													<th style="text-align:center">Scheduled Date</th>
													<th>Status</th>
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
                </div>
                <!-- End Page-content -->
          
<!------------------------  Multiple Lead Assignment modal  ---------------------->
	<div class="modal fade" id="multiAssignModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content mla-modal">
				<div class="modal-header">
					<h5 class="modal-title"><i class="bx bx-user-plus"></i> Multiple Lead Assignment</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mla-count">Selected leads <span id="mla_count" class="mla-badge">0</span></div>

					<div class="mla-mode">
						<label class="mla-mode__opt is-active"><input type="radio" name="mla_mode" value="all" checked> <i class="bx bx-group"></i> Assign all to one staff</label>
						<label class="mla-mode__opt"><input type="radio" name="mla_mode" value="each"> <i class="bx bx-user-pin"></i> Assign individually</label>
					</div>

					<label class="mla-label">Set inspection template &amp; date per lead</label>
					<div class="mla-list-wrap">
						<div id="mla_list" class="mla-list"><div class="text-muted p-2">No leads selected.</div></div>
					</div>

					<div id="mla_all_wrap">
						<label class="mla-label">Assign all to</label>
						<select id="mla_staff" class="form-select">
							<option value="">Select staff</option>
							@foreach(($users ?? []) as $uid => $uname)
								<option value="{{ $uid }}">{{ $uname }}</option>
							@endforeach
						</select>
					</div>
					<div id="mla_error" class="mla-error"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-brand" id="mla_submit"><i class="bx bx-check"></i> Assign Leads</button>
				</div>
			</div>
		</div>
	</div>

<!------------------------     followup modal      ---------------------->
	<div class="modal fade bs-example-modal-xl show" id="modal_followup"  role="dialog" aria-labelledby="exampleModalCenterLabel"  aria-modal="true" style="display:none">
		<div class="modal-dialog modal-dialog-scrollable modal-xl">
			<div class="modal-content" style="">
				<div class="modal-header">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"> </span></button>
                </div>
                <div class="modal-body">
                	<div id="view_modal_body"> </div>
                       <!--<input type="text" id="modal_lead_ids" name="modal_lead_ids" >-->
                    <div class="card">
                    	<div class="card-body">
                            <form id="followup_form">
                            	<input type="hidden" id="modal_lead_id" name="modal_lead_id" value="">
                            	<input type="hidden" name="statususer" id="statususer" >
								
                            	<div class="row">
									<div class="col-md-2">
										<div class="mb-2">
											<label class="col-form-label">Status<span class="text-red"> </span></label>

											<select name="follow_status" id="follow_status" required class="form-control form-select select2"  style="width:160px;" onchange="changeStatus(this.value)"  >
											</select>
										</div>
									</div>
									
									<div class="col-md-2" id="div_follow4" style="display:none">
										<div class="mb-2">
										   <label class="col-form-label">Staff<span style=""> </span></label>
										  {!! html()->select('assign_staff', $users , null)->attributes([ 'class'=>'form-control select2','id'=> 'assign_staff', 'placeholder'=>'Select Staff','style'=>'width:160px','data-parsley-errors-container'=>"#error_assign"]) !!}
										  <div id="error_assign"></div>
										</div>
									</div>
                                
									<div class="col-md-2" id="div_follow1" style="display:none">
										<div class="mb-2">
											<label class="col-form-label">Next Followup Date<span class="text-red"></span></label>
											<input type="date"  value="" class="form-control datetimepicker-input " id="follow_next_date" name="follow_next_date" data-toggle="datetimepicker" data-target="#follow_next_date" style="width:160px">
										</div>
									</div>
                               
									<div class="col-md-2" id="div_follow3" style="">
										<div class="mb-2">
										    <label class="col-form-label">Comments<span class="text-red" id="commentid"></span></label>
										    <textarea  name="followup_remark" id="followup_remark" class="form-control" is="followup_remark"  data-parsley-errors-container="#error_followup_remark" style="height:39px;resize:none;"></textarea>
										    <div id="error_followup_remark"></div> 
										</div>
									</div>
                                 
									<div class="col-md-1" id="btn_followup_div" style="margin-top:30px">
										<div class="mb-3">
											 <button type="button" onclick="addFollowUp(modal_lead_id.value,follow_next_date.value,follow_status.value,followup_remark.value,assign_staff.value)"  class="btn btn-primary" id="folsub">Submit</button>
										</div>
									</div>   
                                
                            	</div>
                        	</form>
                        
							<div class="row" >
								<div class="col-md-12">
									<div id="view_modal_body_follow">  </div>
								</div>
							</div>
                        </div>
                     </div>
					
                    </div>
				
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
	</div>    
<!------------------------   View modal  ---------------------->
<!---------------------- Delete Lead ------------------------------>
	<div class="modal fade" id="delete_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete Lead</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						{!! html()->form('POST')->attributes(['url' =>'','method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}   
 						<input type='hidden' name='_token' value='{{csrf_token()}}'> 
						<input type='hidden' name='del_lead_id' id="del_lead_id" value=''> 
						 Are you sure want to delete the lead <span id="delete_lead"></span>
						 {!! html()->form()->close() !!} 
                    </div>
                    <div class="modal-footer">
						<button type="button" class="btn btn-primary" onclick="deleteLead(del_lead_id.value)">Delete</button>
                        <!--button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button-->
						<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
			</div>
	</div>
<!---------------------- Delete Lead -------------------------------->
 @endsection

@section('css')
<style>
    :root { --ld-dark:#00263D; --ld-brand:#04B084; }

    /* Page + card */
    .page-title-box h4 { color:var(--ld-dark); font-weight:700; }
    .page-content .card { border:0; border-radius:16px; box-shadow:0 4px 20px rgba(16,40,70,.06); }
    .page-content .card > .card-body { padding:22px 24px; }

    /* Filter + form controls */
    .page-content .card-body .form-control, .page-content .card-body .form-select { border:1px solid #e4e8ee; border-radius:10px; font-size:13.5px; height:38px; }
    .form-control:focus, .form-select:focus { border-color:var(--ld-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }

    /* Modern filter bar + action toolbar */
    .lead-controls { margin-bottom:18px; }
    .lead-filterbar { display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; background:#f7f9fc; border:1px solid #eef1f5; border-radius:14px; padding:16px; }
    .lead-field { display:flex; flex-direction:column; gap:5px; flex:1 1 168px; min-width:150px; }
    .lead-field > label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#98a2b3; margin:0; }
    .lead-field .select2-container { width:100% !important; }
    .lead-field--btn { flex:0 0 auto; min-width:auto; flex-direction:row; gap:8px; align-items:flex-end; }
    /* Make the WHOLE button the clickable hit area (icon + text + padding), and
       keep the buttons above any adjacent select2 overlay so no part of the
       button is covered — otherwise only the bare text region fires the click. */
    .lead-apply, .lead-reset { height:38px; min-width:120px; white-space:nowrap; display:inline-flex;
        align-items:center; justify-content:center; gap:6px; padding:0 18px; cursor:pointer;
        position:relative; z-index:5; }
    /* Every child (icon + text) passes the click through to the button, so the
       ENTIRE button area is clickable, not just the text. */
    .lead-apply *, .lead-reset * { pointer-events:none; }
    .lead-actionbar { display:flex; flex-wrap:wrap; align-items:flex-end; gap:16px; margin-top:16px; padding-top:16px; border-top:1px dashed #e6eaf0; }
    .lead-field--assign { flex:0 0 240px; max-width:240px; position:relative; }
    /* keep the error hints from adding height so the buttons align with the select */
    .lead-field--assign #error_assigns, .lead-field--assign #error_assigns_lead { position:absolute; top:100%; left:0; right:0; margin-top:2px; }
    .lead-actionbar__btns { display:flex; align-items:flex-end; gap:8px; flex-wrap:wrap; }
    .lead-actionbar__btns .btn { height:38px; margin:0; display:inline-flex; align-items:center; gap:6px; }
    #assign_btn_delete { color:#fff; }

    /* select2 to match */
    .select2-container--default .select2-selection--single { border:1px solid #e4e8ee !important; border-radius:10px !important; height:38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px !important; color:#344054; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--ld-brand) !important; }

    /* Toolbar buttons */
    .btn { border-radius:9px; }
    #assign_btn { background:var(--ld-dark); border-color:var(--ld-dark); color:#fff; font-weight:600; }
    #assign_btn:hover { background:var(--ld-brand); border-color:var(--ld-brand); color:#fff; }
    #excelexport { background:var(--ld-brand) !important; border-color:var(--ld-brand) !important; width:auto !important; }
    #excelexport:hover { background:var(--ld-dark) !important; border-color:var(--ld-dark) !important; }

    /* Horizontal scroll — keep all columns (incl. Action icons) on one line */
    .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .dataTables_wrapper .dataTables_scrollBody { overflow-x:auto !important; }
    #lead_table thead th, #lead_table tbody td { white-space:nowrap; }
    #lead_table td.actionwidth, #lead_table th.actionwidth { white-space:nowrap; min-width:120px; }
    #lead_table td:last-child a, #lead_table td:last-child i { display:inline-block; }

    /* Name → view-details link */
    #lead_table a.lead-name-link { color:var(--ld-dark); font-weight:600; text-decoration:none; }
    #lead_table a.lead-name-link:hover { color:var(--ld-brand); text-decoration:underline; }

    /* Brand button */
    .btn-brand { background:var(--ld-dark); border-color:var(--ld-dark); color:#fff; font-weight:600; }
    .btn-brand:hover { background:var(--ld-brand); border-color:var(--ld-brand); color:#fff; }

    /* Multiple Lead Assignment — render as a clearly visible, fully clickable button */
    #multiAssignBtn { display:inline-flex; align-items:center; justify-content:center; gap:6px;
        height:38px; padding:0 18px; background:var(--ld-brand); border:1px solid var(--ld-brand);
        color:#fff !important; font-weight:600; white-space:nowrap; cursor:pointer; }
    #multiAssignBtn:hover { background:var(--ld-dark); border-color:var(--ld-dark); color:#fff !important; }
    #multiAssignBtn i { pointer-events:none; }

    /* Multiple Lead Assignment modal */
    .mla-modal { border:0; border-radius:16px; overflow:hidden; }
    .mla-modal .modal-header { background:linear-gradient(120deg,#00263D,#04B084); color:#fff; border:0; padding:16px 20px; }
    /* Bootstrap sets h5{color:var(--bs-dark)} directly on the element, which
       otherwise hides the white title on the dark gradient — force white here. */
    .mla-modal .modal-title, .mla-modal .modal-title i { color:#fff !important; font-weight:700; }
    .mla-modal .modal-title { display:flex; align-items:center; gap:8px; font-size:17px; }
    .mla-modal .btn-close { filter:invert(1) grayscale(1) brightness(2); }
    .mla-count { font-weight:700; color:var(--ld-dark); margin-bottom:10px; }
    .mla-badge { display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:22px; padding:0 8px; border-radius:20px; background:#e7f8ef; color:var(--ld-brand); font-size:12.5px; margin-left:4px; }
    /* mode toggle */
    .mla-mode { display:flex; gap:8px; margin-bottom:14px; }
    .mla-mode__opt { flex:1; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; margin:0;
        border:1px solid #e4e8ee; border-radius:10px; padding:9px 10px; font-size:13px; font-weight:600; color:#667085; transition:all .12s; }
    .mla-mode__opt input { display:none; }
    .mla-mode__opt.is-active { border-color:var(--ld-brand); background:#e7f8ef; color:var(--ld-brand); }
    .mla-mode__opt i { font-size:16px; }

    .mla-list-wrap { max-height:300px; overflow-y:auto; border:1px solid #eef1f5; border-radius:12px; padding:6px; margin-bottom:16px; background:#f9fbfc; }
    .mla-list { margin:0; padding:0; }
    .mla-row { display:grid; grid-template-columns:1.4fr 1.2fr 1fr; gap:10px; align-items:center; padding:7px 10px; border-radius:8px; }
    .mla-list.mla-mode-each .mla-row { grid-template-columns:1.3fr 1fr .9fr 1fr; }
    .mla-row + .mla-row { border-top:1px solid #eef1f5; }
    .mla-row--head { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; border-bottom:1px solid #eef1f5; }
    .mla-row__name { display:flex; align-items:center; gap:6px; font-size:13.5px; color:#344054; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .mla-row__name i { color:var(--ld-brand); }
    .mla-row .form-select, .mla-row .form-control { border:1px solid #e4e8ee; border-radius:8px; }
    .mla-row--err .mla-row__type { border-color:#e5484d; }
    .mla-row--err-staff .mla-row__staff { border-color:#e5484d; }
    #multiAssignModal .mla-row__staff { display:none !important; }
    #multiAssignModal .mla-list.mla-mode-each .mla-row__staff { display:block !important; }
    @media (max-width:575px){ .mla-row, .mla-list.mla-mode-each .mla-row { grid-template-columns:1fr; gap:5px; } .mla-row--head { display:none; } }
    .mla-label { font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; margin-bottom:5px; }
    #mla_staff { border:1px solid #e4e8ee; border-radius:10px; }
    #mla_staff:focus { border-color:var(--ld-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    .mla-error { color:#e5484d; font-size:12.5px; margin-top:8px; min-height:16px; }
    /* Footer buttons — Cancel + Assign Leads */
    .mla-modal .modal-footer { border-top:1px solid #eef1f5; padding:14px 18px; gap:10px; }
    .mla-modal .modal-footer .btn { height:40px; padding:0 22px; border-radius:10px; font-weight:600;
        display:inline-flex; align-items:center; justify-content:center; gap:6px; transition:all .15s; }
    .mla-modal .modal-footer .btn i { pointer-events:none; }
    .mla-modal .modal-footer .btn-light { background:#f2f5f8; border:1px solid #e4e8ee; color:#5b6472; }
    .mla-modal .modal-footer .btn-light:hover { background:#e9edf2; border-color:#d5dce4; color:#344054; }
    .mla-modal #mla_submit { background:var(--ld-brand); border:1px solid var(--ld-brand); color:#fff;
        box-shadow:0 6px 16px rgba(4,176,132,.28); }
    .mla-modal #mla_submit:hover { background:var(--ld-dark); border-color:var(--ld-dark); color:#fff;
        box-shadow:0 8px 20px rgba(0,38,61,.30); }

    /* Table */
    #lead_table { border:0 !important; }
    #lead_table thead th { background:#f7f9fc; color:#98a2b3; text-transform:uppercase; font-size:11px; letter-spacing:.4px;
        font-weight:700; border:0 !important; border-bottom:1px solid #eef1f5 !important; padding:12px 10px; vertical-align:middle; white-space:nowrap; }
    #lead_table tbody td { border:0 !important; border-bottom:1px solid #f0f2f6 !important; padding:12px 10px; vertical-align:middle;
        font-size:13.5px; color:#344054; }
    #lead_table tbody tr:hover td { background:#fafcff; }
    #lead_table .badge { border-radius:20px; padding:5px 11px; font-weight:600; font-size:11.5px; }
    #lead_table .btn, #lead_table a.btn { border-radius:8px; }

    /* DataTable generated controls */
    .dataTables_wrapper .dataTables_filter input { border:1px solid #e4e8ee; border-radius:9px; padding:7px 12px; margin-left:8px; outline:none; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color:var(--ld-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    .dataTables_wrapper .dataTables_length select { border:1px solid #e4e8ee; border-radius:9px; padding:5px 26px 5px 10px; }
    .dataTables_wrapper .dataTables_info { color:#98a2b3; font-size:12.5px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:8px !important; border:0 !important; margin:0 2px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:var(--ld-brand) !important; border:0 !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--ld-dark) !important; border:0 !important; color:#fff !important; }

    /* ============================================================
       Distinct look for the Leads (leadslist) page — .ll-page
       ============================================================ */
    .ll-page { background:
        radial-gradient(1200px 240px at 100% -60px, rgba(4,176,132,.10), transparent 60%),
        radial-gradient(900px 220px at -10% -40px, rgba(0,38,61,.08), transparent 55%); }

    /* Gradient hero */
    .ll-hero { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;
        background:linear-gradient(120deg, #00263D 0%, #06655a 60%, #04B084 130%); color:#fff;
        border-radius:20px; padding:22px 28px; margin-bottom:22px; box-shadow:0 14px 34px rgba(0,38,61,.22); position:relative; overflow:hidden; }
    .ll-hero::after { content:''; position:absolute; right:-40px; top:-60px; width:220px; height:220px; border-radius:50%;
        background:rgba(255,255,255,.06); }
    .ll-hero__left { display:flex; align-items:center; gap:16px; z-index:1; }
    .ll-hero__icon { width:56px; height:56px; border-radius:16px; background:rgba(255,255,255,.14); display:flex; align-items:center; justify-content:center; font-size:30px; }
    .ll-hero__eyebrow { font-size:12px; letter-spacing:.5px; text-transform:uppercase; opacity:.8; }
    .ll-hero__title { font-size:26px; font-weight:800; margin:2px 0 2px; color:#fff; }
    .ll-hero__sub { margin:0; font-size:13px; opacity:.85; }
    .ll-hero__crumb { background:transparent !important; z-index:1; }
    .ll-hero__crumb .breadcrumb-item a, .ll-hero__crumb .breadcrumb-item.active { color:rgba(255,255,255,.92) !important; }
    .ll-hero__crumb .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.55) !important; }

    /* Elevated, rounder card with a green top edge */
    .ll-page .card { border-radius:20px; box-shadow:0 12px 32px rgba(0,38,61,.09); overflow:hidden; }
    .ll-page .card::before { content:''; display:block; height:4px; background:linear-gradient(90deg, var(--ld-dark), var(--ld-brand)); margin:-1px -1px 0; }

    /* Filter bar — white, elevated, green accent (vs My Leads' grey) */
    .ll-page .lead-filterbar { background:#fff; border:1px solid #eef1f5; border-left:4px solid var(--ld-brand);
        box-shadow:0 4px 16px rgba(16,40,70,.05); }

    /* Pill buttons */
    .ll-page .btn, .ll-page .lead-actionbar__btns .btn, .ll-page .lead-reset { border-radius:30px; }

    /* Dark, distinct table header + zebra rows + smooth row motion */
    .ll-page #lead_table thead th { background:linear-gradient(90deg, #00263D, #0b466b); color:#dbeee7 !important;
        border-bottom:0 !important; }
    .ll-page #lead_table tbody tr td { transition:background .15s, transform .15s; }
    .ll-page #lead_table tbody tr:nth-child(even) td { background:#f8fbfc; }
    .ll-page #lead_table tbody tr:hover td { background:#eefaf5; }
    .ll-page #lead_table .badge { box-shadow:0 2px 6px rgba(16,40,70,.12); }

    /* Softer, elevated select/inputs on hover */
    .ll-page .lead-field .form-control:hover, .ll-page .lead-field .select2-selection:hover { border-color:#cfd6df; }
</style>
@endsection

 @section('js')
<script type="text/javascript">
	var public_path              = '<?php echo url('/');?>';
	var url_lead_table           = '{{ URL::to(request()->segment(1)."/get-list") }}';
	var url_setfilter_staff      = "{{url('/leads/setFilterStaff')}}";
	var url_setfilter_status     = "{{url('/leads/setFilterStatus')}}";
	var url_view_followup        = "{{url('/leads/set_lead_session')}}";
	var url_view_followup_table  = "{{url('/leads/set_lead_session_followtable')}}";
	var url_staffData            = '{{URL::to("leads/getstaff")}}'; 
	var url_add_followup         = "{{url('/leads/add_followup')}}";
	var url_view_followup_tables = "{{url('/leads/set_lead_session_followtables')}}";
	var url_assignenquery        = '{{URL::to("leads/assignenquerydata")}}';
	var url_edit                 = "{{url('leads/getleadsdata')}}";
	var url_edit_lead            = "{{url('/leads')}}"; 
</script>
      
		<!-- Required datatable js -->
        <script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
		<!-- Datatable init js -->
        <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
        <!-- plugins -->
        <script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>       
        <!-- init js -->
        <script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>
		
	   	<!-- parsleyjs -->
		<script src="{{asset('assets/libs/parsleyjs/parsley.min.js')}}"></script>
		<script src="{{asset('module.js/Leads/index_leads.js?ver=3.6')}}"></script>
		<script src="{{asset('module.js/Leads/main.js?ver=1.2') }}"></script>  
		<script src="{{asset('module.js/main.js?ver=1.3')}}"></script>
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

</script>
	
<script>
	var url       = '<?php $current_route = \Route::current()->uri(); echo $current_route;?>';
	var privilage = '<?php $privilage     = Auth::user()->previlage;echo $privilage;?>';
		
		if(url=="view_leads")
		{
		     var url_datatable = "{{url('/view_leads/get-list')}}";
		}
		else if(url=="myleads")
		{
		     var url_datatable = "{{url('/myleads/get-list')}}";
		}
</script>
<!--get role wise permissiom ajax script-->
 
<script>
var token = $('meta[name="csrf-token"]').attr('content');
// Apply every filter selection together when the user clicks "Apply Filters".
// All server-side filters (dates + source, staff, status) are saved to the
// session first, and the table is reloaded only ONCE after every save has
// finished. Doing a single coordinated reload removes the stale-session race
// that previously made the Staff filter return the wrong results. The
// Assignment filter (#assign_filter) is read live by the DataTable ajax
// callback, so it applies automatically on this reload.
function applyFilters()
{
	var token = $('meta[name="csrf-token"]').attr('content');
	var $btn  = $('.lead-apply');
	$btn.prop('disabled', true);

	// One request writes every filter to the session atomically; the table is
	// reloaded only after it returns. This removes the multi-request race that
	// previously required several clicks before the filter took effect.
	$.ajax({
		type: 'POST',
		url : "{{ url('/leads/filter') }}",
		data: {
			'_token'       : token,
			'from_date'    : $('#from_date').val(),
			'to_date'      : $('#to_date').val(),
			'source'       : $('#filter_source').val(),
			'date_type'    : $('#filter_date_type').val(),
			'staff'        : $('#filter_staff').val(),
			'status'       : $('#filter_status').val(),
			'assign_status': $('#assign_filter').val() || ''
		}
	}).always(function () {
		$('#lead_table').DataTable().ajax.reload(null, false);
		$btn.prop('disabled', false);
	});
}

// Reset clears every filter. The page-load handler (view_all_index) already
// forgets all filter session keys, so a straight reload is the most reliable
// full reset and repaints the controls from the now-empty session.
function resetFilters()
{
	window.location.reload();
}

// Delegated handlers: a click anywhere on the button element (icon, text or
// padding) fires the action. Delegation + the pointer-events rule above make
// the whole button clickable, not just the text.
$(document).on('click', '.lead-apply', function(e){ e.preventDefault(); applyFilters(); });
$(document).on('click', '.lead-reset', function(e){ e.preventDefault(); resetFilters(); });
 
function setFilterstatus(status)
{
       $.ajax({
            type: 'GET',
            url: url_setfilter_status,
            dataType:'JSON',
            data:{'status':status},
            success: function (res) {
                $('#lead_table').DataTable().ajax.reload();
            }
        });
}
 
function updateDataTableSelectAllCtrl(table)
{
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);
   
   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
 
}
 
 // Handle click on checkbox
   $('#lead_table tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

      // Get row data
      var data = dTable.row($row).data();

      // Get row ID
      var rowId = data.lead_id;

      // Determine whether row ID is in the list of selected row IDs
      var index = $.inArray(rowId, rows_selected);

      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(dTable);
      //console.log(rows_selected);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });
   
  //var url_getAllLeads = "{{url('leads/getAllLeads')}}";
  
   $('thead input[name="select_all"]', dTable.table().container()).on('click', function(e){
       
      if(this.checked){
          var searching = $("input[type='search']").val();
           $.ajax({
                   type: 'POST',
                   url: "{{url('/leads/getAllLeads')}}",
            	   dataType:'json',
            	   data:{'_token':"{{csrf_token()}}",'search':searching},
                    success: function (data) {
                        rows_selected=[];
                        rows_selected=data;
                    }
                }); 
          
         $('#lead_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
          rows_selected=[];
         $('#lead_table tbody input[type="checkbox"]:checked').trigger('click');
      } 

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });        

   // Handle table draw event
   dTable.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(dTable);
   });
   
   
   /*********** Assign Lead **************/
var token            = $('meta[name="csrf-token"]').attr('content');
var url_assign_leads = "{{url('/leads/assign_leads')}}";

$("#assign_btn").click(function()
{   
	var staff = $("#assign_lead_staff").val();
   
	$("#error_assigns").html("");
	$("#error_assigns_lead").html("")
	if(staff.length==0)
	{
		$("#error_assigns").html("Please Select Staff");
		return false;
	}
 
	if(rows_selected.length==0)
	{
		$("#error_assigns_lead").html("Please select at least one lead");
		return false;
	}
 
        $.ajax({
            type: 'POST',
            url: url_assign_leads,
            data:{'_token':token,'leads':rows_selected,'user_id':staff},
            success: function (data) {
					 
				Command: toastr["success"](data.text)
						toastr.options = {
						  "heading": "data.heading",
						  "text": "data.text",
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

    		rows_selected = [];
            //$("#lead_table").DataTable().ajax.reload();
    		$("#lead_table").DataTable().ajax.reload( null,false );
    	    $("#assign_lead_staff").val("").trigger('change');
        }
    }); 
})

/*********** Delete Assign Lead **************/

$("#assign_btn_delete").click(function()
{            
         $("#error_assigns").html("");
         $("#error_assigns_lead_delete").html("")
         
        if(rows_selected.length==0)
        {
            $("#error_assigns_lead_delete").html("please select at least one lead");
            return false;
        }
        $.ajax({
                type: 'POST',
                url: "{{url('/leads/assign_leads_delete')}}",
                dataType:'json',
                data:{'leads':rows_selected,'_token':"{{csrf_token()}}"},
                success: function (data) {
                        
					Command: toastr["success"](data.text)
						toastr.options = {
						  "heading": "data.heading",
						  "text": "data.text",
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
                rows_selected=[];
                $("#lead_table").DataTable().ajax.reload();
                
                    }
                }); 
})
</script>
<script>
	function changeStatus(status,name)
	{
		$(this).val('').trigger('change');
		
		$("#div_follow1").hide();$("#div_follow1").find('input').removeAttr('required');
		$("#div_follow4").hide();$("#div_follow4").find('select').removeAttr('required');
  
        $("#intm").html('');
        $("#inty").html('');
   
		$("#btn_followup_div").attr('class','col-md-3');
		$("#div_follow3").show();
		//$("#commentid").html('*');
		$("#error_followup_remark").show();
          
		//$("#div_follow3").find('textarea').attr('required','required');
		if(status=="1")  // Assign
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			//  $("#div_follow1").find('input').attr('required','required');
			//$("#div_follow4").find('select').attr('required','required');
         
			$("#div_follow3").show();
			$("#commentid").html('');
		}
		else if(status=="2")   // Reassign
		{
			$("#div_follow1").show();
			$("#div_follow4").show();
			$("#div_follow4").show();
                   
			$("#div_follow3").show();
			$("#commentid").html('');        
		}
		else if(status=="3")   // Followup
		{
			$("#div_follow1").show();
		}
		else if(status=="4")  // Registered
		{
			//$("#div_follow1").show();                 
		}
		else if(status=="5")	// Rejected
		{
			//$("#div_follow1").show();                    
		}
		else if(status=="6")	// Closed
		{
			//$("#div_follow1").show();                       
		}
		else if(status=="7")
		{
			$("#div_follow1").show();     
		}
		else
		{
			$("#div_follow1").hide();
		}
	}  
</script> 

<script>
function addFollowUp(id,date,status,remarks,assigned_user,convertstatus)      
{
	 var name  = $('#follow_status option:selected').text();
	 var staff = $("#assign_staff").val();
    
    /* $('#followup_form').parsley().validate();
       if(! $('#followup_form').parsley().isValid())
        {
            return false;
        }*/
	 
	//$("#follow_status").find('select2').attr('required','required');
    $.ajax({
            type: 'POST',
            url: url_add_followup,
            dataType:'json',
            data:{'id':id,'date':date,'status':status,'name':name,'remarks':remarks,'_token':"{{csrf_token()}}",'assinged_user':assigned_user,'staff':staff},
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
						
						$.ajax({
							type: 'GET',
							url: url_view_followup_tables,
							dataType:'html',
							data:{'id':id},
							success: function (data) 
							{
								console.log(id);
                                $("#view_modal_body_follow").html(data);
                            }
                        });      
                    
						$("#followup_form").trigger('reset');
						$('#follow_next_date').val('').trigger('change');
						$('#followup_remark').val('').trigger('change')						
                       // $('#followup_form')[0].reset();
                        $('#assign_staff').val(null).trigger("change");
						$("#lead_table").DataTable().ajax.reload();
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

<!-- Multiple Lead Assignment modal logic -->
<script type="text/javascript">
    (function () {
        var openBtn = document.getElementById('multiAssignBtn');
        if (!openBtn) return;
        var modalEl = document.getElementById('multiAssignModal');
        var TYPES = {!! json_encode($inspectionTypes ?? []) !!};   // {id: name}
        var STAFF = {!! json_encode($users ?? []) !!};             // {id: name}

        function typeOptions() {
            var html = '<option value="" disabled selected>Template</option>';
            Object.keys(TYPES).forEach(function (id) {
                html += '<option value="' + id + '">' + TYPES[id] + '</option>';
            });
            return html;
        }
        function staffOptions() {
            var html = '<option value="">Staff</option>';
            Object.keys(STAFF).forEach(function (id) {
                html += '<option value="' + id + '">' + STAFF[id] + '</option>';
            });
            return html;
        }
        function currentMode() {
            var r = document.querySelector('input[name="mla_mode"]:checked');
            return r ? r.value : 'all';
        }
        function applyMode() {
            var each = currentMode() === 'each';
            document.getElementById('mla_list').classList.toggle('mla-mode-each', each);
            document.getElementById('mla_all_wrap').style.display = each ? 'none' : '';
            document.querySelectorAll('.mla-mode__opt').forEach(function (o) {
                o.classList.toggle('is-active', o.querySelector('input').checked);
            });
        }
        document.querySelectorAll('input[name="mla_mode"]').forEach(function (r) {
            r.addEventListener('change', applyMode);
        });

        function selectedLeads() {
            var out = [];
            document.querySelectorAll('#lead_table tbody tr').forEach(function (tr) {
                var cb = tr.querySelector('input[type="checkbox"]');
                if (!cb || !cb.checked) return;
                var sel = tr.querySelector('.change_staff');
                var link = tr.querySelector('a.lead-name-link');
                var id = sel ? sel.getAttribute('data-enq_id') : (link ? link.getAttribute('href').split('/').pop() : null);
                var name = (tr.children[4] ? tr.children[4].textContent.trim() : '') || ('Lead #' + id);
                if (id) out.push({ id: id, name: name });
            });
            return out;
        }

        openBtn.addEventListener('click', function () {
            var leads = selectedLeads();
            document.getElementById('mla_count').textContent = leads.length;
            document.getElementById('mla_error').textContent = '';
            document.getElementById('mla_staff').value = '';
            var list = document.getElementById('mla_list');

            if (leads.length === 0) {
                list.innerHTML = '<div class="text-muted p-2">No leads selected. Tick the checkboxes in the list first.</div>';
            } else {
                list.innerHTML =
                    '<div class="mla-row mla-row--head"><span>Lead</span><span>Template</span><span>Date</span><span class="mla-row__staff">Staff</span></div>' +
                    leads.map(function (l) {
                        return '<div class="mla-row" data-lead-id="' + l.id + '">' +
                            '<span class="mla-row__name" title="' + l.name + '"><i class="bx bx-user"></i> ' + l.name + '</span>' +
                            '<select class="form-select form-select-sm mla-row__type">' + typeOptions() + '</select>' +
                            '<input type="date" class="form-control form-control-sm mla-row__date">' +
                            '<select class="form-select form-select-sm mla-row__staff">' + staffOptions() + '</select>' +
                            '</div>';
                    }).join('');
            }
            document.querySelector('input[name="mla_mode"][value="all"]').checked = true;
            applyMode();
            new bootstrap.Modal(modalEl).show();
        });

        document.getElementById('mla_submit').addEventListener('click', function () {
            var rows = document.querySelectorAll('#mla_list .mla-row[data-lead-id]');
            var err = document.getElementById('mla_error');
            var each = currentMode() === 'each';
            var allStaff = document.getElementById('mla_staff').value;

            if (rows.length === 0) { err.textContent = 'No leads selected.'; return; }
            if (!each && !allStaff) { err.textContent = 'Please select a staff member.'; return; }

            var body = new URLSearchParams();
            if (!each) { body.append('user_id', allStaff); }
            var missingType = false, missingStaff = false;
            rows.forEach(function (row) {
                var id = row.getAttribute('data-lead-id');
                var type = row.querySelector('.mla-row__type').value;
                var date = row.querySelector('.mla-row__date').value;
                var staff = row.querySelector('.mla-row__staff').value;
                row.classList.toggle('mla-row--err', !type);
                row.classList.toggle('mla-row--err-staff', each && !staff);
                if (!type) { missingType = true; return; }
                if (each && !staff) { missingStaff = true; return; }
                body.append('leads[]', id);
                body.append('inspection_type_id[' + id + ']', type);
                body.append('scheduled_at[' + id + ']', date);
                body.append('date[' + id + ']', date);
                if (each) { body.append('user_id[' + id + ']', staff); }
            });
            if (missingType)  { err.textContent = 'Please choose a template for each lead.'; return; }
            if (missingStaff) { err.textContent = 'Please choose a staff member for each lead.'; return; }

            var btn = this; btn.disabled = true; err.textContent = '';
            fetch("{{ url('leads/assign_leads') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (res) {
                bootstrap.Modal.getInstance(modalEl).hide();
                if (window.jQuery) $('#lead_table').DataTable().ajax.reload(null, false);
                // Show the assigned / skipped-completed summary returned by the server.
                if (res && res.text && window.toastr) {
                    var kind = (res.skipped > 0 && res.assigned === 0) ? 'warning' : 'success';
                    toastr[kind](res.text);
                }
            })
            .catch(function () { err.textContent = 'Assignment failed. Please try again.'; })
            .finally(function () { btn.disabled = false; });
        });
    })();
</script>

@endsection     
