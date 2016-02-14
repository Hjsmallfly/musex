<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午2:44
 * 获取关于图片的评论
 */

/*
 * 方法:get
 * 参数:
 * photo_id 照片的主键
 */

// 参数检查
if (!isset($_GET["photo_id"])){
    echo json_encode(["ERROR"=>"parameter photo_id needed"]);
    return;
}

require_once("../database/db_creator.php");
get_comments($_GET["photo_id"]);

function get_comments($photo_id){
    if ($photo_id <= 0){
        echo json_encode(["ERROR"=>"photo_id must be grater than zero"]);
        return;
    }
    global $db;
    $stmt = $db->prepare("SELECT * FROM Comments WHERE photo_id=:pid");
    $stmt->bindParam(":pid", $photo_id, PDO::PARAM_INT);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result)
            echo json_encode($result, JSON_UNESCAPED_SLASHES);
        else
            echo json_encode(["ERROR"=>"no comments"]);
    }catch (PDOException $e){
        error_log("ERROR WHILE get latest_photos: " . $e->getMessage());
        echo $e->getMessage();
    }
}