jQuery(document).ready(function () {
   setTime();
   search();
   toogle('weakCoverRatio');
});

function search(){
	var l = Ladda.create( document.getElementById( 'search' ) );
	l.start();
	var time = $('#startTime').val();
	var params = {
		date:time
	}
	$.get('SearchWeakCoverRatio', params, function(data){
			//console.log(data);
			l.stop();
			if(data == 'databaseNotExists'){
				alert('数据库不存在');
				//l.stop();
				return;
			}
			data = eval('('+data+')');
            $('#weakCoverRatio').html('');
            $('#weakCoverRatio').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: null
                },
                subtitle: {
		            text: data['date']
		        },
                xAxis: {
                    categories: data['category']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                tooltip: {
		            pointFormat: '弱覆盖占比：{point.y:.1f} %',
		            shared: true,
		            useHTML: true
		        },
                legend:{
                    enabled:false
                },
                credits: {
                    enabled: false,
                },
                series: data['series']
            });
    
        });
}

function setTime(){
  $("#startTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
  var nowTemp = new Date();
  $("#startTime").datepicker('setValue', nowTemp);

  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $('#startTime').datepicker({
	onRender: function(date) {
	  return date.valueOf() < now.valueOf() ? '' : '';
	}
  }).on('changeDate', function(ev) {
	checkin.hide();
	}).data('datepicker');
}
