<?php
if (!defined('ROOT_PATH')) {
    if (is_file('../../../shop/configs/config.ini.php')) {
        require_once '../../../shop/configs/config.ini.php';
    } else {
        die('请先运行index.php,生成应用程序框架结构！');
    }

}
set_time_limit(0);
$file_sql = "test.sql";
$content1 = file_get_contents($shop_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content1 . "\n", FILE_APPEND | LOCK_EX);

$content2 = file_get_contents($shop_admin_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content2 . "\n", FILE_APPEND | LOCK_EX);

$content3 = file_get_contents($ucenter_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content3 . "\n", FILE_APPEND | LOCK_EX);

$content4 = file_get_contents($ucenter_admin_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content4 . "\n", FILE_APPEND | LOCK_EX);

$content5 = file_get_contents($paycenter_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content5 . "\n", FILE_APPEND | LOCK_EX);

$content6 = file_get_contents($paycenter_admin_api_url . "/install/data/sql/" . $file_sql);
file_put_contents("./test.sql", $content6 . "\n", FILE_APPEND | LOCK_EX);

$contents = file_get_contents("./test.sql");

$host = "127.0.0.1";
$userName = "root";
$password = "root";

$con = @mysqli_connect($host, $userName, $password);
$result = mysqli_query($con, 'show databases');
$databases = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (strpos($row['Database'], 'local') !== false) {
        array_push($databases, $row['Database']);
    }
}

foreach ($databases as $k => $v) {
    $mysqli = new mysqli($host, $userName, $password, $v);
    if ($mysqli->connect_error) {
        die($mysqli->connect_error);
    }
    $res = $mysqli->multi_query($contents);
    $mysqli->close();
}

?>
