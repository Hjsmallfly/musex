<?php
/**
 * Created by PhpStorm.
 * User: smallfly
 * Date: 16-2-12
 * Time: 上午12:26
 * 数据库设置
 */

define("DBNAME", "musex");
define("USERNAME", "smallfly");
define("PASSWORD", "hjsmallfly0806");

$photo_table_sql = "
    CREATE TABLE IF NOT EXISTS Photos(
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      model_id INT,
      path VARCHAR(256) CHARACTER SET utf8,
      title VARCHAR(40) CHARACTER SET utf8,
      photographer VARCHAR(20) CHARACTER SET utf8,
      model VARCHAR(20) CHARACTER SET utf8,
      moment TIMESTAMP DEFAULT 0,
      location VARCHAR(40) CHARACTER SET utf8,
      tags VARCHAR(140) CHARACTER SET utf8,
      ratio VARCHAR(10) CHARACTER SET utf8
    )
";

$model_table_sql = "
    CREATE TABLE IF NOT EXISTS Models(
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(20) CHARACTER SET utf8,
      wechat VARCHAR(20) CHARACTER SET utf8,
      qq VARCHAR(20) CHARACTER SET utf8,
      weibo VARCHAR(20) CHARACTER SET utf8,
      gender VARCHAR(6) CHARACTER SET utf8,
      birthday TIMESTAMP DEFAULT 0
    )
";
