@extends('layouts.myfudapp')
@section('content')
<div class="container-fluid innerdash panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <ul class="breadcrumb">
    <li><a href="{{URL::to('/dashboard')}}">DashBoard</a></li>
    <li>Manage</li>
    <li>Branch</li>
  </ul>
  <div class="clearfix"></div>
  <!-- accordian divs -->
  <?php
  $option1 = json_decode($option->opset_options);
  if(in_array('1',$option1) || in_array('2',$option1)){
  
  ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a  role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <i class="more-less fa fa-minus-square-o"></i>
          <span class="fa fa-pencil-square-o"></span> Manage Branch
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <!-- accordian divs -->
        <div class="row authentication-form mx-auto">  
         
			{!! html()->form('POST')->attributes(['class'=> 'myform', 'id' => 'createForm','files' => true])->open() !!}
		
			<input type='hidden' name='_token' value='{{csrf_token()}}'>
			
			  <div class="col-md-4">
				  <div class="group material-input">
				  <input type='hidden' name='edit_id' id="edit_id">
					<select  class="form-control" id="company_i" name="company_i" data-company="" data-parsley-required-message="Company Required" required>
					  <option value="">--Select Company--</option>
					  <?php 
					  foreach ($company as $value) 
						{
						?>
						<option value="<?=$value->company_id;?>" ><?=$value->company_name;?></option>
						<?php 
						}
					  ?>  
					</select>
					<span class="highlight"></span>
				  </div>
				</div>
            
				<div class="col-md-4" > 
					<div class="group material-input">       
						{!! html()-> text('branch_name','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Branch Name Required', 'class'=>'form-control', 'id'=>'branch_name', 'onkeypress="return alphaOnly(event)"'])!!}  
						<span class="highlight"></span>
						<span id="branch_name-parsley-error"></span>
						<label>Branch Name</label>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_code','')->attributes(['required'=>'required', 'placeholder'=>"", 'data-parsley-required-message'=>'Branch Code Required', 'class'=>'form-control', 'id'=>'branch_code'])!!}
						<span class="highlight"></span>
						<span id="branch_code-parsley-error"></span>
						<label>Branch Code</label>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_person','')->attributes(['class'=>'form-control', 'id'=>'branch_person', 'onkeypress="return alphaOnly(event)"'])!!}                 
						<span class="highlight"></span>
						<label>Contact Person</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_mob','')->attributes(['class'=>'form-control', 'id'=>'branch_mob', 'onkeypress="return isNumberKey(event)"', 'maxlength="15"'])!!}
						<span class="highlight"></span>
						<label>Contact Mobile</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_lan','')->attributes(['class'=>'form-control', 'id'=>'branch_lan', 'onkeypress="return isNumberKey(event)"', 'maxlength="15"'])!!}
						<span class="highlight"></span>
						<label>Contact Landline</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_email','')->attributes(['class'=>'form-control','id'=>'branch_email'])!!}
						<span class="highlight"></span>
						<label>Email</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_web','')->attributes(['class'=>'form-control','id'=>'branch_web'])!!}
						<span class="highlight"></span>
						<label>Website</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_gst','')->attributes(['class'=>'form-control','id'=>'branch_gst'])!!}
						<span class="highlight"></span>
						<label>GST</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						<!-- <input type="file" name='branch_logo' class='form-control' id='branch_logo'> -->
						{!! html()->file('branch_logo', ['class' => 'form-control','type'=>'file']) !!}
						<span class="highlight"></span>
						<label>Logo</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						<textarea name="branch_address" id="branch_address" class="form-control"></textarea>
						<span class="highlight"></span>
						<label>Address</label>
					</div>
				</div>
			
				<div class="col-md-4">
					<div class="group material-input">
						{!! html()-> text('branch_pin','',array('class'=>'form-control','id'=>'branch_pin'))!!}
						<span class="highlight"></span>
						<label>Pincode</label>
					</div>
				</div>
			
				<div class="col-md-4">
				  <div class="group material-input">
					<select  class="form-control" id="branch_country" name="branch_country">
					  <option value="">--Select Country--</option>
					  <?php 
					  foreach ($country as $value) 
						{
						?>
						<option value="<?=$value->id;?>" <?php if($value->id == 101) { ?>Selected <?php } ?>><?=$value->name;?></option>
						<?php 
						}
					  ?>  
					</select>
					<span class="highlight"></span>
				  </div>
				</div>
			
				<div class="col-md-4 pt-2">
				  <div class="group material-input">
					<select  class="form-control" id="branch_state" name="branch_state" data-state="">
					  <option value="">--Select State--</option>
					  <?php
							
						foreach($state as $value)
						{
					?>
						<option value="<?=$value->id;?>" <?php if($value->id == 19) { ?>Selected <?php } ?>><?=$value->name;?></option>
						
						
						
					<?php
						}
							
					  ?>
					</select>
					<span class="highlight"></span>
				  </div>
				</div>
				
				<div class="col-md-4 pt-2" style="display:none;">
				  <div class="group material-input">
					<select name="branch_district" id="branch_district" class="form-control" data-city="">
					  <option value="">---Select District---</option>
					 <?php 
					 foreach($district as $val)
					 {
						?>
						<option value="<?= $val->district_id; ?>"><?= $val->district_name; ?></option>
						
					 <?php } ?>
					</select>
				  </div>
				</div>
				
				<div class="col-md-4 pt-2">
					<div class="group material-input">
						{!! html()-> text('branch_lat','')->attributes(['class'=>'form-control', 'id'=>'branch_lat', 'onkeypress="return lat(event)"'])!!}
						<span class="highlight"></span>
						<label>Latitude</label>
					</div>
				</div>
			
				<div class="col-md-4 pt-2">
					<div class="group material-input">
						{!! html()-> text('branch_long','')->attributes(['class'=>'form-control', 'id'=>'branch_long', 'onkeypress="return lat(event)"'])!!}
						<span class="highlight"></span>
						<label>Longitude</label>
					</div>
				</div>
            
				<div class="col-md-2 pt-2 editButton" style="display: none;">
					<a href="#" onclick="createOrUpdate('{{URL::to("branch/edit_branch")}}', 'createForm', 'edit-modal', 'branchDatatable','')" class="btn btn-info btn-block">
						<i class="fa fa-save"></i>
						Update
					</a>
				</div>
				
				<div class="col-md-4 pt-2 saveButton">
					<a href="#" onclick="createOrUpdate('{{URL::to("branch/add_branch")}}', 'createForm', 'add-modal', 'branchDatatable','')" class="btn btn-info btn-block">
						<i class="fa fa-save"></i> 
						Save </a>
					<a href="#" onclick="clearForm()" class="btn btn-warning btn-block">
						<i class="fa fa-refresh"></i>  Reset </a>
				</div>
         
			</div>
		</div>
		
      <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
  </div>
  <div class="clearfix"></div>
  <?php } ?>
  <div class="panel panel-default">
    <div class="clearfix"></div>
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          <i class="more-less fa fa-minus-square-o"></i>
          <span class="fa fa-bullseye"></span> Added Branch
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse container-fluid show" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
        <div class="table-responsive m-t-40">
          <table id="branchDatatable" class="mytable table table-bordered table-hover">
            <thead>
              <tr>
                <th class="sno">#</th>
                <th>Branch Name</th>
                <th>Branch Code </th>
                <th>Person</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>GST</th>
                <th>Logo </th>
                <th class="actionwidth">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    
    <!-- accordian divs -->
  </div>
