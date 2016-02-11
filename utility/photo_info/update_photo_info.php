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

function update_info($root){
    global $db;
    $info_files = scan_info_files($root);
    if (!$info_files)
        return false;
    $photoInfoObj = new PhotoInfo("");
//    $stmt = $db->prepare("INSERT INTO Photos ()")
//    var_dump($info_files);
    foreach($info_files as $info){
        $jsonStr = file_get_contents($info, FILE_TEXT);
//        var_dump($jsonStr);
        $photoInfoObj->decode_json_info($jsonStr);
        var_dump($photoInfoObj->get_info());
    }
}

update_info("../../photos");


