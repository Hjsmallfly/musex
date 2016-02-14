<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 上午11:48
 * 获取最热的count张照片
 */

/*
 * 方法get
 * 参数:
 * count 返回排名数据, -1 表示获取所有数据, 默认为前10
 */

// 参数检查
if(!isset($_GET["count"])){
    $error_string = "top_ranking_photos: parameter count needed";
    echo json_encode(array("ERROR"=>$error_string));
    error_log($error_string);
    return;
}

require_once("../database/db_creator.php");

// 调用函数
get_ranking($_GET["count"]);

function get_ranking($count){
    global $db;
    if ($count == 0){
        echo json_encode(array("ERROR"=>"count can't be zero"));
        return;
    }elseif($count > 0) {
        $stmt = $db->prepare("SELECT * FROM Photos ORDER  BY favour_count DESC LIMIT :count");
        $stmt->bindParam(":count", $count, PDO::PARAM_INT);
    }else   // 获取全部照片,按照[赞]的数量
        $stmt = $db->prepare("SELECT * FROM Photos ORDER  BY favour_count DESC");

    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result)
            echo json_encode($result);
        else{
            echo json_encode(array("ERROR"=>"no photos found"));
        }
    }catch (PDOException $e){
        error_log("ERROR WHILE get top_ranking photos: " . $e->getMessage());
        echo $e->getMessage();
    }

}
