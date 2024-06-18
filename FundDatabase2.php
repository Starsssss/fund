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

    // 准备数据
$columns = implode(', ', array_keys($data[0])); // 假设所有数据的列名相同，这里取第一条数据的列名
$insertValues = [];
$updateValues = [];
$table = 'funds1';
foreach ($data as $row) {
    $insertValues[] = '(' . implode(', ', array_map(function($value) use ($conn) {
        // return "'" . $conn->real_escape_string($value) . "'";
        if(is_null($value)) {
            return "NULL";
        }
        return "'" .$value. "'";
        
    }, array_values($row)) ) . ')';
    
    foreach ($row as $key => $value) {
        $updateValues[] = "$key = VALUES($key)";
    }
}

// 构建SQL语句
$sql = "INSERT INTO $table ($columns) VALUES " . implode(', ', $insertValues) . " ON DUPLICATE KEY UPDATE " . implode(', ', $updateValues);

// 执行SQL语句
if ($conn->multi_query($sql) === TRUE) {
    echo "新记录插入成功";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    die;
}
}
