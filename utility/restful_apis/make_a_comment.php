<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午2:13
 * 给照片添加评论
 */

/*
 * 方法: post
 * 参数:
 * photo_id 照片的id
 * username 用户名
 * content  评论内容
 * 测试:curl "http://localhost:63342/hello_php/musex/utility/restful_apis/make_a_comment.php" -d "photo_id=1&content=helloworld"
 */

define("CONTENT_MAX_LENGTH", 140);

// 参数检查
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST["photo_id"]) /*|| !isset($_POST["username"])*/ || !isset($_POST["content"])){
    echo json_encode(["ERROR"=>"method post, parameters: photo_id, content"]);
    return;
}

//require_once("../database/db_creator.php");
require_once("../database/database_operations.php");

make_a_comment($_POST["photo_id"], $_SERVER["REMOTE_ADDR"], $_POST["content"]);

function make_a_comment($pid, $name, $content){
    $content = trim($content);
    if (strlen($content) > CONTENT_MAX_LENGTH){
        echo json_encode(["content too long(max 140)"]);
        return;
    }

    global $db;
    try {
        $uid = insert_user($name);
        $stmt = $db->prepare("INSERT INTO Comments (user_id, photo_id, content) VALUES(:user_id, :photo_id, :content)");
        $stmt->bindParam(":user_id", $uid, PDO::PARAM_INT);
        $stmt->bindParam(":photo_id", $pid, PDO::PARAM_INT);
        $stmt->bindParam(":content", $content);
        $stmt->execute();
        echo json_encode(["status"=>"OK"]);
    }catch (PDOException $e){
        error_log("ERROR while favour: " . $e->getMessage());
        echo json_encode(["ERROR"=>$e->getMessage()]);
//        return false;
    }
}