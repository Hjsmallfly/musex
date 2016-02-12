<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 下午2:21
 */

require_once("db_creator.php");

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

function find_photo($filename){
    global $db;
    $stmt = $db->prepare("SELECT id FROM Photos WHERE filename=:filename LIMIT 1");
    $stmt->bindParam(":filename", $filename);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result)
        return intval($result["id"]);
    return false;
}

function insert_photo($filename, $thumbnail_filename, $model_id,
                      $title, $photographer, $model_name, $moment, $location, $ratio, $tag_id_list){
    global $db;
    $p_id = find_photo($filename);
    if ($p_id)
        return $p_id;
    // TIMESTAMP 是个字符串类型来着,所以如果传入的数据是时间戳的话,需要加上 FROM_UNIXTIME(:time_var)
    $stmt = $db->prepare("INSERT INTO Photos (filename, thumbnail_file, model_id,
                        title, photographer, model, moment, location, ratio) VALUES(:filename,
                        :thumbnail_file, :model_id, :title, :photographer, :model, FROM_UNIXTIME(:moment),
                        :location, :ratio)");

    $stmt->bindParam(":filename", $filename);
    $stmt->bindParam(":thumbnail_file", $thumbnail_filename);
    $stmt->bindParam(":model_id", $model_id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":photographer", $photographer);
    $stmt->bindParam(":model", $model_name);
    $stmt->bindParam(":moment", $moment, PDO::PARAM_INT);
//    error_log($moment);
    $stmt->bindParam(":location", $location);
    $stmt->bindParam(":ratio", $ratio);

    try{
        $stmt->execute();
        $p_id = $db->lastInsertId();
        // 建立和tags的多对多关系
        $stmt = $db->prepare("INSERT INTO Photo_Tag_Associ (photo_id, tag_id) VALUES(:p_id, :tag_id)");
        $tag_id = 0;
        $stmt->bindParam(":p_id", $p_id);
        $stmt->bindParam(":tag_id", $tag_id);
        foreach($tag_id_list as $tag_id){
            $stmt->execute();
        }
        return $p_id;
    }catch (PDOException $e){
        error_log("ERROR while insert photo: "  . $e->getMessage());
        return false;
    }

}