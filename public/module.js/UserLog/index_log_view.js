var token = $('meta[name="csrf-token"]').attr('content');

  $('#branchDataTable1').DataTable({
        "responsive": true,
        "serverSide": true,
        "ordering": true,
        "searching": true,
        "bLengthChange": true,
        "info":     false,
        "order": [
            [1, 'desc']
        ],
        "columnDefs": [{
                "targets": [0, 5]
            },
            {"className": "text-center"}
        ],
        "displayLength":25,
        "ajax": {
            "url": url_userDataTable,
            "type": "post",
            "data": function (data) {
                 data._token = token;
				 data.from_date = $('#from_date').val();
                 data.to_date = $('#to_date').val();
                   //data.fk_member_id = $('#fk_member_id').val(),
                   // data.mobile_number = $('#mobile_number').val();
                 
                return data;

            }
        },
	  "AutoWidth": false,
      "columns": [
            		{"data": "id", "name": "id"},
            		{"data": "created_at", "name": "created_at"},
            		{"data": "created_at", "name": "created_at"},
             		{"data": "activity_ip", "name": "activity_ip"},
           			{"data": "name", "name": "name"},
			        {"data": "activity_desc", "name": "activity_desc"}
		  
        		], 
	  "fnCreatedRow": function (nRow, aData, iDataIndex) 
	  {
            var info = this.dataTable().api().page.info();
            var page = info.page;
            var length = info.length;
            var index = (page * length + (iDataIndex + 1));
            $('td:eq(0)', nRow).html(index).addClass('text-center');
            var somedate = new Date(aData.created_at);
            var date = dateFormat(new Date(new Date(somedate)), 'dd-mm-yyyy');
            //alert(date);
            var sometime = new Date(aData.created_at);
            var time = dateFormat(new Date(new Date(sometime)), 'HH:MM:ss');
            //alert(time);
              $('td:eq(1)', nRow).html(date).addClass('text-center');
              $('td:eq(2)', nRow).html(time).addClass('text-center');
              $('td:eq(3)', nRow).addClass('center');
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
shortDate:      "m-d-yy",
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
