var token = $('meta[name="csrf-token"]').attr('content');


  $('#userDataTable').DataTable({
        "responsive": true,
        "serverSide": true,
        "ordering": true,
        "order": [
            [0, 'desc']
        ],
        "columnDefs": [{
                "targets": [0, 6],
                "orderable": false
            },
            {"className": "text-center", "targets": [0, 6]}
        ],
        "displayLength":100,
        "ajax": {
            "url": url_usersDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
                 
                return data;

            }
        }, "AutoWidth": false,
        "columns": [
            {"data": "id", "name": "id"},
            {"data": "name", "name": "name"},
            {"data": "previlage", "name": "previlage"},
            {"data": "username", "name": "username"},
            {"data": "user_email", "name": "user_email"},
            {"data": "mobile", "name": "mobile"},
            {"data": "id", "name": "id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');
           
            var action = '';
            action = '<a href='+url_usersDataTable+'?id=' + aData.id + ' title="Edit"><i class="fa fa-pencil " style="color:green"></i></a>';
             action += '&nbsp <a href='+url_usersDataTable+'?id=' + aData.id + ' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
            $('td:eq(6)', nRow).html(action).addClass('center');
  }
    
});