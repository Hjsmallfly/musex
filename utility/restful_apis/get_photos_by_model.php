<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午2:00
 * 按照模特展示照片
 */

/*
 * 方法: get
 * 参数:
 * model_id 模特在数据库中的主键
 */

// 检查参数
if(!isset($_GET["model_id"])){
    $error_string = "model_id needed";
    echo json_encode(array("ERROR"=>$error_string));
    return;
}

require_once("../database/database_operations.php");
// 执行函数
get_photos_by_model($_GET["model_id"]);

function get_photos_by_model($model_id){
    if ($model_id <= 0){
        $error_string = "model_id must be grater than zero";
        echo json_encode(["ERROR"=>$error_string]);
        return;
    }

    global $db;
    $stmt = $db->prepare("SELECT * FROM Photos WHERE model_id=:model_id ORDER BY moment DESC");
    $stmt->bindParam(":model_id", $model_id, PDO::PARAM_INT);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result){
            echo json_encode($result);
            return;
        }else{
            $error_string = "no photos of this model founded";
            echo json_encode(["ERROR"=>$error_string]);
            return;
        }
    }catch (PDOException $e){
        error_log("ERROR WHILE get photos by model_id: " . $e->getMessage());
        echo $e->getMessage();
    }
}