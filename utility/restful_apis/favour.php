<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 下午3:16
 * [赞]功能
 */

/*
 * 点赞
 * 方法: POST
 * 参数:
 *      photo_id 照片在数据库中的id
 *      username 点赞的用户名
 */

// 参数检查
if ($_SERVER["REQUEST_METHOD"] != "POST"){
    error_log("NOT POST METHOD IN FAVOUR");
    echo json_encode(array("ERROR"=>"POST METHOD NEEDED"));
    return;
}

if (!isset($_POST["photo_id"]) || !isset($_POST["username"])){
    error_log("MISSING PARAMETERS IN FAVOUR");
    echo json_encode(array("ERROR"=>"MISSING PARAMETERS IN FAVOUR(photo_id username)"));
    return;
}

require_once("../database/database_operations.php");

$count = favour_photo($_POST["photo_id"], $_POST["username"]);
echo json_encode(array("favour_count" => $count));