function getChartColorsArray(r){if(null!==document.getElementById(r)){
	var t=document.getElementById(r).getAttribute("data-colors");
	if(t)return(t=JSON.parse(t)).map(function(r){var t=r.replace(" ","");if(-1===t.indexOf(",")){var e=getComputedStyle(document.documentElement).getPropertyValue(t);return e||t}var a=r.split(",");return 2!=a.length?t:"rgba("+getComputedStyle(document.documentElement).getPropertyValue(a[0])+","+a[1]+")"})}}var options1,chart1,BarchartTotalReveueColors=getChartColorsArray("total-revenue-chart");BarchartTotalReveueColors&&(options1={series:[{data:[25,66,41,89,63,25,44,20,36,40,54]}],fill:{colors:BarchartTotalReveueColors},chart:{type:"bar",width:70,height:40,sparkline:{enabled:!0}},plotOptions:{bar:{columnWidth:"50%"}},labels:[1,2,3,4,5,6,7,8,9,10,11],xaxis:{crosshairs:{width:1}},tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(r){return""}}},marker:{show:!1}}},(chart1=new ApexCharts(document.querySelector("#total-revenue-chart"),options1)).render());


var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-d1");
var value=document.getElementById('orders-chart-d1').getAttribute("data-value");
var total = document.getElementById('orders-chart-d1').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-d1"),options)).render());

