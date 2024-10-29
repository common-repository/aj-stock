jQuery(document).ready(function($){
	var pre_data = [];
    var chart_array = [];
    var chart_line_data = [];
    var chart_area_data = [];
    var chart_type = '1m';
    var Aoptions = {
        legend:'none',
        chartArea: { 'width' : '100%', 'top' : 0, 'left' : 0 },
        bar: { groupWidth: '90%' },
        candlestick: {
            fallingColor: { strokeWidth: 0, fill: chartinfo.colordown },
            risingColor: { strokeWidth: 0, fill: chartinfo.colorup },
        },
        vAxis: { textPosition: "in" },
        hAxis: { textPosition: "none", count: 4 }
    };
    var Boptions = {
        legend:'none',
        chartArea: {'width' : '100%', 'top' : 0, 'left' : 0 },
        vAxis: { textPosition: "in" },
        hAxis: { textPosition: "none", count: 4 }
    };
    var Coptions = {
        legend:'none',
        chartArea: {'width' : '100%' },
        vAxis: { textPosition: "none" },
        hAxis: { textPosition: "out", count: 2, ticks: [0, 5, 10, 15, 25] }
    };
    if(chartinfo.usevolume == "Y"){
        Aoptions.hAxis = { textPosition: "none", count: 4 };
        Aoptions.chartArea = {'width' : '100%', 'height' : '100%' };
        Boptions.hAxis = { textPosition: "none", count: 4 };
    }else{
        Aoptions.hAxis = { textPosition: "out", gridlines: { count: 3 }, slantedText: false };
        Boptions.hAxis = { textPosition: "out", gridlines: { count: 3 }, slantedText: false };
    }
    if(chartinfo.iswidget == "Y"){
        Aoptions.hAxis = { textPosition: "none", count: 4 };
        Boptions.hAxis = { textPosition: "none", count: 4 };
    }
    
	//console.log(Aoptions);
	//console.log(Boptions);
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(gotimer);

    if(chartinfo.showopt == "Y"){
        $.each($(".ajsi_chart_opt"), function(k, v){ $(v).show(); });
    }else{
        $.each($(".ajsi_chart_opt"), function(k, v){ $(v).hide(); });
    }
    $(".aj_btn_showvolchart").click(function(e){
        e.preventDefault();
        var rndkey = $(this).attr("chartkey");
        var showtopchart;
        if($(this).hasClass("btn-primary")){
            $(this).removeClass("btn-primary");
            $(this).addClass("btn-default");
            $(this).text("거래량 Off");
            $(".ajsi_cs_vol_chart_"+rndkey).hide();
            showTophAxis(rndkey, "out");
        }else{
            $(this).removeClass("btn-default");
            $(this).addClass("btn-primary");
            $(this).text("거래량 On");
            $(".ajsi_cs_vol_chart_"+rndkey).show();
            showTophAxis(rndkey, "none");
        }
    })
    function showTophAxis(rndkey, show){
        $.each(chart_array, function(k, v){
            if(v.chartkey == rndkey){
                if(chart_type == "1m"){
                    Boptions.hAxis = { textPosition: show };
                    v.chartA.draw(chart_line_data, Boptions);
                }
                if(chart_type == "5m" || chart_type == "1d"){
                    //console.log(v.chartC);
                    Aoptions.hAxis = { textPosition: show };
                    v.chartC.draw(chart_area_data, Aoptions);
                }
            }
        })
    }
    function ajsi_chart_update(){
        $.ajax({
            url : chartinfo.ajax_url,
            type : 'get',
            data : {
                action : 'ajsi_get_chart_data',
				nonce : chartinfo.nonce,
                code : chartinfo.code,
                iswidget : chartinfo.iswidget,
                rnd : chartinfo.rndkey,
                interval : chartinfo.interval,
                usevolume : chartinfo.usevolume,
            },
            success : function(response) {
		pre_data = response;
                drawChart(JSON.parse(response));
                if(chartinfo.usevolume == "Y")
                    drawVolChart(JSON.parse(response));
            }
        });
    }
    function drawVolChart(res){
        var cdata = new google.visualization.DataTable();
        cdata.addColumn('string', '날짜');
        cdata.addColumn('number', '거래량');
        var arr = new Array();
        //$.each(res.chartvoldata, function (k, v){
        //    arr.push([new Date(Date.parse(v[0])), v[1]]);
        //});
        //console.log(res.chartvoldata);
        cdata.addRows(res.chartvoldata);
        var Coptions = {
            legend:'none',
            chartArea: {'width' : '100%', 'height' : '80%', 'top' : "-10" },
            vAxis: { textPosition: "in", gridlines: { count: 2 } },
            //hAxis: { textPosition: "out", count: 2, ticks: res.ticks }
            hAxis: { textPosition: "out", gridlines: { count: 3 }, slantedText: false }
        };
        //var cdata = google.visualization.arrayToDataTable();
        $.each(chart_array, function (k, v){
            v.chartB.draw(cdata, Coptions);
        });
    }
    function drawChart(res){
        chart_type = res.interval;
        if(res.interval == "1m"){
            var cdata = new google.visualization.DataTable();
            cdata.addColumn('string', '날짜');
            cdata.addColumn('number', '종가');
            var array = new Array();
            $.each(res.chartdata, function (k, v){
                array.push([v[0], v[1]]);
            });
            cdata.addRows(array);
            $.each(chart_array, function (k, v){
                v.chartA.draw(cdata, Boptions);
            });
        }else if(res.interval == "1d"){
            chart_area_data = google.visualization.arrayToDataTable(res.chartdata, true);
            $.each(chart_array, function (k, v){
                v.chartC.draw(chart_area_data, Aoptions);
            });
        }
    }
    function gotimer(){
        if(chartinfo.usevolume == "Y"){
            $.each($(".aj_btn_showvolchart"), function(k, v){ $(v).show(); $(v).text("거개량 On"); $(v).addClass("btn-default"); $(v).addClass("btn-primary"); });
        }else{
            $.each($(".aj_btn_showvolchart"), function(k, v){ $(v).hide(); $(v).text("거개량 Off");  $(v).addClass("btn-primary"); $(v).addClass("btn-default"); });
        }
        $.each($(".ajsi_cs_chart"), function(k, v){
            var chart = { 
                "chartkey" : $(v).attr("chartkey"), 
                "chartA" : new google.visualization.AreaChart(v),
                "chartC" : new google.visualization.CandlestickChart(v),
                "chartB" : {}
            };
            if(chartinfo.usevolume == "Y"){
                chart.chartB = new google.visualization.ColumnChart($(".ajsi_cs_vol_chart_"+$(v).attr("chartkey"))[0]);
            }
            chart_array.push(chart);
        });
        ajsi_chart_update();
        if(chartinfo.refresh_interval != "NO")
            setInterval (ajsi_chart_update, chartinfo.refresh_interval);
    }
$(window).resize(function() {
    if(this.resizeTO) clearTimeout(this.resizeTO);
    this.resizeTO = setTimeout(function() {
        $(this).trigger('resizeEnd');
    }, 500);
});

//redraw graph when window resize is completed  
$(window).on('resizeEnd', function() {
	drawChart(JSON.parse(pre_data));
	if(chartinfo.usevolume == "Y")
	    drawVolChart(JSON.parse(pre_data));
});
});
