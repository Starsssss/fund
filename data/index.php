<!DOCTYPE html>
<html>

<head>
    <title>多指标折线图</title>
    <!-- 引入ECharts库 -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.2.2/dist/echarts.min.js"></script>
</head>

<body>
    <div id="main" style="width: 600px;height:400px;"></div>
    <script type="text/javascript">
        // 基于准备好的dom，初始化ECharts实例
        var myChart = echarts.init(document.getElementById('main'));
        <?php include '../FundDatabase.php';

        $rows = query();
        $data = [];
        // var_dump($rows[0]['data']);die;
        $apiData1 = $rows[0]['data'] ?: '';
        $apiData1= json_decode($apiData1,true);
       
        $apiData1= $apiData1['data']['items'] ?: []; $apiData1= array_reverse($apiData1);
        $categories = array_column($apiData1, 'date');
        $data['name'] = array_column($rows, 'fund_name');
        $data['categories'] = $categories;
        foreach ($rows as $key => $v) {
            $apiData = $v['data']?: '';
            $apiData= json_decode($apiData,true);
            $dataArr=$apiData['data']['items'];
            $dataArr= array_reverse($dataArr);
            $categories = array_column($dataArr, 'date');
            $data['categories'] = $categories;
            $data['series'][] = [
                'name'=>$v['fund_name'],
                'type'=>'line',
                'data'=>array_column($dataArr, 'percentage')
            ];
        }
        $option = [

        ];
        ?>
        // 假设您的数据是这样的
        // var data = <?php echo json_encode($data); ?>;

        // 假设数据结构如下（示例数据）
        var data = {
            categories: ['A', 'B', 'C', 'D', 'E'],
            series1: [120, 200, 150, 80, 70],
            series2: [90, 150, 200, 110, 100]
        };

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '多指标折线图'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['指标1', '指标2']
            },
            xAxis: {
                type: 'category',
                data: data.categories
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '指标1',
                    type: 'line',
                    data: data.series1
                },
                {
                    name: '指标2',
                    type: 'line',
                    data: data.series2
                }
            ]
        };
        option.legend.data = <?php echo json_encode($data['categories']); ?>;
        option.series = <?php echo json_encode($data['series']); ?>;
        option.xAxis.data = <?php echo json_encode($categories); ?>;
        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
</body>

</html>