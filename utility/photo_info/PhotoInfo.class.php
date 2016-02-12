<?php

/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-11
 * Time: 下午10:31
 */

class PhotoInfo{
    private $title;
    private $photographer;
    private $model;
    private $time;
    private $location;
    private $tags;
    private $ratio;
    private $filename;
    private $thumbnail_filename;

    static $TAG_SEPARATOR = "+";

    public function __construct($json_str){
        if (!empty($json_str))
            $this->decode_json_info($json_str);
    }

    public function decode_json_info($json_str){
//        error_log($json_str);
        $json_str = $this->strip_UTF8_BOM($json_str);
        $info_obj = json_decode($json_str, true);
        if ($info_obj == null){
            error_log(json_last_error_msg());
        }
        $this->title = $info_obj["Title"];
        $this->photographer = $info_obj["Photographer"];
        $this->model = $info_obj["Model"];
        $this->time = $this->decode_time($info_obj["Time"]);
        $this->location = $info_obj["Location"];
        $this->tags = $this->decode_tags($info_obj["Tags"]);
        $this->ratio = $info_obj["Ratio"];
        $this->filename = $info_obj["filename"];
        $this->thumbnail_filename = $info_obj["thumbnail_filename"];
    }

    // json_decode 函数不能解码 WITH_BOM 的字符串
    private function strip_UTF8_BOM(&$content){
        if(substr($content, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
            $content = substr($content, 3);
        }
        return $content;
    }

    // getters

    public function getTitle(){return $this->title;}
    public function getPhotographer(){return $this->photographer;}
    public function getModel(){return $this->model;}
    public function getTime(){return $this->time;}
    public function getLocation(){return $this->location;}
    public function getTags(){return $this->tags;}
    public function getRatio(){return $this->ratio;}
    public function getFilename(){return $this->filename;}
    public function getThumbnailFilename(){return $this->thumbnail_filename;}
    // getters

    public function get_info(){
        return [$this->title, $this->photographer, $this->model, $this->time,
        $this->location, $this->tags, $this->ratio, $this->filename, $this->thumbnail_filename];
    }

    private function decode_time($time_str){
        return strtotime($time_str);
    }

    private function decode_tags($tags_str){
        return explode(PhotoInfo::$TAG_SEPARATOR, $tags_str);
    }

}

//$json_str = '{"Title":"Untitled","Photographer":"Max Kwok","Model":"Lv Xiaoying","Time":"2015-11-14","Location":"Rome Square of Changsha","Tags":"Modern","Ratio":"16x9"}';
//$photo_info = new PhotoInfo("");
//$photo_info->set_json($json_str);
//var_dump($photo_info->get_info());
//
//$json_str ='{"Title":"Rain","Photographer":"Max Kwok","Model":"Zhang Xin","Time":"2015-6-14","Location":"Hunan First Normal University","Tags":"Modern","Ratio":"16x9"}';
//$photo_info->set_json($json_str);
//var_dump($photo_info->get_info());