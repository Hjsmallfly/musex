<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 下午3:16
 * 用于获取最新的照片
 */

require_once("../database/database_operations.php");

/*
 * api:
 * 方法: get
 * 参数: count 最新的 count 张照片 默认是10张, 负数表示全部照片
 */

// 参数检查
if(!isset($_GET["count"])){
    get_latest_photos(10);
}elseif($_GET["count"] == 0){
    echo json_encode(array("ERROR" => "count must be grater than zero"));
    return;
}else
    get_latest_photos($_GET["count"]);

function get_latest_photos($count){
    global $db;
    if ($count > 0) {
        $stmt = $db->prepare("SELECT * from Photos ORDER BY moment DESC LIMIT :count");
        $stmt->bindParam(":count", $count, PDO::PARAM_INT);
    }
    else
        $stmt = $db->prepare("SELECT * from Photos ORDER BY moment DESC");
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//        var_dump($result);
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }catch (PDOException $e){
        error_log("ERROR WHILE get latest_photos: " . $e->getMessage());
        echo $e->getMessage();
    }
}


