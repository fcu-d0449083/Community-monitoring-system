<?




$dayData = array(
    'data' => array(),
    'days' => array()
);
$daysData = array(
    'last30Day' => $dayData,
    'last7Day' => $dayData,
    'today' => $dayData
);

for($ii = 29; $ii >= 0; $ii--){
    $day = date('Y/m/d',strtotime('-'.$ii.' day'));
    $daysData['last30Day']['days'][] = $day;
}
for($ii = 6; $ii >= 0; $ii--){
    $day = date('Y/m/d',strtotime('-'.$ii.' day'));
    $daysData['last7Day']['days'][] = $day;
}
$daysData['today']['days'][] = date('Y/m/d');

$sql = "SELECT * FROM `time` ";
$query =  mysqli_query($conn, $sql);
$selectData = array();
$echartsData = array();
while ($data = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $data['user_name'] = str_replace("['`","",$data['user_name']);
    $data['user_name'] = str_replace("`']","",$data['user_name']);
    if(!in_array($data['user_name'],$selectData)){
        $selectData[] = $data['user_name'];
    }
    if(!isset($echartsData[$data['user_name']])){
        $echartsData[$data['user_name']] = $daysData;
    }
    $date = date('Y/m/d',strtotime($data['year'].'/'.$data['month'].'/'.$data['day']));
    $hour = $data['hour']*1;
    foreach ($daysData as $key => $dayData){
        if(in_array($date, $dayData['days'])){
            $daysKey = array_search($date, $dayData['days']);
            if(!isset($echartsData[$data['user_name']][$key]['data'][$hour.','.$daysKey])){
                $echartsData[$data['user_name']][$key]['data'][$hour.','.$daysKey] = 0;
            }
            $echartsData[$data['user_name']][$key]['data'][$hour.','.$daysKey] += 1;
        }
    }
}



?>
<script type="text/javascript" src="echarts.min.js"></script>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">

    var last30Day = [
        <?foreach ($daysData['last30Day']['days'] as $day){?>
        '<?=$day?>',
        <?}?>
    ];
    var last7Day = [
        <?foreach ($daysData['last7Day']['days'] as $day){?>
        '<?=$day?>',
        <?}?>
    ];
    var today = [
        <?foreach ($daysData['today']['days'] as $day){?>
        '<?=$day?>',
        <?}?>
    ];

    <?foreach ($selectData as $name){?>
    $("#xType").append("<option value='<?=$name?>'><?=$name?></option>");
    <?}?>


    <?foreach ($echartsData as $user_name => $daysData){?>
        var data_<?=$user_name?> ={
            <?foreach ($daysData as $day => $dayData){?>
                <?=$day?>:[
                    <?foreach ($dayData['data'] as $loc => $num){?>
                        [<?=$loc?>,<?=$num?>],
                    <?}?>
                ],
            <?}?>
        };
    <?}?>

    setChart(window[$('#yType').val()],window["data_"+$('#xType').val()][$('#yType').val()],$('#xType').val());

    $(document).on("change", "#yType,#xType", function(){
        setChart(window[$('#yType').val()],window["data_"+$('#xType').val()][$('#yType').val()],$('#xType').val());
    });

    function setChart(days,data,name) {
        var hours = ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
        var dom = document.getElementById("container_echarts");
        var myChart = echarts.init(dom);
        option = null;

        option = {
            tooltip: {
                position: 'top'
            },
            animation: false,
            grid: {
                height: '80%',
                y: '10%'
            },
            xAxis: {
                type: 'category',
                data: hours,
                splitArea: {
                    show: true
                }
            },
            yAxis: {
                type: 'category',
                data: days,
                splitArea: {
                    show: true
                }
            },
            visualMap: {
                min: 0,
                max: 10,
                calculable: true,
                orient: 'horizontal',
                left: 'center',
                bottom: '0'
            },
            series: [{
                name: name,
                type: 'heatmap',
                data: data,
                label: {
                    normal: {
                        show: true
                    }
                },
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };;
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    }
</script>