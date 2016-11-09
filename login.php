<?php
/**
 * Created by PhpStorm.
 * User: 七月在线科技
 * Date: 2016/11/8
 * Time: 12:16
 */
use \Firebase\JWT\JWT;
include "pdo.php";
include "utils.php";
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
$sql = "select * from " . TB_USER . " where email = '" . $account . "' and password = '" . $password . "'";
$result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    //邮箱登陆失败
    $sql = "select * from " . TB_USER . " where phone_no = '" . $account . "' and password = '" . $password . "'";
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
$uid = $result["uid"];
$last_login = time();
$login_ip = utils::getClientIP();
$login_num = $result["login_num"];
//先获取登录次数，如果登录次数大于0的话直接递增否则的话置为1
if($login_num>0){
	$login_num++;
}else{
	$login_num = 1;
}
//更新数据库相应记录
$sql = "update ".TB_USER." set last_login=?,login_num=?,login_ip=? where uid = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1,$last_login);
$stmt->bindValue(2,$login_num);
$stmt->bindValue(3,$login_ip);
$stmt->bindValue(4,$uid);
$stmt->execute();
//组装用户信息
$data = array();
$data["userinfo"] = $result;
$key = "example_key";
$token = array(
	"iat" => time(),
	"exp" = > 24*7*3600
);
$jwt = JWT::encode($token,$key);
$response["errno"] = 0;
$response["msg"] = "登录成功";
$response["data"] = $data;
echo json_encode($response);
die();
