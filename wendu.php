<html>
<head>
<meta charset="utf-8">
<title>树莓派温度计</title>
	
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.0.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.js"></script>
	
	<script>
		
		
				var temperature=[];
				var humidity=[];
				var time=[];
			function drawChart2(chartID,c_type){
				var ctx2 = document.getElementById(chartID).getContext('2d');
				
				
				var myChart2 = new Chart(ctx2, {
					type: c_type,
					data: {
						labels: [],
						datasets: [{
							label:'温度',
							data: [],
							backgroundColor:[],
							borderColor:[],
							fill:'+1'
						},
						{
							label:'湿度',
							data: [],
							backgroundColor:[],
							borderColor:[],
							
						}],
						

					},
					options: {

						plugins: {
							filler: {
								propagate: true
							}
						}
						
					}
				});
				
				var table_tr_length2=document.getElementById('table').getElementsByTagName("tr").length;
				var na_2=1;
				var rowss_2=document.getElementById('table').rows;
				

				
				
				while(na_2<table_tr_length2){
					var now2=rowss_2[na_2];
					var temp=now2.cells[0].firstChild.nodeValue;
					var hum=now2.cells[1].firstChild.nodeValue;
					var tim=now2.cells[2].firstChild.nodeValue;
					
					
					temperature.push(temp);
					humidity.push(hum);
					time.push(tim);
					na_2++;
				}
				for(var i=temperature.length-1;i>=0;i--){
					ChartaddData(myChart2,time[i],temperature[i],0,'rgba(255,133,27,1.0)','rgba(255,220,0,0.5)');
					ChartaddData(myChart2,'',humidity[i],1,'rgba(0, 116, 217,1.0)','rgba(57,201,201,0.5)');
				}
				
			

			}
			
			function arraysum(arr){
				var s=0
				for(var i=arr.length-1;i>=0;i--){
					s+=arr[i];
				}
				return s;
			}
			
			function ChartaddData(chart,label,data,addto,boderColor,Bgcolor){
				if(label!=''){
					chart.data.labels.push(label);
				}
				

				
				chart.data.datasets[addto].data.push(data);
				
					//随机颜色
				
				chart.data.datasets[addto].backgroundColor.push(Bgcolor);
				chart.data.datasets[addto].borderColor.push(boderColor);
				
				chart.update();
			}
		
		function chart(){
			drawChart2('chart','line');
			document.getElementById('now_temp').innerHTML=document.getElementById('table').rows[1].cells[0].innerHTML;
			document.getElementById('now_hum').innerHTML=document.getElementById('table').rows[1].cells[1].innerHTML;
			document.getElementById('now_time').innerHTML=document.getElementById('table').rows[1].cells[2].innerHTML;
		}
	</script>

</head>
<?php
	$con=mysqli_connect('数据库地址','数据库用户名','密码','数据库名');
	if(!$con){
		die('oops connection problem ! --> '.mysqli_error());
	}
?>
<?php
	date_default_timezone_set('Asia/Shanghai');
	$res=mysqli_query($con,"select * from 表名 order by id desc limit 10;");
	
	?>
	
<body onLoad="chart();">
	<div class="container">
		
			<h1>- 树莓派网页温度计 +</h1>
			<h5>更新时间: <span id="now_time"></span></h5>

		<hr>
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-body">
						<h1 class="card-title">当前温度：<span id="now_temp"></span>℃</h1>
						
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card">
					<div class="card-body">
						<h1 class="card-title">当前湿度：<span id="now_hum"></span>%
						</h1>
						
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-body">
						<h1 class="card-title">历史</h1>
						<canvas id="chart" height="80"></canvas>
						<table class="table" id="table">
							<th>温度</th><th>湿度</th><th>时间</th>
								<?php
									while($row=mysqli_fetch_array($res)){
										echo '<tr><td>'.$row['temperature'].'</td><td>'.$row['humidity'].'</td><td>'.date('Y-m-d / H:i:s',$row['time']).'</td></tr>';
									}
								?>
						</table>
						

					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
