<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-11
 * Time: 下午11:19
 * 用于更新图片数据
 */

require_once("photo_file_scanner.php");
require_once("PhotoInfo.class.php");
require_once("../database/db_creator.php");

$photo_table_sql = "
    CREATE TABLE IF NOT EXISTS Photos(
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      model_id INT NOT NULL,  # 外键
      filename VARCHAR(128) CHARACTER SET utf8 NOT NULL UNIQUE,  # 不应该重复 VARCHAR 字段长度貌似有些限制
      thumbnail_file VARCHAR(128) CHARACTER SET utf8 NOT NULL UNIQUE, # 缩略图文件
      title VARCHAR(40) CHARACTER SET utf8,
      photographer VARCHAR(20) CHARACTER SET utf8,  # 以名字区分
      model VARCHAR(20) CHARACTER SET utf8,         # 模特名
      moment TIMESTAMP DEFAULT 0,                   # 照片拍摄日期
      location VARCHAR(40) CHARACTER SET utf8,
      favour_count INT DEFAULT 0,                   # 点赞数
      ratio VARCHAR(10) CHARACTER SET utf8,          # 照片的分辨率
      # model_id 是外键
      FOREIGN KEY (model_id) REFERENCES Models(id)
    )
";

function find_tag($name){
    global $db;
    $stmt = $db->prepare("SELECT id FROM Tags WHERE tag=:tag LIMIT 1");
    $stmt->bindParam(":tag", $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result)
        return intval($result["id"]);
    return false;
}

function insert_tag($name){
    $tag_id = find_tag($name);
    if ($tag_id)
        return intval($tag_id);
    global $db;
    $stmt = $db->prepare("INSERT INTO Tags (tag) VALUES(:tag)");
    $stmt->bindParam(":tag", $name);
    try{
        $stmt->execute();
        return $db->lastInsertId();
    }catch (PDOException $e){
        error_log("ERROR while insert tag: " . $e->getMessage());
        return false;
    }
}

function find_model($name){
    global $db;
    $stmt = $db->prepare("SELECT id FROM Models WHERE name=:name LIMIT 1");
    $stmt->bindParam(":name", $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result)
        return intval($result["id"]);
    return false;
//    var_dump($result);
}

function insert_model($name, $wechat=null, $qq=null, $weibo=null, $gender="female", $birthday=0){
    global $db;
    $model_id = find_model($name);
    if ($model_id)
        // 说明模特已经在数据库中了
        return $model_id;
    $stmt = $db->prepare("INSERT INTO Models (name, wechat, qq, weibo, gender, birthday)
                                      VALUES(:name, :wechat, :qq, :weibo, :gender, :b_day)");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":wechat", $wechat);
    $stmt->bindParam(":qq", $qq);
    $stmt->bindParam(":weibo", $weibo);
    $stmt->bindParam(":gender", $gender);
    $stmt->bindParam(":b_day", $birthday, PDO::PARAM_INT);
    try{
        $stmt->execute();
        return $db->lastInsertId();
    }catch (PDOException $e){
        error_log("ERROR while insert model: " . $e->getMessage());
        return false;
    }


}


function update_info($root){
    global $db;
    $info_files = scan_info_files($root);
    $photo_files = generate_photo_files($info_files);
    $thumbnail_files = generate_thumbnail_files($info_files);
    if (!$info_files)
        return false;
    $photoInfoObj = new PhotoInfo("");
    $title = "";
    $photographer = "";
    $model = "";
    $timestamp = 0;
    $location = "";
    $tag_list = array();
    $ratio = "";
//    $stmt = $db->prepare("INSERT INTO Photos ()")
    $i = 0 ;
    foreach($info_files as $info){
        $jsonStr = file_get_contents($info, FILE_TEXT);
        // 往原始的json数据中添加新的信息
        if(substr($jsonStr, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
            $jsonStr = substr($jsonStr, 3);
        }
        $filename = $photo_files[$i];
        $thumbnail_filename = $thumbnail_files[$i];
        ++$i;
        $json_obj = json_decode($jsonStr, true);
        $json_obj["filename"] = pathinfo($filename, PATHINFO_BASENAME);
        $json_obj["thumbnail_filename"] = pathinfo($thumbnail_filename, PATHINFO_BASENAME);
        // 往原始的json数据中添加新的信息
        $photoInfoObj->decode_json_info(json_encode($json_obj));
        var_dump($photoInfoObj->get_info());
        $title = $photoInfoObj->getTitle();
        $photographer = $photoInfoObj->getPhotographer();
        $model = $photoInfoObj->getModel();
        $timestamp = $photoInfoObj->getTime();
        $location = $photoInfoObj->getLocation();
        $tag_list = $photoInfoObj->getTags();
        $ratio = $photoInfoObj->getRatio();

        // 增加模特信息
        $model_id = insert_model($model);
        // tag 信息
        $tag_id_list = array();
        foreach($tag_list as $tag){
            $tag_id_list[] = insert_tag($tag);
        }

        // 添加照片信息到数据库
//        $stmt = $db->prepare()


    }
}

update_info("../../photos");

