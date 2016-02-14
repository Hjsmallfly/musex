<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 上午12:50
 */

require_once("db_creator.php");
require_once("dbConfig.php");
if ($db){
    try{
        foreach($ALL_TABLES as $table){
            $db->exec($table);
        }
        echo "数据库建立完毕" . "<br>";
//        $db = null;
    }catch (PDOException $e){
        echo json_encode(array("ERROR" => $e->getMessage()));
        error_log($e->getMessage());
    }
}