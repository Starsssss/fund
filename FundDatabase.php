<?php
function insert($data)
{
    // 数据库连接
    $servername = "127.0.0.1";
    $username = "root";
    $password = "root";
    $dbname = "fund";


    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 创建记录
    if (1) {
        // 处理字符串值，给值两端加上引号
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = "'" . $value . "'";
            } else if (is_null($value)) {
                $data[$key] = "NULL";
            }
        }
        // 处理null值
        // $today_percentage = $today_percentage !== null ? "'" . $today_percentage . "'" : "NULL";
        // $yesterday_percentage = $yesterday_percentage !== null ? "'" . $yesterday_percentage . "'" : "NULL";
        // $sql = "INSERT INTO funds (code,fund_name,egg_count_100,egg_day_100,yesterday_percentage,today_percentage, max_draw_down_1y,data) VALUES ('$code','$name','$eggCount','$eggDays',$yesterday_percentage,$today_percentage,'$max_draw_down_1y','$data')
        // ON DUPLICATE KEY UPDATE  egg_count_100 = VALUES(egg_count_100);";
        $table = 'funds1';
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', $data);
        foreach ($data as $key => $value) {
            $updateValues .= "$key = VALUES($key), ";
        }
        $updateValues = rtrim($updateValues, ', '); // Remove the trailing comma and space
        
        $sql = "INSERT INTO $table ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $updateValues";
        if ($conn->query($sql) === TRUE) {
            echo "新记录插入成功";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            die;
        }
    }
}

function query(){
    $servername = "127.0.0.1";
    $username = "root";
    $password = "root";
    $dbname = "fund";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
   $sql = "SELECT * FROM funds1 WHERE type_desc LIKE '%短债%' order by egg_count_100 DESC limit 5";
   $res=$conn->query($sql);
   $data=[];
   while($row = $res->fetch_assoc()) {
    //    echo "code: " . $row["code"]. " - fund_name: " . $row["fund_name"]. "<br>";
       $data[]=$row;
   }
   return $data;
}
