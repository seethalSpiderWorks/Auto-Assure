function getChartColorsArray(e){if(null!==document.getElementById(e)){var t=document.getElementById(e).getAttribute("data-colors");if(t)return(t=JSON.parse(t)).map(function(e){var t=e.replace(" ","");if(-1===t.indexOf(",")){var r=getComputedStyle(document.documentElement).getPropertyValue(t);return r||t}var a=e.split(",");return 2!=a.length?t:"rgba("+getComputedStyle(document.documentElement).getPropertyValue(a[0])+","+a[1]+")"})}}

var lineColors2 = ['#FF5733', '#2E8B57', '#3366FF','#b9b41c','#c619db'];

var LinechartDashedColors=getChartColorsArray("line_chart_dashed-n1");
LinechartDashedColors&&(options={chart:{height:380,type:"line",zoom:{enabled:!1},toolbar:{show:!1}},colors:lineColors2,dataLabels:{enabled:!1},stroke:{width:[3,4,3],curve:"straight",dashArray:[0,8,5]},series:[{name:"Facebook",data:[45,52,38,24,33]},{name:"Twitter",data:[36,42,60,42,13]},{name:"GMB",data:[89,56,74,98,72]},{name:"Insta",data:[12,14,16,25,42]},{name:"TooTwo",data:[25,35,12,20,32]}],title:{text:"Campaign wise Lead Statistics",align:"left"},markers:{size:0,hover:{sizeOffset:6}},xaxis:{categories:["June","July","August","September","October",]},tooltip:{y:[{title:{formatter:function(e){return e+" (mins)"}}},{title:{formatter:function(e){return e+" per session"}}},{title:{formatter:function(e){return e}}}]},grid:{borderColor:"#f1f1f1"}},(chart=new ApexCharts(document.querySelector("#line_chart_dashed-n1"),options)).render());




var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-c1");
var value=document.getElementById('orders-chart-c1').getAttribute("data-value");
var total = document.getElementById('orders-chart-c1').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-c1"),options)).render());



var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-c2");
var value=document.getElementById('orders-chart-c2').getAttribute("data-value");
var total = document.getElementById('orders-chart-c2').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-c2"),options)).render());

var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-c3");
var value=document.getElementById('orders-chart-c3').getAttribute("data-value");
var total = document.getElementById('orders-chart-c3').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-c3"),options)).render());


var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-c4");
var value=document.getElementById('orders-chart-c4').getAttribute("data-value");
var total = document.getElementById('orders-chart-c4').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-c4"),options)).render());
