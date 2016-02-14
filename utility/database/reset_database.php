<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-14
 * Time: 下午8:15
 */

require_once("db_creator.php");

echo "DROPPING musex DATABASE" . "<br>";
$db->exec("DROP DATABASE musex");
echo "CREATING musex DATABASE" . "<br>";
$db->exec("CREATE DATABASE musex;use musex");
require_once("create_tables.php");
require_once("../photo_info/update_photo_info.php");

$db = null;