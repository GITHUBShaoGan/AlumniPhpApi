<?php
/**
 * Created by PhpStorm.
 * User: 七月在线科技
 * Date: 2016/11/8
 * Time: 12:17
 */
include "dbconst.php";
$response = array();
try {
    $pdo = new PDO(DSN, USER, PWD);
} catch (Exception $e) {
    $response["errno"] = "1000";
    $response["msg"] = "创建PDO实例失败" . $e->getMessage();
    echo json_encode($response);
    die();
}