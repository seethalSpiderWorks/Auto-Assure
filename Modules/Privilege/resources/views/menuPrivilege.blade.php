
@extends('layouts.myfudapp')
@section('content')
<div class="container-fluid innerdash">
<div class="page-header" >
    <div class="d-flex align-items-center">
        <h2 class="page-header-title">Menu Privilege </h2>
    </div>
</div>
<div class="row authentication-form mx-auto"> 
{!! Form::open(['url' =>'','method'=>'post','id'=>'createForm','class'=>'myform']) !!}  
{{Form::hidden('menu_privilege_id','',['id'=>'menu_privilege_id'])}}


<div class="col-md-4" > 
<div class="group material-input">       
{!! Form::select('privilege_name',[],null,['id'=>'privilege_name','class'=>'form-control  select2 privilege_name','required'=>'required','data-parsley-required-message'=>'Privilege Name required']) !!}                  
<span class="highlight"></span>
<label>Privilege Name</label>
</div>
</div>

<div class="col-md-2 editButton">
  <a href="#" onclick="menuPrivilege()" class="btn btn-success btn-block">Submit</a>
</div>

{!!Form::close() !!}
</div>
<div class="clearfix"></div>

  {!! Form::open(array('url'=>'','class'=>'form-horizontal','id'=>'updateRole')) !!} 
                 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="privillege_set" style="display: none;">
                                <input type="hidden" name="privilege_id" id="privilege_id" value="">
                               <div class="panel-group">
                              <div class="panel panel-default">
                                  <div class="panel-heading">
                                    <h4 class="panel-title">
                                      <input class="pc-box" type="checkbox" id="select-department<?php echo "0";?>" onclick="selector('<?php echo "0";?>');" >
                                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo "0";?>" class="">
                                        All Menu Items
                                      </a>
                                    </h4>

                                  </div>
                                  <div class="panel-body" id="checker<?php echo "0";?>">
                                     <div id="tree" class="set_menu_privilege">
                                       
                                     </div>
                                      <p id="permissionError" class="text-danger" style="display: none">Please check user privilege</p> 
                                  </div>
                            </div>
                       </div>
                        <div class="row">
                            <div><a href="#" id="btnupdateRole" class="btn btn-info btn-block"><i class="fa fa-save"></i> Update</a></div>                            
                        </div>

                        </div>
                       {!!Form::close() !!}

</div>

@endsection

@section('js')
<!-- This is data table -->
<script src="{{asset('public/assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<!-- start - This is for export functionality only -->
<script src="{{asset('public/assets/template/material/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('public/assets/template/material/js/vfs_fonts.js')}}"></script>
<script src="{{asset('public/assets/template/material/js/buttons.flash.min.js')}}"></script>
<script src="{{asset('public/assets/template/material/js/jszip.min.js')}}"></script>
<script src="{{asset('public/assets/template/material/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/assets/template/material/js/buttons.print.min.js')}}"></script>
<script src="{{asset('public/assets/plugins/select2/dist/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/assets/plugins/bootstrap-select/bootstrap-select.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/assets/plugins/tree/jquery-ui.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/assets/plugins/tree/jquery.tree.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
   
    var url_privilegeGet =  '{{URL::to("menuprivilege/getPrivilege")}}';
    var url_getmenuprivilege = '{{URL::to("menuprivilege/getmenuprivilege") }}' ;

    function selector(id)
{
  if(document.getElementById('select-department'+id).checked){
    $('#checker'+id+' :checkbox').each(function(){
      this.checked = true;
    });
  }
  else{
    $('#checker'+id+' :checkbox').each(function(){
      this.checked = false;
    });
  }
}


$(document).ready(function () {
    $('#tree').tree({
        dnd: false,
        onCheck: {
            ancestors: 'checkIfFull',
            descendants: 'check'
        },
        onUncheck: {
            ancestors: 'uncheck'
        }
    });
    
    $("input[type=checkbox]").on('click', function () {
        window.setTimeout(function () {
            if ($("input.permission[type=checkbox]:checked").length > 0) {
                $('#permissionError').hide();
            } else {
                $('#permissionError').show();
            }
        }, 100);
    });
    
   
    $('.daredevel-tree-label').on('click', function () {
        $(this).parent('li').children('input[type=checkbox]').trigger('click')
    });
});

    $('#btnupdateRole').on('click', function () {
    $('#updateRole').parsley().validate();
    if ($("input.permission[type=checkbox]:checked").length > 0) {
        $('#permissionError').hide();
        if ($('#updateRole').parsley().isValid()) {

            $.ajax({
                url: '{!! URL::to("menuprivilege/updateRole") !!}',
                type: "POST",
                data: $('#updateRole').serialize(),
                success: function (data) {
                    if (data.status == 1) {
                        $.toast({
                            heading: 'Success',
                            text: "Privilege Updated Successfully",
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 3500,
                            stack: 6
                        });
                     ;
                    } else if (data.status == 0) {
                        $.toast({
                            heading: 'Error',
                            text: data.msg,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3500
                        });
                    } else {
                        $.toast({
                            heading: '??',
                            text: data.msg,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3500
                        });
                    }
                }
            });
        }
    } else {
        $.toast({
            heading: 'Error',
            text: 'Please select user privilege',
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: 'error',
            hideAfter: 3500
        });
        $('#permissionError').show();

    }
});

</script>
<script src="{{asset('public/module.js/Privilege/menuindex.js')}}"></script>
<script src="{{asset('public/module.js/main.js?ver=55')}}"></script>
@endsection