<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 上午12:46
 * 创建数据库
 */

require_once("dbConfig.php");

try{
    $db = new PDO("mysql:host=localhost;dbname=" . DBNAME, USERNAME, PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $db;
}catch (PDOException $e){
    echo json_encode(array("ERROR" => $e->getMessage()));
    error_log($e->getMessage());
    $db = null;
    return false;
}