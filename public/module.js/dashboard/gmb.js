var lineColors = ['#FF5733', '#2E8B57', '#3366FF','#b9b41c','#b7ccf5','#928c5f','#426b6f','#fea8e8'];
var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel-1");
LinechartDatalabelColors&&(options={
	chart:{
		   height:380,type:"line",
		   zoom:{enabled:!1},
		   toolbar:{show:!1}},
	       colors:lineColors,
	       dataLabels:{enabled:!1},stroke:{width:[3,3],
		   curve:"straight"},
	       series:[call,msg,dir,click,mobile_map,desktop_maps,mobile_search,desktop_search],
	       title:{text:"Total GMB Data",align:"left"},
	      grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
	      markers:{style:"inverted",size:6},
	      xaxis:{categories:category,title:{text:"Month"}},
	    //  yaxis:{title:{text:"Number"},min:1,max:100},
		 tooltip: {
                y: [
                    { title: { formatter: function(e) { return e   } } },
                    { title: { formatter: function(e) { return e  } } },
                    { title: { formatter: function(e) { return e } } }
                ]
            },
	      legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
	      responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},
						   
			(chart=new ApexCharts(document.querySelector("#line_chart_datalabel-1"),options)).render());



var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-g1");
var value=document.getElementById('orders-chart-g1').getAttribute("data-value");
var total = document.getElementById('orders-chart-g1').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-g1"),options)).render());


var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-g2");
var value=document.getElementById('orders-chart-g2').getAttribute("data-value");
var total = document.getElementById('orders-chart-g2').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-g2"),options)).render());

var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-g3");
var value=document.getElementById('orders-chart-g3').getAttribute("data-value");
var total = document.getElementById('orders-chart-g3').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);

RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-g3"),options)).render());

var RadialchartOrdersChartColors1=getChartColorsArray("orders-chart-g4");
var value=document.getElementById('orders-chart-g2').getAttribute("data-value");
var total = document.getElementById('orders-chart-g2').getAttribute("data-total");
var avg  = 0;
if(total != 0)
	avg = parseFloat( (value/total ) *100);
RadialchartOrdersChartColors1&&(options={fill:{colors:RadialchartOrdersChartColors1},series:[avg],chart:{type:"radialBar",width:45,height:45,sparkline:{enabled:!0}},dataLabels:{enabled:!1},plotOptions:{radialBar:{hollow:{margin:0,size:"60%"},track:{margin:0},dataLabels:{show:!1}}}},(chart=new ApexCharts(document.querySelector("#orders-chart-g4"),options)).render());


function gmbDaily(category)
{
	$('#line_chart_datalabel-1').hide();
	
	$.ajax({
            type: 'GET',
            dataType:'json',
            data: { 'category':category },
            url: url_gmbDaily,
            success: function(data) 
            {
				$('#line_chart_datalabel-2').show();
				$('#chart_type').html("Daily");
				
				var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel-2");
LinechartDatalabelColors&&(options={
		chart:{
			   height:380,type:"line",
			   zoom:{enabled:!1},
			   toolbar:{show:!1}},
			   colors:lineColors,
			   dataLabels:{enabled:!1},stroke:{width:[3,3],
			   curve:"straight"},
			   series:[data.call,data.msg,data.direction,data.click,data.MobMap,data.deskMap,data.mobSearch,data.deskSearch],
			   title:{text:"Total GMB Data",align:"left"},
			  grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
			  markers:{style:"inverted",size:6},
			  xaxis:{categories:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],title:{text:"Daily"}},
			//  yaxis:{title:{text:"Number"},min:1,max:100},
			 tooltip: {
					y: [
						{ title: { formatter: function(e) { return e   } } },
						{ title: { formatter: function(e) { return e  } } },
						{ title: { formatter: function(e) { return e } } }
					]
				},
			  legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
			  responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},

				(chart=new ApexCharts(document.querySelector("#line_chart_datalabel-2"),options)).render());

				}	  
	  });
}

function gmbWeekly(category)
{
	$('#line_chart_datalabel-1').hide();
	$('#line_chart_datalabel-2').hide();
	
		$.ajax({
            type: 'GET',
            dataType:'json',
            data: { 'category':category },
            url: url_gmbWeekly,
            success: function(data) 
            {
				$('#line_chart_datalabel-3').show();
				$('#chart_type').html("Weekly");
				
				var LinechartDatalabelColors=getChartColorsArray("line_chart_datalabel-3");
LinechartDatalabelColors&&(options={
		chart:{
			   height:380,type:"line",
			   zoom:{enabled:!1},
			   toolbar:{show:!1}},
			   colors:lineColors,
			   dataLabels:{enabled:!1},stroke:{width:[3,3],
			   curve:"straight"},
			   series:[data.call,data.msg,data.direction,data.click,data.MobMap,data.deskMap,data.mobSearch,data.deskSearch],
			   title:{text:"Total GMB Data",align:"left"},
			  grid:{row:{colors:["transparent","transparent"],opacity:.2},borderColor:"#f1f1f1"},
			  markers:{style:"inverted",size:6},
			  xaxis:{categories:data.category,title:{text:"Daily"}},
			//  yaxis:{title:{text:"Number"},min:1,max:100},
			 tooltip: {
					y: [
						{ title: { formatter: function(e) { return e   } } },
						{ title: { formatter: function(e) { return e  } } },
						{ title: { formatter: function(e) { return e } } }
					]
				},
			  legend:{position:"top",horizontalAlign:"right",floating:!0,offsetY:-25,offsetX:-5},
			  responsive:[{breakpoint:600,options:{chart:{toolbar:{show:!1}},legend:{show:!1}}}]},

				(chart=new ApexCharts(document.querySelector("#line_chart_datalabel-3"),options)).render());

				
			}
		});
}

function gmbMonthly(category)
{
	$('#line_chart_datalabel-2').hide();
	$('#line_chart_datalabel-3').hide();
	
	$('#line_chart_datalabel-1').show();
	
	$('#chart_type').html("Monthly");
	
}


