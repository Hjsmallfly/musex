<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午2:56
 * 按照title获取照片
 */

/*
 * 方法: get
 * 参数:
 * title 照片的标题
 */

// 检查参数
if(!isset($_GET["title"])){
    $error_string = "title needed";
    echo json_encode(array("ERROR"=>$error_string));
    return;
}

require_once("../database/database_operations.php");
// 执行函数
get_photos_by_title($_GET["title"]);

function get_photos_by_title($title){
    $title = trim($title);
    if (empty($title)){
        echo json_encode(["ERROR"=>"title can't be empty"]);
        return;
    }
    global $db;
    $stmt = $db->prepare("SELECT * FROM Photos WHERE title=:title ORDER BY moment DESC");
    $stmt->bindParam(":title", $title);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result){
            echo json_encode($result);
            return;
        }else{
            $error_string = "no photos of this title founded";
            echo json_encode(["ERROR"=>$error_string]);
            return;
        }
    }catch (PDOException $e){
        error_log("ERROR WHILE get photos by title: " . $e->getMessage());
        echo $e->getMessage();
    }
}