<?php

require "FundDatabase.php";

header('Content-Type: text/plain; charset=utf-8');
// 设置内部编码为 UTF-8
mb_internal_encoding('UTF-8');

function get_fund_data($code)
{
    // FundDatabase::insertData('');
    // ;
}

function curl_get($url, $params = array(), $headers = array())
{
    // 初始化 cURL
    $ch = curl_init();

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //ssl
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // 执行 cURL 请求并获取响应
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
        die;
    }

    // 关闭 cURL 资源
    curl_close($ch);
    return $response;
}
function getFundList($pageNum=1){
    $url = "https://www.sse.org.cn/api/report/ShowReport/data?SHOWTYPE=JSON&CATALOGID=1105&TABKEY=tab1&PAGENO={$pageNum}&random=0.5965912139754759";
    $url = "https://www.sse.org.cn/api/report/ShowReport/data?SHOWTYPE=JSON&CATALOGID=1105&TABKEY=tab1&selectTzlb=%E5%80%BA%E5%88%B8%E5%9F%BA%E9%87%91&random=0.31431441397901394&PAGENO={$pageNum}&SIZE=200";
    $res = curl_get($url);
    $res = json_decode($res, true);
    $count=$res[0]['metadata']['pagecount'];
    for ($i=$pageNum; $i <= $count; $i++) { 
        $url = "https://www.sse.org.cn/api/report/ShowReport/data?SHOWTYPE=JSON&CATALOGID=1105&TABKEY=tab1&selectTzlb=%E5%80%BA%E5%88%B8%E5%9F%BA%E9%87%91&random=0.31431441397901394&PAGENO={$i}&SIZE=200";
        $res = curl_get($url);
        $res = json_decode($res, true);
        foreach ($res[0]['data'] as $key => $value) {
            $str = $value['sys_key'];
            $pattern = '/<u>(.*?)<\/u>/';
            preg_match($pattern, $str, $matches);
           jj($matches[1]); // 输出 159649
        }
    }
}

function all($pageNum = 1)
{
    $SIZE = 200; // 前500个基金
    $TYPE = 2;
    $order_by = '5y';
    $totalPages = 1000; // 假设总页数为1000

    do {
        $url = "https://danjuanfunds.com/djapi/v3/filter/fund?type={$TYPE}&order_by=&size={$SIZE}&page={$pageNum}";
        $res = curl_get($url);
        $res = json_decode($res, true);
        var_dump('sleep...',$pageNum,$SIZE,json_encode($res));
        foreach ($res['data']['items'] as $key => $v) {
            jj($v['fd_code']);
        }

        // sleep(1);


        $pageNum++;
    } while ($pageNum <= $totalPages);
}