</div>
<div class="clearfix"></div>
<div class="modal fade" id="view_modal" role="dialog">
  <div class="modal-dialog modal-lg view-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" >View Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body mbody" id="view-modal-body">
        <div class="table-responsive">
          <div class="model-body-message"></div>  
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

	<div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="staticBackdropLabel">Delete Centre</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							  {!! html()->form('POST')->attributes([''])->open() !!}
							<input type='hidden' name='_token' value='{{csrf_token()}}'> 
							<input type='hidden' name='del_id' id="del_id" value=''> 
							 <p>Are you sure, You want to delete the Centre?</p>
						</div>
						<div class="modal-footer">
							<button type="button" onclick="deleteEntry()" class="btn btn-primary">Delete</button>
							<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
							
						</div>
					</div>
				</div>
			</div>


@endsection

@section('js')
<script type="text/javascript">
  //  var token = $('meta[name="csrf-token"]').attr('content');
  var public_path = '<?php echo url('/');?>';
  var url_branchDataTable = '{{URL::to("branch/getdatatable")}}';
  var url_editbranch = '{{URL::to("branch/get_details")}}';
  var url_viewbranch = '{{URL::to("branch/view_branch")}}';
  var url_deletebranch = '{{URL::to("branch/delete_branch")}}';
</script>
<script src="{{asset('assets/material/js/toastr.js')}}"></script>
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/toast-master/js/jquery.toast.js')}}"></script>
<!-- start - This is for export functionality only -->
<script src="{{asset('assets/material/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/material/js/parsley.js')}}"></script>
<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Branch/index.js?ver=2.6')}}"></script>

<script type="text/javascript">
$( document ).ready(function() 
  {
  $('#branch_country').trigger('change');
  });
$('#branch_country').change(function()
  {
  var country = $(this).val();
  if(country)
    { 
    $.ajax({
          type: 'GET',
          url: 'Branch/get_state',
          data: { country: country },
          success:function(res)
            {              
            $("#branch_state").empty();
            $("#branch_state").append('<option value="">--- State ---</option>');
            $("#branch_city").empty();
            $("#branch_city").append('<option value="">--- City ---</option>');
            if(res)
              {
              var a = $('#branch_state').attr('data-state'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#branch_state").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#branch_state").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              $('#branch_state').trigger('change');
              }
            }
         });
    }
  else
    {
    $("#branch_city").empty();
    $("#branch_city").append('<option value="">--- City ---</option>');
    }      
  });
$('#branch_state').change(function()
  {
  var state = $(this).val();
  if(state)
    { 
    $.ajax({
          type: 'GET',
          url: 'Branch/get_city',
          data: { state: state },
          success:function(res)
            {              
            $("#branch_city").empty();
            $("#branch_city").append('<option value="">--- City ---</option>');
            if(res)
              {
              var a = $('#branch_city').attr('data-city'); 
              $.each(res,function(key,value)
                {
                var x = "";
                if(a === key)
                  {
                  x="selected";
                  $("#branch_city").append('<option value="'+key+'" '+x+'>'+value+'</option>');
                  }  
                else
                  {
                  $("#branch_city").append('<option value="'+key+'">'+value+'</option>');
                  }
                });
              }
            }
         });
    }
  else
    {
    $("#branch_city").empty();
    $("#branch_city").append('<option value="">--- City ---</option>');
    } 
  });
</script>
@endsection