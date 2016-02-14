<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午3:04
 */


/*
 * 方法: get
 * 参数:
 * year 年份
 */

// 检查参数
if(!isset($_GET["year"])){
    $error_string = "year needed";
    echo json_encode(array("ERROR"=>$error_string));
    return;
}

require_once("../database/database_operations.php");
// 执行函数
get_photos_by_year($_GET["year"]);

function get_photos_by_year($year){
    if (!is_numeric(($year))){
        echo json_encode(["ERROR"=>"year must be int"]);
        return;
    }
    global $db;
    // 貌似用 YEAR() 的话会影响index的使用, 所以如果性能出现瓶颈可以考虑试试 WHERE moment > "2015-01-01" 之类的语句
    $stmt = $db->prepare("SELECT * FROM Photos WHERE YEAR(moment)=:year ORDER BY moment DESC");
    $stmt->bindParam(":year", $year);
    try{
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result){
            echo json_encode($result);
            return;
        }else{
            $error_string = "no photos of this year founded";
            echo json_encode(["ERROR"=>$error_string]);
            return;
        }
    }catch (PDOException $e){
        error_log("ERROR WHILE get photos by year: " . $e->getMessage());
        echo $e->getMessage();
    }
}