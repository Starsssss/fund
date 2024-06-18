<?php
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// 配置数据库连接
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'your_host',
    'database'  => 'your_database',
    'username'  => 'your_username',
    'password'  => 'your_password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 定义 "Fund" 模型
class Fund extends Illuminate\Database\Eloquent\Model {
    protected $table = 'funds';
    public $timestamps = false;
}

// 插入数据
$data = [
    ['fund_id' => 5, 'fund_name' => 'Fund 5', 'fund_type' => 'Type E', 'establishment_date' => '2025-01-15', 'asset_size' => 4000000.00, 'latest_nav' => 30.1234, 'one_year_return' => 25.67, 'fund_manager' => 'Manager E', 'fees' => 5.67],
    ['fund_id' => 6, 'fund_name' => 'Fund 6', 'fund_type' => 'Type F', 'establishment_date' => '2024-05-20', 'asset_size' => 5000000.00, 'latest_nav' => 35.6789, 'one_year_return' => 28.91, 'fund_manager' => 'Manager F', 'fees' => 6.78]
];

Fund::insert($data);

// 删除数据
Fund::where('fund_id', 5)->delete();

// 更新数据
Fund::where('fund_id', 6)->update(['fund_name' => 'New Fund 6']);

// 查询数据
$fetchedData = Fund::all();
print_r($fetchedData->toArray());
?>
