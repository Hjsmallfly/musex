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
                      $title, $photographer, $model_name, $moment, $location, $ratio, $tag_id_list, $tag_str){
    global $db;
    $p_id = find_photo($filename);
    if ($p_id)
        return $p_id;
    // TIMESTAMP 是个字符串类型来着,所以如果传入的数据是时间戳的话,需要加上 FROM_UNIXTIME(:time_var)
    $stmt = $db->prepare("INSERT INTO Photos (filename, thumbnail_file, model_id,
                        title, photographer, model, moment, location, ratio, tags) VALUES(:filename,
                        :thumbnail_file, :model_id, :title, :photographer, :model, FROM_UNIXTIME(:moment),
                        :location, :ratio, :tags)");

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
    $stmt->bindParam(":tags", $tag_str);

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

function find_user($username){
    global $db;
    $stmt = $db->prepare("SELECT id FROM Users WHERE username=:username LIMIT 1");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result)
        return intval($result["id"]);
    return false;
}

function insert_user($username, $nickname=null, $password_hash=null, $phone=null, $email=null){
    global $db;
    $user_id = find_user($username);
    if ($user_id)
        return $user_id;
    $stmt = $db->prepare("INSERT INTO Users (username, nickname, password_hash, phone, email)
                                    VALUES (:username, :nickname, :password_hash, :phone, :email)");
    try {
        $stmt->execute([$username, $nickname, $password_hash, $phone, $email]);
        return $db->lastInsertId();
    }catch (PDOException $e){
        error_log("ERROR while insert user: " . $e->getMessage());
        return false;
    }

}

function get_photo_favour($p_id){
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) AS favour_count FROM PhotoFavours WHERE photo_id=:p_id ");
    $stmt->execute([$p_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($count)
        return $count["favour_count"];
    return 0;
}

function favour_photo($p_id, $username, $token=null){
    // 以后添加不允许重复投票的机制
    // ------------------------
    global $db;
    $u_id = insert_user($username);
    $stmt = $db->prepare("INSERT INTO PhotoFavours (photo_id, user_id) VALUES (:p_id, :u_id)");
    try{
        $stmt->execute([$p_id, $u_id]);
        // 返回赞的数量
        $favour_count = get_photo_favour($p_id);
        // 更新Photos里面的赞数量
        $db->exec("UPDATE Photos SET favour_count=$favour_count WHERE id=$p_id");
        return $favour_count;

    }catch (PDOException $e){
        error_log("ERROR while favour: " . $e->getMessage());
        return false;
    }

}

function get_photo_by_tag($tag_id){
    global $db;
    $stmt = $db->prepare("SELECT * FROM Photos WHERE id IN (SELECT photo_id FROM Photo_Tag_Associ WHERE tag_id=:tag_id);");
    $stmt->bindParam(":tag_id", $tag_id, PDO::PARAM_INT);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }catch (PDOException $e){
        error_log("ERROR WHILE get photo_by_tags: " . $e->getMessage());
        return json_encode(array("ERROR"=>"ERROR WHILE get photo_by_tags: " . $e->getMessage()));
    }
}
