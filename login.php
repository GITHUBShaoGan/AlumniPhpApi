<?php
/**
 * Created by PhpStorm.
 * User: 七月在线科技
 * Date: 2016/11/8
 * Time: 12:16
 */
include "pdo.php";
//校验参数是否传递
if (!isset($_POST["account"]) || !isset($_POST["password"])) {
    $response["errno"] = 1001;
    $response["msg"] = "POST值不合法";
    echo json_encode($response);
    die();
}
$account = $_POST["account"];
$password = $_POST["password"];
if ($account == "") {
    $response["errno"] = 1002;
    $response["msg"] = "账户不合法";
    echo json_encode($response);
    die();
}
if (strlen($password) < 4 || strlen($password) > 128) {
    $response["errno"] = 1003;
    $response["msg"] = "密码长度不符合规定";
    echo json_encode($response);
    die();
}
$sql = "select * from " . TB_USER . " where email = '" . $account . "' and password = '" . md5($password) . "'";
$result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    //邮箱登陆失败
    $sql = "select * from " . TB_USER . " where phone_no = '" . $account . "' and password = '" . md5($password) . "'";
    $result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //手机号登录失败
        $response["errno"] = 1004;
        $response["msg"] = "账号或者密码错误";
        echo json_encode($response);
        die();
    }
}
//登录成功
