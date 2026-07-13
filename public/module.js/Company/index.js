$('#companyDatatable').DataTable(
  {
  "responsive": true,
  "serverSide": true,
  //"ordering": true,
  "searching": true,
  "bLengthChange": true,
  //"bSortable": true,
  "info":     false,
  "order": [[0, 'asc']],
  "columnDefs": [
                {
                "className": "text-center", 
                 "targets": 0,
				 "orderable": true 
                }
                ],
  "displayLength":25,
  "ajax": 
        {
		"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        "url": url_companyDataTable,
        "type": "post",
        "data": function (data) 
          {
			var token = '';
            data._token = token;
            return data;
          }
        },  
  "autoWidth": false,
  "columns": 
            [
				{"data": "company_id", "name": "company_id"},
				{"data": "company_unq_id", "name": "company_unq_id"},
				{"data": "company_name", "name": "company_name"},
				{"data": "company_code", "name": "company_code"},
				{"data": "company_mob", "name": "company_mob"},
				{"data": "company_email", "name": "company_email"},
				//{"data": "company_logo", "name": "company_logo"},
				{"data": "name", "name": "name"},
				{"data": "company_id", "name": "company_id"}
            ], 
  "fnCreatedRow": function (nRow, aData, iDataIndex) 
    {
    //var info   = this.dataTable().api().page.info();
	var info = this.api().page.info();
    var page   = info.page;
    var length = info.length;
    
    var response = $('#companyDatatable').DataTable().ajax.json();
    var option =response.option.opset_options;
    //alert(option);
    var index  = (page * length + (iDataIndex + 1));
    $('td:eq(0)', nRow).html(index).addClass('center');
	$('td:eq(1)', nRow).html(aData.company_unq_id).addClass('text-center');
    
    if(aData.company_logo != '')
    {
        var image = '<a href="'+public_path+aData.company_logo+'" targets="_blank" ><img style="width:150px" src="'+public_path+aData.company_logo+'"></a>';
    }
    else
    {
        var image = '';
    }
    
    $('td:eq(6)', nRow).addClass('text-center');
    var deleteFunction = "javascript:delete_function('" + aData.company_id + "')" ;
    var editFunction = "javascript:edit('" + aData.company_id + "')";
    var viewFunction = "javascript:view('" + aData.company_id + "')";
    var action = '';
		
    if(option.indexOf("1") !== -1 ){
    	action += '<a href='+ viewFunction +' title="View"><i class="far fa-eye" style="color:blue"></i></a>';
    }
    if(option.indexOf("2") !== -1){
    	action += '&nbsp <a href="#" onclick='+ editFunction +' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
    }
    if(option.indexOf("3") !== -1){
    	action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="far fa-trash-alt" style="color:red"></i></a>';
    }
    $('td:eq(7)', nRow).html(action).addClass('center');
    }
});

function view(id)
{
  $.ajax({
        type: 'GET',
        data: {'id': id},
        url: url_viewcompany,
        success: function (result) 
          {
          $('#view-modal-body').html(result);
          $('#view_modal').modal('show');
          }
    });
}
  
function edit(id)
{
	  $('#parsley-id-5').html(" ");
	  $('#parsley-id-7').html(" ");
	  var token = '';
  $.ajax({
		  headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          type: 'POST',
          data: {'_token':token, 'id': id},
          url: url_editcompany,
          success: function (result) 
            {
            $('.editButton').show();
            $('.saveButton').hide();
            $('#edit_id').val(result.company_id);
            $('#company_code').val(result.company_code);
            $('#company_name').val(result.company_name);
            $('#company_person').val(result.company_person);
            $('#company_mob').val(result.company_mob);
            $('#company_lan').val(result.company_lan);
            $('#company_email').val(result.company_email);
            $('#company_web').val(result.company_web);
            $('#company_address').val(result.company_address);
            //$('#company_country').val(result.company_country);
            //$('#company_state').attr('data-state',result.company_state);
            //$('#company_city').attr('data-city',result.company_city);
            //$('#company_country').trigger('change');
            //$('#company_lat').val(result.company_lat);
            //$('#company_long').val(result.company_long);
            }
    });
  }
function delete_function(id) 
  {
  $('#delete_entry').modal('show');
  $('#del_id').val(id);
  }
  
function deleteEntry()
 {
	var token = '';
	var id = $('#del_id').val();
	$.ajax({
		headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: {'_token':token, 'id': id},
        url: url_deletecompany,
        success: function (result) 
          {
          if (result.status == 1) 
            {
				Command: toastr["success"](result.msg)
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
						};

            $('#delete_entry').modal('hide');
            $('#companyDatatable').DataTable().ajax.reload();
            document.getElementById("createForm").reset();
            } 
            else 
            {}
          }
      });
  }
function clearForm()
  {
  $('#createForm').parsley().reset();
  $('#createForm')[0].reset();
  }

$('body').on('change', '#company_logo', function (e) {
        $("#image1 span").text("");

        var fileUpload = $("#company_logo")[0];
        //Check whether the file is valid Image.
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:()])+(.jpg|.png)$");
        if (regex.test(fileUpload.value.toLowerCase())) {
            //Check whether HTML5 is supported.
            if (typeof (fileUpload.files) != "undefined") {
                //Initiate the FileReader object.
                var reader = new FileReader();
                //Read the contents of Image File.  
                
                reader.readAsDataURL(fileUpload.files[0]);
                reader.onload = function (e) {
                    //Initiate the JavaScript Image object.
                    var image = new Image();
                    //Set the Base64 string return from FileReader as source.
                    image.src = e.target.result;
                    image.onload = function () {
                        //Determine the Height and Width.
                        var height = this.height;
                        var width = this.width;                        
                        if (height != 99 || width != 415)
                         {
                             $("#image1 span").text("Size Not Matching");
                             $("#company_logo").val(null);
                         }  
                    };
                }
            } else {
              $("#image1 span").text("This browser does not support HTML5");
                $("#company_logo").val(null);
            }
        } else {
            $("#image1 span").text("Please select a valid file");
            $("#company_logo").val(null);

        }
    }); 