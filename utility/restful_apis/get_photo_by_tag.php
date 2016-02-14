<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 上午11:22
 * 获取相应tag的所有的照片
 * 测试http://localhost:63342/hello_php/musex/utility/restful_apis/get_photo_by_tag.php?tag_name=classic
 */

require_once("../database/database_operations.php");

/*
 * 方法get
 * 参数:
 * tag_name
 */

if (!isset($_GET["tag_name"])){
    error_log("query parameter tag_name needed");
    echo json_encode(array("ERROR"=>"query parameter tag_name needed"));
    return;
}

// mysql搜索字符串默认是不区分大小写的
get_photo(trim($_GET["tag_name"]));

function get_photo($tag_name){
    $tag_id = find_tag($tag_name);
    if (!$tag_id){
        $error_str = "no tag_name " . $tag_name . " found";
        error_log($error_str);
        echo json_encode(array("ERROR"=>$error_str));
        return;
    }
    $json_str = get_photo_by_tag($tag_id);
    echo $json_str;
}