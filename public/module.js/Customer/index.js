var token = $('meta[name="csrf-token"]').attr('content');


  $('#divisionDataTable').DataTable({
        "responsive": true,
        "serverSide": true,
        "ordering": false,
        "searching": true,
        "bLengthChange": false,
        "info":     false,
        "order": [
            [0, 'desc']
        ],
        "columnDefs": [{
                "targets": [0,7],
                "orderable": false
            },
            {"className": "text-center", "targets": [0,7]}
        ],
        "displayLength":50,
        "ajax": {
            "url": url_DivisionDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
            {"data": "customer_id", "name": "customer_id"},
             {"data": "customer_unqid", "name": "customer_unqid"},
            {"data": "customer_name", "name": "customer_name"},
            // {"data": "customer_shortcode", "name": "customer_shortcode"},
            // {"data": "customer_person", "name": "customer_person"},
           // {"data": "customer_design", "name": "customer_design"},
            {"data": "customer_mob", "name": "customer_mob"},
            // {"data": "customer_land", "name": "customer_land"},
            {"data": "customer_email_id", "name": "customer_email_id"},
            // {"data": "customer_web", "name": "customer_web"},
          
           
            {"data": "state_name", "name": "state_name"},
             {"data": "city", "name": "city"},
            // {"data": "name", "name": "name"},
            
            
           {"data": "customer_id", "name": "customer_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
             var viewFunction = "javascript:viewEnquery('" + aData.customer_id + "')" ;
            var deleteFunction = "javascript:divisionDelete('" + aData.customer_id + "')" ;
            var url_divisionEdit = "javascript:editCurrency('" + aData.customer_id + "')"
            var action = '';
            
             action1 = '<img style="max-width:100px;height:100px;" class="img-circle" src="'+public_path+'/public/uploads/customer_image/'+aData.customer_image+'">';
            action = '<a href='+ viewFunction +' title="View"><i class="fa fa-eye " style="color:green"></i></a>';
          //  action += '&nbsp <a href='+ url_divisionEdit +' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
             action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            $('td:eq(7)', nRow).html(action).addClass('center');
            
            $('td:eq(1)', nRow).addClass('center');
            $('td:eq(3)', nRow).addClass('center');
            $('td:eq(4)', nRow).addClass('center');
           // $('td:eq(5)', nRow).html(action1).addClass('center');
  }
    
});

/*
 * @function editRedirect
 * editRedirect
 * 
 * @param 
 * 
 * 
 * @return 
 * string
 * 
 */
function divisionDelete(customer_id) {

  $('#delete_entry').modal('show');
  $('#customer_id').val(customer_id);
    
  }
  function deleteEntry(){
    var customer_id = $('#customer_id').val();
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'customer_id': customer_id},
        url: url_deletedivision,
        success: function (result) {
            if (result.status == 1) {
                 $.toast({
                    heading: result.heading,
                    text: result.msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 1500,
                    stack: 6
                });
         $('#delete_entry').modal('hide');
    $('#divisionDataTable').DataTable().ajax.reload();
           } else {

            }
        }
    })
  }
  function editCurrency(customer_id){
    
  $.ajax({
       type: 'POST',
        data: {'_token':token, 'customer_id': customer_id},
        url: url_editdivision,
        success: function (result) {
          $('.editButton').show();
          $('.saveButton').hide();
          $('.cancelButton').hide();
          $('#customer_id').val(result.data.customer_id);
            $('#customer_cardno').val(result.data.customer_cardno);
          $('.customer_name').val(result.data.customer_name);
          $('#customer_shortcode').val(result.data.customer_shortcode);
            $('#customer_person').val(result.data.customer_person);
             // $('#customer_design').val(result.data.customer_design);
                $('#customer_mob').val(result.data.customer_mob);
                  $('#customer_land').val(result.data.customer_land);
                    $('#customer_email').val(result.data.customer_email);
           $('#customer_web').val(result.data.customer_web);
            $('#customer_address').val(result.data.customer_address);
              $('#customer_state').val(result.data.customer_state);
          $('#customer_state').trigger('change');
          $('#customer_district').attr('data-customer_district',result.data.customer_district);
             
                 $('#customer_pin').val(result.data.customer_pin);
                  $('#customer_gstin').val(result.data.customer_gstin);
                   $('#customer_pan').val(result.data.customer_pan);
                    $('#customer_cin').val(result.data.customer_cin);
                     $('#customer_tds').val(result.data.customer_tds);
                 $('#customer_latitude').val(result.data.customer_latitude);
                  $('#customer_longitude').val(result.data.customer_longitude);
                   $('#customer_image').val(result.data.customer_image);
         
             }
    });
  }

  function viewEnquery(customer_id)
{
      $.ajax({
       type: 'POST',
        data: {'_token':token, 'customer_id': customer_id},
        url: url_viewenquery,
        success: function (result) {
             customer_child_array=result.data.fetch_customer_childrens;
             console.log(result);
             
             
           $('#view_entry').modal('show');
           $('#cname1').html(result.data.enquery.customer_name);
           $('#ccardno1').html(result.data.enquery.customer_cardno);
           $('#ccardno2').html(result.data.enquery.customer_cardno);
           $('#customer_unqid1').html(result.data.enquery.customer_unqid);
           $('#customer_name1').html(result.data.enquery.customer_name);
           $('#customer_shortcode1').html(result.data.enquery.customer_shortcode);
           $('#customer_person1').html(result.data.enquery.customer_person);
           $('#customer_mob1').html(result.data.enquery.customer_mob);
           $('#customer_land1').html(result.data.enquery.customer_land);
           $('#customer_email1').html(result.data.enquery.customer_email_id);
           $('#customer_web1').html(result.data.enquery.customer_web);
           $('#customer_address1').html(result.data.enquery.customer_address);
           $('#customer_state1').html(result.data.enquery.state_name);
           $('#customer_district1').html(result.data.enquery.city);
           $('#customer_pin1').html(result.data.enquery.customer_pincode);
           $('#customer_gstin1').html(result.data.enquery.customer_gstin);
           $('#customer_pan1').html(result.data.enquery.customer_pan);
           $('#customer_cin1').html(result.data.enquery.customer_cin);
        
           
           if (result.data.enquery.customer_tds == 1) {
               $('#customer_tds1').html("Available");
              }
            else{
                $('#customer_tds1').html("Not available");
              }
            $('#customer_latitude1').html(result.data.enquery.customer_latitude);
            $('#customer_longitude1').html(result.data.enquery.customer_longitude);
            $('#customer_image1').html('<img src="'+public_path+'/public/uploads/customer_image/'+result.data.enquery.customer_image+'" style="max-width:100px;height:100px;">'); 
    //   if(result.data.fetch_customer_childrens.length>0){
    //       alert("tests");
    //   } 

             }
    });
}

  function clearForm(){
    $('#createForm').parsley().reset();
     $('#createForm')[0].reset();
    
}

$('#branch_name_data').change(function(){
    // console.log('branchCHANGING'+ $('#branch_name_data').val())
    $('#branch_id1').val( $('#branch_name_data').val() );
    
   
});
$(document).ready(function(e){
  var branchId = $('#branch_name_data').val();
  $("#branch_id1").val(branchId);
});


