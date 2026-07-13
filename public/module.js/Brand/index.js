var token = $('meta[name="csrf-token"]').attr('content');

	$('#branchDataTable').DataTable({
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
                "targets": [0, 6],
                "orderable": false
            },
            {"className": "text-center", "targets": [0,6]}
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
					{"data": "brand_id", "name": "brand_id"},
					{"data": "created_at", "name": "created_at"},
					{"data": "created_at", "name": "created_at"},
					{"data": "brand_name_english", "name": "brand_name_english"},
					//{"data": "category_name_arabic", "name": "category_name_arabic"},
					{"data": "brand_image", "name": "brand_image"},
					{"data": "name", "name": "name"},
					{"data": "brand_id", "name": "brand_id"}
        ], "fnCreatedRow": function (nRow, aData, iDataIndex) 
		{
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('center');

			var somedate = new Date(aData.created_at);
			var date = dateFormat(new Date(new Date(somedate)), 'dd-mm-yyyy');
			//alert(date);
			var sometime = new Date(aData.created_at);
			var time = dateFormat(new Date(new Date(sometime)), 'HH:MM:ss');
			//alert(time);
			var viewFunction = "javascript:viewEnquery('" + aData.brand_id + "')" ;
			var deleteFunction = "javascript:branchDelete('" + aData.brand_id + "')" ;
			var url_branchEdit = "javascript:editCurrency('" + aData.brand_id + "')"
			var action = '';
			var action1 = '';
			
			if(aData.brand_image != '')
			{
				action1 = '<a href='+viewFunction+' title="Logo"><img style="width:120px;height:125px;" class="img-circle" src="'+public_path+'/public/uploads/brand_logos/'+aData.brand_image+'"></a>';
			}
			else
			{
				action1='';
			}
			action += '<a href='+ url_branchEdit +' title="Edit"><i class="far fa-edit" style="color:green"></i></a>';
			action += '&nbsp <a href='+deleteFunction+' title="Delete" ><i class="fa fa-trash " style="color:red"></i></a>';
			$('td:eq(6)', nRow).html(action).addClass('center');
			$('td:eq(4)', nRow).html(action1).addClass('center');
			$('td:eq(1)', nRow).html(date).addClass('center');
			$('td:eq(2)', nRow).html(time).addClass('center');
		}   
	});


function branchDelete(brand_id) 
{
	$('#delete_entry').modal('show');
	$('#brand_id').val(brand_id);  
}

function deleteEntry()
{
    var brand_id = $('#brand_id').val();
    $.ajax({
        type: 'POST',
        data: {'_token':token, 'brand_id': brand_id},
        url: url_deletedivision,
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
					}
				$('#delete_entry').modal('hide');
				$('#branchDataTable').DataTable().ajax.reload();
			} 
			else {
            }
        }
    })
}

function editCurrency(brand_id)
{    
	$.ajax({
       type: 'POST',
        data: {'_token':token, 'brand_id': brand_id},
        url: url_editdivision,
        success: function (result) 
		{
			$('.editButton').show();
			$('.saveButton').hide();
			$('.cancelButton').hide();
			$('#brand_id').val(result.data.brand_id)
			$('#brand_name_english').val(result.data.brand_name_english);
			$('#category_name_arabic').val(result.data.category_name_arabic);
			$('#brand_image').val(result.data.brand_image);         
        }
    });
}

function clearForm()
{
    $('#createForm').parsley().reset();
    $('#createForm')[0].reset();   
}

function viewEnquery(brand_id)
{
    $.ajax({
       type: 'POST',
        data: {'_token':token, 'brand_id': brand_id},
        url: url_viewenquery,
        success: function (result) 
		{
			$('#view_entry').modal('show');       
       
			$('#b_name2').html(result.data.brand_name_english);
            $('#c_logo1').html('<img src="'+public_path+'/public/uploads/brand_logos/'+result.data.brand_image+'" style="max-width:100%">'); 
        
			// $('#c_logo1').html('<img src="'+public_path+'/public/uploads/company-logo/'+result.data.company_logo+'" style="max-width:100%">');      
        }
    });
}


var dateFormat = function () {
var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
timezoneClip = /[^-+\dA-Z]/g,
pad = function (val, len) {
val = String(val);
len = len || 2;
while (val.length < len) val = "0" + val;
return val;
};

// Regexes and supporting functions are cached through closure
return function (date, mask, utc) {
var dF = dateFormat;

// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
mask = date;
date = undefined;
}

// Passing date through Date applies Date.parse, if necessary
date = date ? new Date(date) : new Date;
if (isNaN(date)) throw SyntaxError("invalid date");

mask = String(dF.masks[mask] || mask || dF.masks["default"]);

// Allow setting the utc argument via the mask
if (mask.slice(0, 4) == "UTC:") {
mask = mask.slice(4);
utc = true;
}

var _ = utc ? "getUTC" : "get",
d = date[_ + "Date"](),
D = date[_ + "Day"](),
m = date[_ + "Month"](),
y = date[_ + "FullYear"](),
H = date[_ + "Hours"](),
M = date[_ + "Minutes"](),
s = date[_ + "Seconds"](),
L = date[_ + "Milliseconds"](),
o = utc ? 0 : date.getTimezoneOffset(),
flags = {
d:    d,
dd:   pad(d),
ddd:  dF.i18n.dayNames[D],
dddd: dF.i18n.dayNames[D + 7],
m:    m + 1,
mm:   pad(m + 1),
mmm:  dF.i18n.monthNames[m],
mmmm: dF.i18n.monthNames[m + 12],
yy:   String(y).slice(2),
yyyy: y,
h:    H % 12 || 12,
hh:   pad(H % 12 || 12),
H:    H,
HH:   pad(H),
M:    M,
MM:   pad(M),
s:    s,
ss:   pad(s),
l:    pad(L, 3),
L:    pad(L > 99 ? Math.round(L / 10) : L),
t:    H < 12 ? "a"  : "p",
tt:   H < 12 ? "am" : "pm",
T:    H < 12 ? "A"  : "P",
TT:   H < 12 ? "AM" : "PM",
Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
};

return mask.replace(token, function ($0) {
return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
});
};
}();

// Some common format strings
dateFormat.masks = {
"default":      "ddd mmm dd yyyy HH:MM:ss",
shortDate:      "m/d/yy",
mediumDate:     "mmm d, yyyy",
longDate:       "mmmm d, yyyy",
fullDate:       "dddd, mmmm d, yyyy",
shortTime:      "h:MM TT",
mediumTime:     "h:MM:ss TT",
longTime:       "h:MM:ss TT Z",
isoDate:        "yyyy-mm-dd",
isoTime:        "HH:MM:ss",
isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
dayNames: [
"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
],
monthNames: [
"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
return dateFormat(this, mask, utc);
}; 
