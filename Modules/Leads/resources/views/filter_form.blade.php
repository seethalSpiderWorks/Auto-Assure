<!-- --------------------------------------- apply through mge ---------------------------------->	
 
@section('script') 
<script type="text/javascript">
   var url_setfilter_staff = '{{URL::to("leads/setFilterStaff")}}';
   var url_setfilter_status = "{{url('/leads/setFilterStatus')}}";
   var url_setfilter_intakemonth = "{{url('/leads/setFilterintakemonth')}}";
   var url_setfilter_intakeyear = "{{url('/leads/setFilterintakeyear')}}";
</script>  

<script type="text/javascript">  
  $(document).ready(function(){
  	 	  $("#filter_div1").hide();
		  $("#filter_div2").hide();

     var a="<?php echo session('filter_area');?>";
     if(a !='')
     {
         $("#filter_areas").trigger('change');
     }
     var b="<?php echo session('filter_course_cat');?>";
     if(b !='')
     {
	 $("#filter_course_cat").trigger('change');
     }
     
    var c="<?php echo session('filter_sub_course_cat');?>";
    if(c !='')
    {
	 $("#filter_interested_course").trigger('change');
    }
  
    var d="<?php echo session('filter_institute');?>";
    if(d !='')
    {
	 $("#filter_institute").trigger('change');
    }
  });
 
    function set_filter_staff(staff)
    {
        //var id = $(ele).val();
        $.ajax({
                   type: 'GET',
                   url: url_setfilter_staff,
            	   dataType:'JSON',
            	   data:{'staff':staff},
                   success: function (res) {
				    $('#lead_table').DataTable().ajax.reload();
				   }
			});
    }
  
    function setFilterstatus(status)
    {
        //var id = $(ele).val();
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
  
    function setFilterintakemonth(intakemonth)
    {
       $.ajax({
                   type: 'GET',
                   url: url_setfilter_intakemonth,
                   dataType:'JSON',
                   data:{'lead_folow_intakemonth':intakemonth},
                   success: function (res) {
                    $('#lead_table').DataTable().ajax.reload();
                   }
                   });
    }

    function setFilterintakeyear(lead_folow_intakeyear)
    {
       $.ajax({
                   type: 'GET',
                   url: url_setfilter_intakeyear,
                   dataType:'JSON',
                   data:{'lead_folow_intakeyear':lead_folow_intakeyear},
                   success: function (res) {
                    $('#lead_table').DataTable().ajax.reload();
                   }
                   });
    }
</script>

@endsection