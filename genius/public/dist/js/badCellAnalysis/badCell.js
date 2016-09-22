$(document).ready(function() {
//设置日期
  setTime(); 
  getAllCity();
  //设置表格
  //setTable();
     var inputType = $('#inputCategory').val();
     //alert(inputType);
    if(inputType == 'lowAccessCell') {
      toogle('lowAccessCell');
    }else if(inputType == 'highLostCell') {
      toogle('highLostCell');
    }else if(inputType == 'badHandoverCell') {
      toogle('badHandoverCell');
    }

  
})

//-------设置日期------//
function setTime(){
  $("#startTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
  $("#endTime").datepicker({format: 'yyyy-mm-dd'});

  var nowTemp = new Date();
  $("#startTime").datepicker('setValue', nowTemp);
  $("#endTime").datepicker('setValue', nowTemp);
  //alert(nowTemp);
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $('#startTime').datepicker({
    onRender: function(date) {
      return date.valueOf() < now.valueOf() ? '' : '';
    }
  }).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.date.valueOf()) {
      var newDate = new Date(ev.date)
      newDate.setDate(newDate.getDate() + 1);
      checkout.setValue(newDate);
    }
    checkin.hide();
    $('#endTime')[0].focus();
    }).data('datepicker');
      var checkout = $('#endTime').datepicker({
      onRender: function(date) {
        //return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
        return date.valueOf() <= checkin.date.valueOf() ? '' : '';
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');
}

//----------获得城市----------//

function getAllCity(){
  $('#allCity').multiselect({
       dropRight: true,
      buttonWidth: '100%',
      //enableFiltering: true,
      nonSelectedText:'请选择城市',
      //filterPlaceholder:'搜索',
      nSelectedText:'项被选中',
      includeSelectAllOption:true,
      selectAllText:'全选/取消全选',
      allSelectedText:'已选中所有平台类型',
      maxHeight:200,
      maxWidth:'100%'
  });
  var url = "badCell/getAllCity";
  $.ajax({
    type:"GET",
    url:url,
    dataType:"json",
    success:function(data){
      var newOptions = new Array();
      var obj = new Object();
      $(data).each(function(k,v){
        var v = eval("("+v+")");
        obj = {
                label : v["text"],
                value : v["value"]
            };
        newOptions.push(obj);
      });
      $('#allCity').multiselect('dataprovider', newOptions);
    }
  });
}

function getChooseCitys(){
  var citys = $('#allCity').val();
  return citys;
}


function getParams(table,inputType){
  //var inputType = $('#worstCellType').val();
  if(inputType == '低接入小区') {
    var type = 'lowAccessCell_ex';
  }else if(inputType == '高掉线小区') {
    type = 'highLostCell_ex';
  }else if(inputType == '切换差小区') {
    type = 'badHandoverCell_ex';
  }
  var startTime   = $('#startTime').val();
  var endTime     = $('#endTime').val();
  var citys       = $('#allCity').val();


  if(citys == null){
    alert("Please choose city first!");
    return false;
  }
  var cell= $('#cellInput').val();
  //alert(moTree);
  var params = {
    startTime:startTime,
    endTime:endTime,
    city:citys,
    //city:JSON.stringify(citys),//citys,
    //subNet:JSON.stringify(subNetworks),//subNetworks,
    table:type,
    cell:cell
    //action:action 
  };
  //alert(params)
  return params;
}

      function doSearchbadCell(table,worstCellType){
        var l = Ladda.create( document.getElementById( 'search' ) );
        var E = Ladda.create( document.getElementById( 'export' ) );
        l.start();
        E.start();
        var params = getParams(table,worstCellType);
        if(params == false){
          l.stop();
          E.stop();
          return false;
        }
        
      $.get('badCell/templateQuery', params, function(data){
        var fieldArr=new Array();
        var text=(JSON.parse(data).content).split(',');
        var filename = JSON.parse(data).filename;
        $('#badCellFile').val(filename);
        for(var i in JSON.parse(data).rows[0]){  
          //console.log(JSON.parse(data).rows[0]);  
    
            fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150,sortable:true};
           
        } 
        //console.log(fieldArr);  
         //fieldArr[fieldArr.length] = "{ width: 50, tmpl: <a href='#'>edit</a>, align: 'center', events: { 'click': Edit } }";
        // alert(fieldArr);
        var newData = JSON.parse(data).rows;

        $('#badCellTable').grid('destroy', true, true);
        var badCellTable = $("#badCellTable").grid({
          columns:fieldArr,
          dataSource:newData,
          pager: { limit: 10, sizes: [10, 20, 50, 100] },
          autoScroll:true,
          uiLibrary: 'bootstrap',
        });
    
        l.stop();
        E.stop();
		 
      badCellTable.on('rowSelect', function (e, $row, id, record) {
        $(".zhaozi").show();
        //var mapv = initMap("map");
        
         //alert('Row with id=' + record.cell + ' is selected.');
         //$('#rowSelect').val(record.cell);
        if($('#chooseTable').val() == 'lowAccessCell') {
           //var table = 'FMA_alarm_log';
           var tableChart = 'lowAccessCell';
           window.mapv = initMap("map");
           getCellData(mapv,e,$row,id,record);
         }else if($('#chooseTable').val() == 'highLostCell'){
            //var table = 'FMA_alarm_log';
            var tableChart = 'highLostCell';
         }else if($('#chooseTable').val() == 'badHandoverCell'){
           //var table = 'FMA_alarm_log';
           var tableChart = 'badHandoverCell';
         }
        var params = {
              table:tableChart,
              //table:table,
              rowCell:record.cell
           };

      $.get('badCell/getalarmWorstCell', params, function(data){
          $("#alarm_zhaozi").hide();
           //alert(data);
          var fieldArr=new Array();
          var text=data.content.split(',');
          var filename = data.filename;
          //$('#alarmWorstCellTable').val(filename);
          for(var i in data.rows[0]){       
            fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
          } //console.log(fieldArr);
           var newData = data.rows;

          $('#alarmWorstCellTable').grid('destroy', true, true);
          var alarmWorstCellTable = $("#alarmWorstCellTable").grid({
            columns:fieldArr,
            dataSource:newData,
            pager: { limit: 10, sizes: [10, 20, 50, 100] },
            autoScroll:true,
            uiLibrary: 'bootstrap',
          });
       })
    //alert('123');
        var yAxis_name_left  = $('#worstCellChartPrimaryAxisType').val();
        var yAxis_name_right = $('#worstCellChartAuxiliaryAxisType').val(); 
        var startTime = $("#startTime").val();  //返回日期
        var endTime = $('#endTime').val();
        if($('#chooseTable').val() == 'lowAccessCell') {
           var table = 'FMA_alarm_log';
           var tableChart = 'lowAccessCell';
         }
        //alert(endTime);
        var params={
        db:'AutoKPI',
        table:tableChart,
        rowCell:record.cell,
        startTime:startTime,
        endTime:endTime,
        yAxis_name_left:yAxis_name_left,
        yAxis_name_right:yAxis_name_right
        }; 
     $.get('badCell/getChartData',params,function(data){ 
      $("#chart_zhaozi").hide(); 

        /*var cat_str =JSON.stringify(data.categories);
        var ser_str = JSON.stringify(data.series);*/
        var cat_str =JSON.stringify(JSON.parse(data).categories);
        var ser_str = JSON.stringify(JSON.parse(data).series);
    
      ser_str=ser_str.replace(/"/g,"");
      //ser_str=ser_str.replace(cell_str,"'"+cell_str+"'");
      ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
      ser_str=ser_str.replace("spline","'spline'");
      ser_str=ser_str.replace("#89A54E","'#89A54E'");
      ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
      ser_str=ser_str.replace("column","'column'");
      ser_str=ser_str.replace("#4572A7","'#4572A7'");

      // alert(cat_str);
      // alert(ser_str);

      var  cat_obj = eval("("+cat_str+")");     
      var  ser_obj = eval("("+ser_str+")");

      $('#worstCellContainer').highcharts({
        exporting: {   
            enabled:true,     
        },
        credits: {  
          enabled: false  
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: record.cell+" / " +worstCellType
        },
        // subtitle: {
        //     text: subNetwork_str+" / "+cell_str
        // },
        xAxis: [{
            categories: cat_obj
        }],
        yAxis: [{
            labels: {
                format: '{value} %',
                style: {
                    color: '#89A54E'
                }
            },
            title: {
                text: yAxis_name_left,
                style: {
                    color: '#89A54E'
                }
            },
            tickPositions: [0, 25, 50, 75, 100]
        }, {
          labels: {
                format: '{value}',
                style: {
                    color: '#4572A7'
                }
            },
            title: {
                text: yAxis_name_right,
                style: {
                    color: '#4572A7'
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            x: 0,
            verticalAlign: 'bottom',
            y: 0,
            floating: true,
            backgroundColor: '#FFFFFF'
        },
        series: ser_obj
    });
  }); 


$('#worstCellChartPrimaryAxisType').change(function(){
  $("#chart_zhaozi").show(); 
    var yAxis_name_left  = $('#worstCellChartPrimaryAxisType').val();
    var yAxis_name_right = $('#worstCellChartAuxiliaryAxisType').val(); 
    var startTime = $("#startTime").val();  //返回日期
    var endTime = $('#endTime').val();
    if($('#chooseTable').val() == 'lowAccessCell') {
           var table = 'FMA_alarm_log';
           var tableChart = 'lowAccessCell';
         }else if($('#chooseTable').val() == 'highLostCell'){
            var table = 'FMA_alarm_log';
            var tableChart = 'highLostCell';
         }else if($('#chooseTable').val() == 'badHandoverCell'){
           var table = 'FMA_alarm_log';
           var tableChart = 'badHandoverCell';
         }
    //alert(endTime);
    var params={
    db:'AutoKPI',
    table:tableChart,
    rowCell:record.cell,
    startTime:startTime,
    endTime:endTime,
    yAxis_name_left:yAxis_name_left,
    yAxis_name_right:yAxis_name_right
    }; 


 $.get('badCell/getChartData',params,function(data){  
    $("#chart_zhaozi").hide(); 
      var cat_str = JSON.stringify(JSON.parse(data).categories);
      var ser_str = JSON.stringify(JSON.parse(data).series);
    
      
      ser_str=ser_str.replace(/"/g,"");

      //ser_str=ser_str.replace(cell_str,"'"+cell_str+"'");
      ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
      ser_str=ser_str.replace("spline","'spline'");
      ser_str=ser_str.replace("#89A54E","'#89A54E'");
      ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
      ser_str=ser_str.replace("column","'column'");
      ser_str=ser_str.replace("#4572A7","'#4572A7'");

      // alert(cat_str);
      // alert(ser_str);

      var  cat_obj = eval("("+cat_str+")");     
      var  ser_obj = eval("("+ser_str+")");

      $('#worstCellContainer').highcharts({
        exporting: {   
            enabled:true,     
        },
        credits: {  
          enabled: false  
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: record.cell+" / " +worstCellType
        },
        // subtitle: {
        //     text: subNetwork_str+" / "+cell_str
        // },
        xAxis: [{
            categories: cat_obj
        }],
        yAxis: [{
            labels: {
                format: '{value} %',
                style: {
                    color: '#89A54E'
                }
            },
            title: {
                text: yAxis_name_left,
                style: {
                    color: '#89A54E'
                }
            },
            tickPositions: [0, 25, 50, 75, 100]
        }, {
          labels: {
                format: '{value}',
                style: {
                    color: '#4572A7'
                }
            },
            title: {
                text: yAxis_name_right,
                style: {
                    color: '#4572A7'
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            x: 0,
            verticalAlign: 'bottom',
            y: 0,
            floating: true,
            backgroundColor: '#FFFFFF'
        },
        series: ser_obj
    });

  }); 
})

$('#worstCellChartAuxiliaryAxisType').change(function(){
  $("#chart_zhaozi").show(); 
    var yAxis_name_left  = $('#worstCellChartPrimaryAxisType').val();
    var yAxis_name_right = $('#worstCellChartAuxiliaryAxisType').val(); 
    var startTime = $("#startTime").val();  //返回日期
    var endTime = $('#endTime').val();
   if($('#chooseTable').val() == 'lowAccessCell') {
           var table = 'FMA_alarm_log';
           var tableChart = 'lowAccessCell';
         }else if($('#chooseTable').val() == 'highLostCell'){
            var table = 'FMA_alarm_log';
            var tableChart = 'highLostCell';
         }else if($('#chooseTable').val() == 'badHandoverCell'){
           var table = 'FMA_alarm_log';
           var tableChart = 'badHandoverCell';
         }
    //alert(endTime);
    var params={
    db:'AutoKPI',
    table:tableChart,
    rowCell:record.cell,
    startTime:startTime,
    endTime:endTime,
    yAxis_name_left:yAxis_name_left,
    yAxis_name_right:yAxis_name_right
    }; 


 $.get('badCell/getChartData',params,function(data){  
    $("#chart_zhaozi").hide(); 
      var cat_str = JSON.stringify(JSON.parse(data).categories);
      var ser_str = JSON.stringify(JSON.parse(data).series);
   
      
      ser_str=ser_str.replace(/"/g,"");

      //ser_str=ser_str.replace(cell_str,"'"+cell_str+"'");
      ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
      ser_str=ser_str.replace("spline","'spline'");
      ser_str=ser_str.replace("#89A54E","'#89A54E'");
      ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
      ser_str=ser_str.replace("column","'column'");
      ser_str=ser_str.replace("#4572A7","'#4572A7'");

      // alert(cat_str);
      // alert(ser_str);

      var  cat_obj = eval("("+cat_str+")");     
      var  ser_obj = eval("("+ser_str+")");

      $('#worstCellContainer').highcharts({
        exporting: {   
            enabled:true,     
        },
        credits: {  
          enabled: false  
        },
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: record.cell+" / " +worstCellType
        },
        // subtitle: {
        //     text: subNetwork_str+" / "+cell_str
        // },
        xAxis: [{
            categories: cat_obj
        }],
        yAxis: [{
            labels: {
                format: '{value} %',
                style: {
                    color: '#89A54E'
                }
            },
            title: {
                text: yAxis_name_left,
                style: {
                    color: '#89A54E'
                }
            },
            tickPositions: [0, 25, 50, 75, 100]
        }, {
          labels: {
                format: '{value}',
                style: {
                    color: '#4572A7'
                }
            },
            title: {
                text: yAxis_name_right,
                style: {
                    color: '#4572A7'
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            x: 0,
            verticalAlign: 'bottom',
            y: 0,
            floating: true,
            backgroundColor: '#FFFFFF'
        },
        series: ser_obj
    });
  }); 

})
            var paramsLTE = {
                input1:3,
                input2:3,
                input3:50,
                input4:10,
                input5:50,
                cell : record.cell,
                dateTime : $("#startTime").val(),
                city : record.city
            };
            $.post("badCell/getLTENeighborHeader",paramsLTE,function(data){
                if(data.error == 'error'){
                  $("#LTE_zhaozi").hide();
                    return;
                }
                var fieldArr=new Array();
                for(var k in data){
                    if(fieldArr.length == 0){
                        fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
                    }else{
                        if (k == 'datetime_id') {
                            fieldArr[fieldArr.length]={field:k,title:k,width:180};
                        }else{
                            fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
                        }

                    }
                }

                $('#LTETable').grid('destroy', true, true);
                var grid = $("#LTETable").grid({
                    columns:fieldArr,
                    params:paramsLTE,
                    dataSource:{
                        url: 'badCell/getLTENeighborData', 
                        success: function(data){
                            data = eval("("+data+")");
                            grid.render(data);
                            $("#LTE_zhaozi").hide();
                        } 
                    },
                    pager: { limit: 10, sizes: [10, 20, 50, 100] },
                    autoScroll:true,
                    uiLibrary: 'bootstrap'
                });

            })

            var paramsGSM = {
                input1:3,
                input2:1,
                input3:50,
                input4:2,
                input5:50,
                input6:-90,
                input7:-15,
                cell : record.cell,
                dateTime : $("#startTime").val(),
                city : record.city
            };
            $.post("badCell/getGSMNeighborHeader",paramsLTE,function(data){
                if(data.error == 'error'){
                  $("#GSM_zhaozi").hide();
                    return;
                }
                var fieldArr=new Array();
                for(var k in data){
                    if(fieldArr.length == 0){
                        fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
                    }else{
                        if (k == 'datetime_id') {
                            fieldArr[fieldArr.length]={field:k,title:k,width:180};
                        }else{
                            fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
                        }

                    }
                }

                $('#GSMTable').grid('destroy', true, true);
                var grid = $("#GSMTable").grid({
                    columns:fieldArr,
                    params:paramsLTE,
                    dataSource:{
                        url: 'badCell/getGSMNeighborData', 
                        success: function(data){
                            data = eval("("+data+")");
                            grid.render(data);
                            $("#GSM_zhaozi").hide();
                        } 
                    },
                    pager: { limit: 10, sizes: [10, 20, 50, 100] },
                    autoScroll:true,
                    uiLibrary: 'bootstrap'
                });

            })

    l.stop();
    E.stop();
  });




  if(table == 'file'){
    var filename = $('#badCellFile').val();
    download(filename);
  }
  })
}


function download(url) {
      var browerInfo = getBrowerInfo();
      if (browerInfo=="chrome"){
          download_chrome(url);
      } else if (browerInfo == "firefox") {
          download_firefox(url);
      }
}

function download_chrome(url){
    var aLink = document.createElement('a');
    aLink.href=url;
       aLink.download = url;
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent("click", false, false);
    aLink.dispatchEvent(evt);
}

function download_firefox(url){
    window.open(url);
}

function getBrowerInfo(){
     var uerAgent = navigator.userAgent.toLowerCase();
     var format =/(msie|firefox|chrome|opera|version).*?([\d.]+)/;
     var matches = uerAgent.match(format);
     return matches[1].replace(/version/, "'safari"); 
}

//点击地图功能
function getCellData(mapv,e,$row,id,record){
  //alert('Row with id=' + record.cell + ' is selected.');
  //var mapv = initMap("map");
  // var cell = 'L42k47C';
  //var date = '2016-09-05';
  //alert(record.cell);
  $('#mapCell').val(record.cell);
  var date = $('#startTime').val();
  $.ajax({
                type: "GET",
                url: "switchData",
                data: {date: date,cell: record.cell},
                //data: {date: date,cell: 'L42k47C'},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].slongitude,
                            lat: returnData[i].slatitude,
                            count: count,
                            dir: returnData[i].sdir,
                            band: returnData[i].sband,
                            master: false,
                            scell: returnData[i].scell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].mlongitude,
                        lat: returnData[0].mlatitude,
                        count: -1,
                        dir:returnData[0].mdir,
                        band: returnData[0].mband,
                        master: true
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv.bmap.setViewport(points);

                    layerout = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchout',
                    // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var scells = [];
                                    for(var i=0;i<data.length;i++) {
                                        scells.push(data[i].scell);
                                    }
                                    var params = {
                                        // date: document.getElementById("date").value,
                                        // cell: document.getElementById("cell").value,
                                        date:date,
                                        cell:record.cell,
                                        scells: scells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"switchDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });

                                    $('#myModal').modal({
                                        keyboard: false
                                    })

                                },
//                                 mousemove: function(e, data) {
//                                     console.log('move', data)
//                                 }
                            }
                        }
                    });

                }
            })
}


function initMap(mapId) {
  var bmap = new BMap.Map(mapId);
  bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
  // 初始化地图,设置中心点坐标和地图级别
  bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);
  //bmap.addControl(new BMap.NavigationControl());
  //bmap.addControl(new BMap.MapTypeControl());
  //自定义控件
  function staticControl(){
    this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
    this.defaultOffset = new BMap.Size(10,10);
  }
  //继承Control的API
  staticControl.prototype = new BMap.Control();
  //初始化控件
  staticControl.prototype.initialize=function(map){
    var div = document.createElement('div');
    var btn1 = document.createElement('button');
    btn1.setAttribute('class','btn btn-mini btn-primary');
    btn1.setAttribute('type','button');
    btn1.textContent ='切出';
    btn1.onclick = function () {
      layerin.hide();
      // var newCell = $('#mapCell').val();
      // var newDate = $('#startTime').val();
      drawMapOut();
    }
    div.appendChild(btn1);
    var btn2 = document.createElement('button');
    btn2.setAttribute('class','btn btn-mini btn-primary');
    btn2.setAttribute('type','button');
    btn2.textContent ='切入';
    btn2.onclick = function () {
      layerout.hide();
      // var newCell = $('#mapCell').val();
      // var newDate = $('#startTime').val();
      drawMapIn();
    }
    div.appendChild(btn2);
    //添加DOM元素到地图中
    map.getContainer().appendChild(div);
    //返回DOM
    return div;
  }
  //创建控件实例
  var staticsCtrl = new staticControl();
  //添加到地图当中
  bmap.addControl(staticsCtrl);
  //自定义控件
  function LeftControl(){
    this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
    this.defaultOffset = new BMap.Size(10,10);
  }
  //继承Control的API
  LeftControl.prototype = new BMap.Control();
  //初始化控件
  LeftControl.prototype.initialize=function(map){
    var ul = document.createElement('ul');
    ul.setAttribute('class','list-group');
    ul.setAttribute('id','leftControl');
    var li = document.createElement('li');
    li.setAttribute('class','list-group-item');
    li.textContent = '请滑动鼠标查看小区名'
    ul.appendChild(li);
    //添加DOM元素到地图中
    map.getContainer().appendChild(ul);
    //返回DOM
    return ul;
  }
  //创建控件实例
  var leftCtrl = new LeftControl();
  //添加到地图当中
  bmap.addControl(leftCtrl);
  var mapv = new Mapv({
    drawTypeControl: false,
    map: bmap // 百度地图的map实例
  });
  $.ajax({
    type: "GET",
    url: "switchSite",
    dataType: "text",
    beforeSend: function () {
      $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
    },
    success: function (data) {
      $("#map_zhaozi").hide();
      var returnData = JSON.parse(data);
      var vdata = [];
      for (var i = 0; i < returnData.length; i++) {
        vdata.push({
          cell: returnData[i].cellName,
          lng: returnData[i].longitude,
          lat: returnData[i].latitude,
          count: 5,
          dir: returnData[i].dir,
          band: returnData[i].band,
        });
      }
      var layer = new Mapv.Layer({
        mapv: mapv, // 对应的mapv实例
        zIndex: 1, // 图层层级
        dataType: 'point', // 数据类型，点类型
        data: vdata, // 数据
        drawType: 'choropleth', // 展示形式
        dataRangeControl: false ,
        drawOptions: { // 绘制参数
          size: 20, // 点大小
          unit: 'px', // 单位
          strokeStyle: 'gray', // 描边颜色
          type: 'site',
          // splitList数值表示按数值区间来展示不同颜色的点
          splitList: [
            {
              start:0,
              end: 10,
              color: 'gray'
            }
          ],
          events: {
            mousemove: function (e, data) {
              //console.log('click', e, data);
              $("#leftControl").children().remove();
              var li = '';
              for (var i = 0; i < data.length; i++) {
                li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
              }
              //console.log(li);
              $("#leftControl").append(li);
            }
          }
        }
      });
    }
});

return {"bmap":bmap,"mapv":mapv};
}



        var layerout = null;
        var layerin = null;
        function drawMapOut() {
            //var mapv = initMap("map");
            var newCell = $('#mapCell').val();
            var newDate = $('#startTime').val();
            if(layerout != null) {
                layerout.hide();
            }
            $.ajax({
                type: "GET",
                url: "switchData",
                //data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                data:{date:newDate,cell:newCell},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].slongitude,
                            lat: returnData[i].slatitude,
                            count: count,
                            dir: returnData[i].sdir,
                            band: returnData[i].sband,
                            master: false,
                            scell: returnData[i].scell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].mlongitude,
                        lat: returnData[0].mlatitude,
                        count: -1,
                        dir:returnData[0].mdir,
                        band: returnData[0].mband,
                        master: true
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv.bmap.setViewport(points);

                    layerout = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchout',
                    // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var scells = [];
                                    for(var i=0;i<data.length;i++) {
                                        scells.push(data[i].scell);
                                    }
                                    var params = {
                                        // date: document.getElementById("date").value,
                                        // cell: document.getElementById("cell").value,
                                        date:newDate,
                                        cell:newCell,
                                        scells: scells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"switchDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });

                                    $('#myModal').modal({
                                        keyboard: false
                                    })

                                },
//                                 mousemove: function(e, data) {
//                                     console.log('move', data)
//                                 }
                            }
                        }
                    });

                }
            })

        }

        /*var drawMapIn = */function drawMapIn() {
          //var mapv = initMap("map");
          var newCell = $('#mapCell').val();
          var newDate = $('#startTime').val();
          //console.log(newCell);alert(newCell);
            if(layerin != null) {
                layerin.hide();
            }
//alert($('mapCell').val());
            $.ajax({
                type: "GET",
                url: "handoverin",
                //data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                data:{date:newDate,cell:newCell},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].mlongitude,
                            lat: returnData[i].mlatitude,
                            count: count,
                            dir: returnData[i].mdir,
                            band: returnData[i].mband,
                            master: false,
                            cell: returnData[i].cell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].slongitude,
                        lat: returnData[0].slatitude,
                        count: -1,
                        dir:returnData[0].sdir,
                        band: returnData[0].sband,
                        master: true
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv.bmap.setViewport(points);

                    layerin = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchin',
                            // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var cells = [];
                                    for(var i=0;i<data.length;i++) {
                                        cells.push(data[i].cell);
                                    }
                                    var params = {
                                        // date: document.getElementById("date").value,
                                        // cell: document.getElementById("cell").value,
                                        cell:newCell,
                                        date:newDate,
                                        cells: cells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"handOverInDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });

                                    $('#myModal').modal({
                                        keyboard: false
                                    })

                                },
//                                 mousemove: function(e, data) {
//                                     console.log('move', data)
//                                 }
                            }
                        }
                    });

                }
            })
        }

  function textWidth(text){
    var length = text.length;
    if(length > 15){
        return length*10;
    }
    return 150;
}