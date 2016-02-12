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
require_once("../database/database_operations.php");





function update_info($root){
    global $db;
    $info_files = scan_info_files($root);
    $photo_files = generate_photo_files($info_files);
    $thumbnail_files = generate_thumbnail_files($info_files);
    if (!$info_files)
        return false;
    $photoInfoObj = new PhotoInfo("");
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
//        var_dump($photoInfoObj->get_info());
        $title = $photoInfoObj->getTitle();
        $photographer = $photoInfoObj->getPhotographer();
        $model = $photoInfoObj->getModel();
        $timestamp = $photoInfoObj->getTime();
        $location = $photoInfoObj->getLocation();
        $tag_list = $photoInfoObj->getTags();
        $ratio = $photoInfoObj->getRatio();
        $filename = $photoInfoObj->getFilename();
        $thumbnail_filename = $photoInfoObj->getThumbnailFilename();

        // 增加模特信息
        $model_id = insert_model($model);
        // tag 信息
        $tag_id_list = array();
        foreach($tag_list as $tag){
            $tag_id_list[] = insert_tag($tag);
        }
        // 添加照片信息到数据库
        insert_photo($filename, $thumbnail_filename, $model_id, $title,
            $photographer, $model, $timestamp, $location, $ratio, $tag_id_list);

    }
}

update_info("../../photos");

