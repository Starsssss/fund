<?php

function columnNameToNumber($columnName) {
    $columnNumber = 0;
    $columnName = strtoupper($columnName);
    $length = strlen($columnName);

    for ($i = 0; $i < $length; $i++) {
        $columnNumber *= 26;
        $columnNumber += ord($columnName[$i]) - 64;
    }

    return $columnNumber;
}

// 使用示例
$columnName = 'II';
$columnNumber = columnNameToNumber($columnName);
echo $columnName . '列对应的列号是：' . $columnNumber;