@extends('layouts.myfudapp')
@section('content')

    <div class="page-content">
        <div class="container-fluid">
            
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"> Models</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Models</li>
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
								{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'createForm', 'class'=>'myform'])->open() !!}   
                                
                                    <input type='hidden' name='model_id' id='model_id'>
                                    <input type='hidden' name='edit_id' id="edit_id">
                                    <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                                        <div class="row"> 

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                <label>Make<span class="star_required" style="color:red"> *</span> </label>
                                                    <select name="model_make" id='model_make' class='form-select form-control select2' required='required' data-parsley-errors-container='#user_privilage-parsley-error' data-parsley-required-message='Make Required'>
                                                        <option value="">Select Make</option> 

                                                        <?php $make = DB::table('tbl_make') 
                                                                    ->where('make_status',0) 
                                                                    ->where('make_publish_status',1)
                                                                    ->get(); ?>
                                                        @foreach($make as $val)
                                                            <option value="{{ $val->make_id }}">{{ $val->make_name }}</option>           
                                                        @endforeach
                                                    </select>
                                                <span class="highlight"></span>                        
                                              </div>
                                            </div> 
                                            
                                             <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Model Name')}}<span class="star_required" style="color:red"> *</span></label>
                                                    <input id="model_name" type="text" class="form-control @error('model_name') is-invalid @enderror" name="model_name" placeholder="Model Name" data-parsley-required-message="Model Name required" >
                                                    <div class="help-block with-errors"></div>
                                                    @error('model_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                  
                                            <div class="col-md-3 editButton" style="display:none;margin-top:25px">
                                                <div class="mb-3">
                                                    <a href="#" class="btn btn-primary waves-effect waves-light w-md" onclick="createOrUpdate('{{URL::to("model/editDivision_model")}}', 'createForm', 'update-modal', 'model_table','')" class="btn btn-info btn-block">
                                                        Update </a>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 saveButton" style="margin-top:25px">
                                                <div class="mb-3">
                                                    <a href="#" class="btn btn-primary waves-effect waves-light" onclick="createOrUpdate('{{URL::to("model/add_model")}}', 'createForm', 'add-modal', 'model_table','')" class="btn btn-info btn-block">
                                                        Save </a>&nbsp;
                                                    <a href="#" onclick="clearForm()" class="btn btn-danger"> Reset </a>                                                    
                                                </div>
                                            </div> 
                                            
                                        </div>
                                {!! html()->form()->close() !!} 
                            </div> 
                        </div>
                    </div>
                </div>
            </div><!-- End Form Layout -->
 
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!--  <h4 class="card-title">View clients</h4>
                            <p class="card-title-desc"></p>-->
                            <div class="table-responsive">  
                                <table id="model_table" class="mytable table table-bordered table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#')}}</th>
                                            <th>{{ __('Make')}}</th>
                                            <th>{{ __('Model')}}</th>
                                            <th>{{ __('Added By')}}</th>
                                            <th>{{ __('Status')}}</th>
                                            <th style="text-align:center">{{ __('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>      
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

            <div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Delete Model</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
							{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}  
                            <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                            <input type='hidden' name='del_id' id="del_id" value=''> 
                            <p>Are you sure,You want to delete the Model?</p>
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
    </div><!-- End Page-content -->

@endsection
 
@section('js')
<!-- Required datatable js -->
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<!-- init js -->
<script src="{{asset('assets/js/pages/form-editor.init.js')}}"></script>
 
<script type="text/javascript">
    var public_path           = '<?php echo url('/');?>';
    var public_path_app       = '<?php echo url('/');?>';
    var url_DivisionDataTable = '{{URL::to("model/get-list_model")}}';
    var url_editdivision      = '{{URL::to("model/getDivisions_model")}}';
    var url_deletedivision    = '{{URL::to("model/deleteDivision_model")}}';
    var url_status            = '{{URL::to("model/status_model")}}';
</script>
<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Manage/model.js?ver=1.9')}}"></script>

@endsection     
