<?php
	include "token/JWT.php";
	include "utils.php";
	include "pdo.php";
	if(!isset($_POST["account"])||!isset($_POST["password"])||!isset($_POST["nickname"])){
		$response["errno"] = 2000;
		$response["msg"] = "POST值不合法";
		echo json_encode($response);
		die();
	}
	$account = $_POST["account"];
	$password = $_POST["password"];
	$nickname = $_POST["nickname"];
	if($account == ""){
		$response["errno"] = 2001;
		$response["msg"] = "账户不能为空";
		echo json_encode($response);
		die();
	}
	if(strlen($password)<4||strlen($password)>128){
		$response["errno"] = 2002;
		$response["msg"] = "密码长度不符合规则";
		echo json_encode($response);
		die();
	}
	if(strlen($nickname) == 0){
		$response["errno"] = 2005;
		$response["msg"] = "昵称不能为空";
		echo json_encode($response);
		die();
	}
	$sql = "select * from ".TB_USER." where phone_no = '".$account."' and password = '".$password."'";
	$result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	if($result){
		//电话号码已被注册
		$response["errno"] = 2003;
		$response["msg"] = "电话号码已被注册";
		echo json_encode($response);
		die();
	}
	$sql = "select * from ".TB_USER." where email = '".$account."' and password = '".$password."'";
	$result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	if($result){
		$response["errno"] = 2004;
		$response["msg"] = "邮箱已经被注册";
		echo json_encode($response);
		die();
	}
	$reg_time = time();
	$last_login = time();
	$login_num = 0;
	$login_ip = utils::getClientIP();
	if(filter_var($account,FILTER_VALIDATE_EMAIL)){
		//邮箱注册
		$sql  = "insert into ".TB_USER." (email,password,nickname,reg_time,last_login,login_num,login_ip) values (?,?,?,?,?,?,?)";
	}else{
		$sql = "insert into ".TB_USER." (phone_no,password,nickname,reg_time,last_login,login_num,login_ip) values(?,?,?,?,?,?,?)";
	}
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(1,$account);
	$stmt->bindValue(2,$password);
	$stmt->bindValue(3,$nickname);
	$stmt->bindValue(4,$reg_time);
	$stmt->bindValue(5,$last_login);
	$stmt->bindValue(6,$login_num);
	$stmt->bindValue(7,$login_ip);
	$result = $stmt->execute();
	if(!$result){
		//注册失败
		$response["errno"] = 2006;
		$response["msg"] = "注册失败，插入数据到数据库失败";
		echo json_encode($response);
		die();
	}
	$response["errno"] = 0;
	$response["msg"] = "注册成功";
	echo json_encode($response);
	die();

