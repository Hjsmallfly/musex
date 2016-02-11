<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-11
 * Time: 下午10:56
 * 用于获取图片目录下的文件信息
 */

define("INFO_EXTENSION", "inf");
define("PHOTO_EXTENSION", "jpg");
define("THUMBNAIL_EXTENSION", "mini");

/*
 * 文件名实例
 * p1364.inf
 * p1364.jpg
 * p1364.mini.jpg
 */

/**
 * 获取所有描述文件, 按照创造时间排序,最新的在前面
 * @param string $root
 * @param string $info_extension
 * @return bool | array
 */
function scan_info_files($root, $info_extension=INFO_EXTENSION){
    if (!file_exists($root) || !is_dir($root))
        return false;
    $info_files = array();
    foreach(scandir($root) as $file){
        $suffix = pathinfo($file, PATHINFO_EXTENSION);
        if ($suffix == $info_extension){
            $filename = $root . DIRECTORY_SEPARATOR . $file;
            $info_files[$filename] = filectime($filename);
        }
    }
    // 按照value排序
    arsort($info_files);
    $info_files = array_keys($info_files);
    return $info_files ? $info_files : false;
}

function generate_photo_files($info_files, $info_extension=INFO_EXTENSION, $photo_ext=PHOTO_EXTENSION){
    $photo_files = array();
    foreach($info_files as $file){
        $photo_files[] = str_replace($info_extension, $photo_ext, $file);
    }
    return $photo_files;
}

function generate_thumbnail_files($info_files, $info_extension=INFO_EXTENSION,
                                  $thumbnail_ext=THUMBNAIL_EXTENSION, $photo_ext=PHOTO_EXTENSION){
    $thumbnail_files = array();
    foreach($info_files as $file){
        $tmp = str_replace($info_extension, $photo_ext, $file);
        $basename = pathinfo($file, PATHINFO_FILENAME);
        $thumbnail_files[] = str_replace($basename, $basename . "." . $thumbnail_ext, $tmp);
    }
    return $thumbnail_files;
}

//$info_files = scan_info_files("../photos");
//$photo_files = generate_photo_files($info_files);
//$thumbnail_files = generate_thumbnail_files($info_files);
////var_dump($photo_files);
//var_dump($thumbnail_files);