function jj($code,$name)
{
    // 设置请求的参数
    //500天涨幅记录
    $size = 80;
    $page = 1;
    $type = 1;
    $order_by = '3y';
    $url = "https://danjuanfunds.com/djapi/fund/nav/history/$code?$type&order_by=$order_by&size=$size&page=$page";

    $response = curl_get($url);
    $fundDetail = curl_get("https://danjuanfunds.com/djapi/fund/{$code}");
    $fundDetail = json_decode($fundDetail, true);
    $fundRateDetail = curl_get("https://danjuanfunds.com/djapi/fund/base/quote/data/index/analysis/{$code}");
    $fundRateDetail = json_decode($fundRateDetail, true);


    $eggCount = 0;
    $eggDays = 0;
    // 处理响应数据
    if ($response) {
        // 在这里对响应数据进行处理，可以是 JSON 解析或其他操作
        $data = json_decode($response, true);
        if ($data) {
            // 处理解析后的数据
            // var_dump($data);die;       
            foreach ($data['data']['items'] as $key => $v) {
                $eggCount += $v['percentage'] * 100;
                if ($v['percentage'] > 0) {
                    $eggDays++;
                }
            }
            $yesterday_percentage = null;
            $today_percentage = null;
            if (date('Y-m-d') == $data['data']['items'][0]['date']) {
                // 今天收蛋
                // echo 'TODAY今天收蛋' . ($data['data']['items'][0]['percentage'] * 100) . PHP_EOL;
                $today_percentage = $data['data']['items'][0]['percentage'];
            } else {
                // echo 'YESTERDAY 昨天收蛋' . ($data['data']['items'][0]['percentage'] * 100) . PHP_EOL;
                $yesterday_percentage = $data['data']['items'][0]['percentage'];
            }
            $data = array(
                'code' => $code,
                'fund_name' => $name,//$fundDetail['data']['fd_name'],
                'egg_count_100' => $eggCount,
                // 'egg_count_30d' => $eggCount,
                // 'egg_count_100d' => $eggCount,
                // 'egg_count_365d' => $eggCount,
                // 'egg_count_3y' => $eggCount,
                // 'egg_count_5y' => $eggCount,
                'egg_day_100' => $eggDays,
                // 'egg_day_30d' => $eggDays,
                // 'egg_day_100d' => $eggDays,
                // 'egg_day_365d' => $eggDays,
                'yesterday_percentage' => $yesterday_percentage,
                'today_percentage' => $today_percentage,
                'max_draw_down_1y' => $fundRateDetail['data']['index_data_list'][0]['average_index']['max_draw_down'],
                'max_draw_down_3y' => $fundRateDetail['data']['index_data_list'][1]['average_index']['max_draw_down'],
                'max_draw_down_5y' => $fundRateDetail['data']['index_data_list'][2]['average_index']['max_draw_down'],
                'sharpe_rank_1y' => $fundRateDetail['data']['index_data_list'][0]['average_index']['sharpe_rank'],
                'sharpe_rank_3y' => $fundRateDetail['data']['index_data_list'][1]['average_index']['sharpe_rank'],
                'sharpe_rank_5y' => $fundRateDetail['data']['index_data_list'][2]['average_index']['sharpe_rank'],
                'volatility_rank_1y' => $fundRateDetail['data']['index_data_list'][0]['average_index']['volatility_rank'],
                'volatility_rank_3y' => $fundRateDetail['data']['index_data_list'][1]['average_index']['volatility_rank'],
                'volatility_rank_5y' => $fundRateDetail['data']['index_data_list'][2]['average_index']['volatility_rank'],
                'risk_level' => $fundDetail['data']['risk_level'],
                'type_desc' => $fundDetail['data']['type_desc'],
                'can_buy' => $fundDetail['data']['can_buy'] == true ?1:0,
                'data' => json_encode($data)
            );
            insert($data);

            // ...
        } else {
            echo 'Failed to decode JSON.';die;
        }
    } else {
        echo 'No response from the server.';die;
    }
    //php命令行执行脚本出现中文乱码
    // header("Content-type:text/html;charset=utf-8");
    echo ("code:{$code},{$size}天收蛋{$eggCount}个, 收蛋共计{$eggDays}天" . PHP_EOL);


}
// getFundList();
$js_string=curl_get('https://fund.eastmoney.com/js/fundcode_search.js');
// 提取包含数据的部分，即 "[["000001","HXCZHH"]"
$data_string = substr($js_string, strpos($js_string, '[['));
// 移除结尾的分号
$data_string = rtrim($data_string, ';');

// 解析JSON格式的数据
$data = json_decode($data_string);
foreach ($data  as $v) {
    if (intval($v[0])<14980) {
        continue;
    }
   jj($v[0],$v[2]);
}
/**
 * 
 */

// jj('010811');//湘财久盈中短债C
// jj('007755');//上银慧永利中短期债券C
// jj('006337');// 
// jj('007677');//
// jj('008395');//
// jj('012622');//
// jj('020741');
// jj('002882');
// jj('014570');
// jj('000640');
// jj('007741');
// jj('001820');
// jj('012265');
// jj('217011');
// jj('007195');
// jj('004908');
// jj('007227');
// jj('008395');
// jj('016619');
// jj('008395');
// jj("000001");

// all(2);
echo '结束';