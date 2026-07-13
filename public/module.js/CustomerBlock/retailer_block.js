 /* Datatable */
 
var token = $('meta[name="csrf-token"]').attr('content');
$('#customerBlockDataTable').DataTable({
    "responsive": true,
    "serverSide": true,
    "ordering": false,
    "searching": true,
    "bLengthChange": false,
    "info": false,
    "order": [ 
        [0, 'desc']
    ],
    "columnDefs": [
        {
            "targets": [0, 3],
            "orderable": false
        },
        {
            "className": "text-center","targets": [0,1,2,3],
        }, 
        {
            "className": "text-right","targets": [ ] ,
        }, 
        {
            "className": "text-left","targets": [ ]
        }
       
    ],
    "displayLength": 15,
    "ajax": {
        "url": url_customerBlockDataTable ,
        "type": "post",
        "data": function(data) {
            data._token = token;
            return data;
        }
    },
    "AutoWidth": false,
    "columns": [
        { "data": "retailer_id",       "name": "retailer_id"        },
        { "data": "retailer_name",     "name": "retailer_name"      },
        { "data": "retailer_mob",      "name": "retailer_mob"       },
        { "data": "retailer_email",    "name": "retailer_email"     },
        { "data": "retailer_address",  "name": "retailer_address"   },
        { "data": "retailer_address",  "name": "retailer_address"   },
    ],
    "fnCreatedRow": function(nRow, aData, iDataIndex) { 
        console.log(aData);
        var info   = this.dataTable().api().page.info();
        var page   = info.page;
        var length = info.length;
        var index  = (page * length + (iDataIndex + 1));
        if( parseInt(aData.retailer_blocked) ) {
            var action = `<a href="javascript:unblock(${aData.retailer_id})" class="btn btn-success">UNBLOCK</a>`;
        } else {
            var action = `<a href="javascript:block(${aData.retailer_id})" class="btn btn-warning" >BLOCK</a>`;
        }
        $('td:eq(0)', nRow).html(index).addClass('center');
        $('td:eq(5)', nRow).html(action).addClass('center');
 
    }
});

function block(custId)
{
    $.ajax({
        url  : url_prefix_all + "/user_block/block_retailer" ,
        type : "post" ,
        data : { 
            '_token' : token ,
            'customer_id' : custId ,
            'status' : '1'
        },
        success:function(data) {
            $.toast({
                heading: "Success",
                text: data.msg,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 1500,
                stack: 6
            });
            $('#customerBlockDataTable').DataTable().ajax.reload(); 
        },
         error : function(data) {
            $('#customerBlockDataTable').DataTable().ajax.reload(); 
            $.toast({
                heading: "Error",
                text: data.msg,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'error',
                hideAfter: 1500,
                stack: 6
            }); 
        }
    });
}

function unblock(custId) 
{
    $.ajax({
        url  : url_prefix_all + "/user_block/block_retailer" ,
        type : "post" ,
        data : { 
            '_token' : token ,
            'customer_id' : custId ,
            'status' : '0'
        },
        success:function(data) {
            $('#customerBlockDataTable').DataTable().ajax.reload(); 
            $.toast({
                heading: "Success",
                text: data.msg,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 1500,
                stack: 6
            });
        },
        error : function(data) {
            $('#customerBlockDataTable').DataTable().ajax.reload(); 
            $.toast({
                heading: "Error",
                text: data.msg,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'error',
                hideAfter: 1500,
                stack: 6
            }); 
        }
    });
}