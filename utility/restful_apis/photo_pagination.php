<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午5:48
 * 用于获取分页数据
 */


/*
 * 方法: get
 * 参数:
 * page_num 第几页(默认第一页)
 * page_size 一页的数量(默认十张)
 */


// 设置默认参数
if (!isset($_GET["page_num"])){
    $page_num = 1;
}elseif (is_numeric(($_GET["page_num"]))){  // 验证表单必须要用 is_numeric
    $page_num = intval($_GET["page_num"]);
    if ($page_num <= 0){
        echo json_encode(array("ERROR"=>"Invalid value for page_num(>0)"));
        return;
    }
}else{
    echo json_encode(array("ERROR"=>"Invalid parameter"));
    return;
}


if (!isset($_GET["page_size"])){
    $page_size = 10;
}elseif (is_numeric(($_GET["page_size"]))){
    $page_size = intval($_GET["page_size"]);
    if ($page_size <= 0){
        echo json_encode(array("ERROR"=>"Invalid value for page_size(>0)"));
        return;
    }
}else{
    echo json_encode(array("ERROR"=>"Invalid parameter"));
    return;
}

require_once("../database/db_creator.php");
// 调用函数
get_pagination($page_num, $page_size);

function get_pagination($page_num, $page_size){
    global $db;
    // 注意两个地方都需要用相同的方式排序,不然可能造成结果的不一致
    $stmt = $db->prepare("SELECT * FROM (SELECT id FROM Photos ORDER BY moment DESC, id DESC LIMIT :page_size OFFSET :offset) AS lt INNER JOIN
                          Photos ON lt.id = Photos.id ORDER BY Photos.moment DESC, Photos.id DESC");
    // 计算offset, 数据库里面的offset是从0开始
    $offset = ( $page_num - 1 ) * $page_size;
    $stmt->bindParam(":page_size", $page_size, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result)
            echo json_encode($result, JSON_UNESCAPED_SLASHES);
        else
            echo json_encode(["ERROR"=>"page overflow"]);

    }catch (PDOException $e){
        error_log("ERROR WHILE pagination by year: " . $e->getMessage());
        echo $e->getMessage();
    }
}