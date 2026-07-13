@extends('layouts.myfudapp')
@section('content')

    <div class="page-content">
        <div class="container-fluid">
            
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"> Interior Color</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Interior Color</li>
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
                                
                                    <input type='hidden' name='inte_color_id' id='inte_color_id'>
                                    <input type='hidden' name='edit_id' id="edit_id">
                                    <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                                        <div class="row"> 
 
                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Interior Color in English')}}<span class="text-red" style="color:red"> *</span></label>
                                                    <input id="inte_color_name" type="text" class="form-control @error('inte_color_name') is-invalid @enderror" name="inte_color_name" placeholder="Interior Color Name" data-parsley-required-message="Interior Color Name required" >
                                                    <div class="help-block with-errors"></div>
                                                    @error('inte_color_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <div class="mb-3">
                                                    <label for="name">{{ __('Interior Color in Arabic')}}<span class="text-red" style="color:red">  </span></label>
                                                    <input id="inte_color_name_arabic" type="text" class="form-control @error('inte_color_name_arabic') is-invalid @enderror" name="inte_color_name_arabic" placeholder="Interior Color Name Arabic" data-parsley-required-message="Interior Color Name Arabic required" >
                                                    <div class="help-block with-errors"></div>
                                                    @error('inte_color_name_arabic')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                  
                                            <div class="col-md-3 editButton" style="display:none;margin-top:25px">
                                                <div class="mb-3">
                                                    <a href="#" class="btn btn-primary waves-effect waves-light w-md" onclick="createOrUpdate('{{URL::to("interiorColor/editDivision")}}', 'createForm', 'update-modal', 'interiorColorTable','')" class="btn btn-info btn-block">
                                                        Update </a>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 saveButton" style="margin-top:25px">
                                                <div class="mb-3">
                                                    <a href="#" class="btn btn-primary waves-effect waves-light" onclick="createOrUpdate('{{URL::to("interiorColor/add")}}', 'createForm', 'add-modal', 'interiorColorTable','')" class="btn btn-info btn-block">
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
            </div>
            <!-- End Form Layout -->
 
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!--  <h4 class="card-title">View clients</h4>
                            <p class="card-title-desc"></p>-->
                            <div class="table-responsive">  
                                <table id="interiorColorTable" class="mytable table table-bordered table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#')}}</th>
                                            <th>{{ __('Interior Color')}}</th>
                                            <th>{{ __('Interior Color Arabic')}}</th>
                                            <th>{{ __('Added By')}}</th>
                                            <th>{{ __('Status')}}</th>
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
                        
            <div class="modal fade" id="delete_entry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Delete Interior Color</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
							{!! html()->form('POST')->attributes(['url' =>'', 'method'=>'post', 'id'=>'deleteForm', 'class'=>'myform'])->open() !!}  
                            <input type='hidden' name='_token' value='{{csrf_token()}}'> 
                            <input type='hidden' name='del_id' id="del_id" value=''> 
                            <p>Are you sure,You want to delete the Interior Color?</p>
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
    var url_DivisionDataTable = '{{URL::to("interiorColor/get-list")}}';
    var url_editdivision      = '{{URL::to("interiorColor/getDivisions")}}';
    var url_deletedivision    = '{{URL::to("interiorColor/deleteDivision")}}';
    var url_status            = '{{URL::to("interiorColor/status")}}';
</script>
<script src="{{asset('module.js/main.js')}}"></script>
<script src="{{asset('module.js/Manage/interiorColor.js?ver=2.7')}}"></script>

@endsection     