var RadialchartOrdersChartColors2=getChartColorsArray("orders-chart-d2");
var value=document.getElementById('orders-chart-d2').getAttribute("data-value");
var total = document.getElementById('orders-chart-d1').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors2&&(options={fill:{colors:RadialchartOrdersChartColors2},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-d2"),options)).render());

var RadialchartOrdersChartColors3=getChartColorsArray("orders-chart-d3");
var value=document.getElementById('orders-chart-d3').getAttribute("data-value");
var total = document.getElementById('orders-chart-d3').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors3&&(options={fill:{colors:RadialchartOrdersChartColors3},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-d3"),options)).render());
var RadialchartOrdersChartColors4=getChartColorsArray("orders-chart-d4");
var value=document.getElementById('orders-chart-d4').getAttribute("data-value");
var total = document.getElementById('orders-chart-d3').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors4&&(options={fill:{colors:RadialchartOrdersChartColors4},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-d4"),options)).render());

var RadialchartCustomersColors=getChartColorsArray("customers-chart");RadialchartCustomersColors&&(options={fill:{colors:RadialchartCustomersColors},series:[55],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#customers-chart"),options)).render());var options2,chart2,BarchartGrowthColors=getChartColorsArray("growth-chart");BarchartGrowthColors&&(options2={series:[{data:[25,66,41,89,63,25,44,12,36,9,54]}],fill:{colors:BarchartGrowthColors},chart:{type:"bar",width:70,height:40,sparkline:{enabled:!0}},plotOptions:{bar:{columnWidth:"50%"}},labels:[1,2,3,4,5,6,7,8,9,10,11],xaxis:{crosshairs:{width:1}},tooltip:{fixed:{enabled:!1},x:{show:!1},y:{title:{formatter:function(r){return""}}},marker:{show:!1}}},(chart2=new ApexCharts(document.querySelector("#growth-chart"),options2)).render());
var options,chart,LinechartsalesColors=getChartColorsArray("sales-analytics-chart");LinechartsalesColors&&(options={chart:{height:343,type:"line",stacked:!1,toolbar:{show:!1}},stroke:{width:[0,2,4],curve:"smooth"},plotOptions:{bar:{columnWidth:"30%"}},colors:LinechartsalesColors,series:[{name:"Desktops",type:"column",data:[23,11,22,27,13,22,37,21,44,22,30]},{name:"Laptops",type:"area",data:[44,55,41,67,22,43,21,41,56,27,43]},{name:"Tablets",type:"line",data:[30,25,36,30,45,35,64,52,59,36,39]}],fill:{opacity:[.85,.25,1],gradient:{inverseColors:!1,shade:"light",type:"vertical",opacityFrom:.85,opacityTo:.55,stops:[0,100,100,100]}},labels:["01/01/2003","02/01/2003","03/01/2003","04/01/2003","05/01/2003","06/01/2003","07/01/2003","08/01/2003","09/01/2003","10/01/2003","11/01/2003"],markers:{size:0},xaxis:{type:"datetime"},yaxis:{title:{text:"Points"}},tooltip:{shared:!0,intersect:!1,y:{formatter:function(r){return void 0!==r?r.toFixed(0)+" points":r}}},grid:{borderColor:"#f1f1f1"}},(chart=new ApexCharts(document.querySelector("#sales-analytics-chart"),options)).render());


var options,chart,LinechartsalesColors=getChartColorsArray("sales-analytics-chart-1");LinechartsalesColors&&(options={chart:{height:343,type:"line",stacked:!1,toolbar:{show:!1}},stroke:{width:[0,2,4],curve:"smooth"},plotOptions:{bar:{columnWidth:"30%"}},colors:LinechartsalesColors,series:[{name:"Lead",type:"column",data:[23,11,22,27,13,22,37]},{name:"Followup",type:"area",data:[44,55,41,67,22,43,21]}],fill:{opacity:[.85,.25,1],gradient:{inverseColors:!1,shade:"light",type:"vertical",opacityFrom:.85,opacityTo:.55,stops:[0,100,100,100]}},labels:["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],markers:{size:0},xaxis:{type:"category"},yaxis:{title:{text:"count in number"}},tooltip:{shared:!0,intersect:!1,y:{formatter:function(r){return void 0!==r?r.toFixed(0)+" points":r}}},grid:{borderColor:"#f1f1f1"}},(chart=new ApexCharts(document.querySelector("#sales-analytics-chart-1"),options)).render());



var lineColors = ['#FF5733', '#2E8B57', '#3366FF','#b9b41c'];

var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel-1");
LinechartDatalabelColors&&(options={
	chart:{
		   height:380,type:"line",
		   zoom:{enabled:!1},
		   toolbar:{show:!1}},
	       colors:lineColors,
	       dataLabels:{enabled:!1},stroke:{width:[3,3],
		   curve:"straight"},
	       series:[leadData,followupData],
	       title:{text:"Total Lead Count",align:"left"},
	      grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
	      markers:{style:"inverted",size:6},
	      xaxis:{categories:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],title:{text:"Days"}},
	      yaxis:{title:{text:"Number"},min:minValue,max:maxValue},
	      legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
	      responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},
			(chart=new ApexCharts(document.querySelector("#line_chart_datalabel-1"),options)).render());


function lineChart(category)
{
	$('#line_chart_datalabel_weely').hide();
	$('#line_chart_datalabel_monthly').hide();
	
	
	      $.ajax({
            type: 'GET',
            dataType:'json',
            data: { 'category':category},
            url: url_linechart,
            success: function(data) 
            {
				$('#line_chart_datalabel-1').show();
				$('#chart_type').html("Daily");
				
				var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel-1");
				LinechartDatalabelColors&&(options={
					chart:{
						   height:380,type:"line",
						   zoom:{enabled:!1},
						   toolbar:{show:!1}},
						   colors:lineColors,
						   dataLabels:{enabled:!1},stroke:{width:[3,3],
						   curve:"straight"},
						   series:[data.leadData,data.followupData],
						   title:{text:"Total Lead Count",align:"left"},
						  grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
						  markers:{style:"inverted",size:6},
						  xaxis:{categories:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],title:{text:"Days"}},
						  yaxis:{title:{text:"Number"},min:data.minValue,max:data.maxValue},
						  legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
						  responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},
							(chart=new ApexCharts(document.querySelector("#line_chart_datalabel-1"),options)).render());
				
				
			}
		  });

}

function lineChartWeekly(category)
{
	$('#line_chart_datalabel-1').hide();
	$('#line_chart_datalabel_monthly').hide();
	
	   $.ajax({
            type: 'GET',
            dataType:'json',
            data: { 'category':category},
            url: url_lineChartWeekly,
            success: function(data) 
            {
				$('#line_chart_datalabel_weely').show();
				$('#chart_type').html("Weekly");
				
				var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel_weely");
				LinechartDatalabelColors&&(options={
					chart:{
						   height:380,type:"line",
						   zoom:{enabled:!1},
						   toolbar:{show:!1}},
						   colors:lineColors,
						   dataLabels:{enabled:!1},stroke:{width:[3,3],
						   curve:"straight"},
						   series:[data.leadData,data.followupData],
						   title:{text:"Total Lead Count",align:"left"},
						  grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
						  markers:{style:"inverted",size:6},
						  xaxis:{categories:data.category,title:{text:"Weeks in the current month"}},
						  yaxis:{title:{text:"Number"},min:data.minValue,max:data.maxValue},
						  legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
						  responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},
							(chart=new ApexCharts(document.querySelector("#line_chart_datalabel_weely"),options)).render());
				
				
			}
		  });
}

function lineChartMonthly(category)
{
	$('#line_chart_datalabel-1').hide();
	$('#line_chart_datalabel_weely').hide();
	
	 $.ajax({
            type: 'GET',
            dataType:'json',
            data: { 'category':category},
            url: url_lineChartMonthly,
            success: function(data) 
            {
				$('#line_chart_datalabel_monthly').show();
				$('#chart_type').html("Monthly");
				
				var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel_monthly");
				LinechartDatalabelColors&&(options={
					chart:{
						   height:380,type:"line",
						   zoom:{enabled:!1},
						   toolbar:{show:!1}},
						   colors:lineColors,
						   dataLabels:{enabled:!1},stroke:{width:[3,3],
						   curve:"straight"},
						   series:[data.leadData,data.followupData],
						   title:{text:"Total Lead Count",align:"left"},
						  grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
						  markers:{style:"inverted",size:6},
						  xaxis:{categories:data.category,title:{text:"Months in the current year"}},
						  yaxis:{title:{text:"Number"},min:data.minValue,max:data.maxValue},
						  legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
						  responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},
							(chart=new ApexCharts(document.querySelector("#line_chart_datalabel_monthly"),options)).render());
				
				
			}
	 });
	
}


function getCourseCount(id)
{
	var type = '';
	
		 $.ajax({
            type: 'GET',
            dataType:'html',
            data: { 'id':id},
            url: url_coureLeadCount,
            success: function(data) 
            {
				if(id == 1) 
				{
					type = 'Daily';
				}
				else if(id == 2)
				{
					type = 'Weekly';
				}
				else if(id == 3)
				{
					type = 'Monthly';
				}
				
				$('#selected_type').html(type);
				$('#course-graph').html(data);
				
			}
		 });
}