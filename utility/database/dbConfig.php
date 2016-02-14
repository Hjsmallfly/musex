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
      model_id INT NOT NULL,  # 外键
      filename VARCHAR(128) CHARACTER SET utf8 NOT NULL UNIQUE,  # 不应该重复 VARCHAR 字段长度貌似有些限制
      thumbnail_file VARCHAR(128) CHARACTER SET utf8 NOT NULL UNIQUE, # 缩略图文件
      title VARCHAR(40) CHARACTER SET utf8,
      photographer VARCHAR(20) CHARACTER SET utf8,  # 以名字区分
      model VARCHAR(20) CHARACTER SET utf8,         # 模特名
      moment TIMESTAMP DEFAULT 0,                   # 照片拍摄日期
      location VARCHAR(40) CHARACTER SET utf8,
      favour_count INT DEFAULT 0,                   # 点赞数
      ratio VARCHAR(10) CHARACTER SET utf8,         # 照片的分辨率
      tags VARCHAR(50) CHARACTER SET utf8,          # 照片的tags, 用于方便提取照片的tag, 用+号分割
      # model_id 是外键
      FOREIGN KEY (model_id) REFERENCES Models(id)
    )
";

$model_table_sql = "
    CREATE TABLE IF NOT EXISTS Models(
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(20) CHARACTER SET utf8 UNIQUE,
      favour_count INT DEFAULT 0, # 赞的数量
      wechat VARCHAR(20) CHARACTER SET utf8,
      qq VARCHAR(20) CHARACTER SET utf8,
      weibo VARCHAR(20) CHARACTER SET utf8,
      gender VARCHAR(6) CHARACTER SET utf8,
      birthday TIMESTAMP DEFAULT 0
    )
";

$tag_table_sql = "
  CREATE TABLE IF NOT EXISTS Tags(  # tag 与照片是多对多的关系
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    tag VARCHAR(20) CHARACTER SET utf8 UNIQUE  # tag的名称
  )
";

$photo_tag_association_table_sql = "
  CREATE TABLE IF NOT EXISTS Photo_Tag_Associ(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    photo_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (photo_id) REFERENCES Photos(id),
    FOREIGN KEY (tag_id) REFERENCES Tags(id)
  )
";

$comment_table_sql = "
  CREATE TABLE IF NOT EXISTS Comments(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL, # 外键
    photo_id INT NOT NULL, # 外键
    content VARCHAR(140) CHARACTER SET utf8 NOT NULL,
    comment_time TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (photo_id) REFERENCES Photos(id)
  )
";

$favour_photo_table_sql = "
  CREATE TABLE IF NOT EXISTS PhotoFavours(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL, # 外键
    photo_id INT NOT NULL, # 外键
    favour_time TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (photo_id) REFERENCES Photos(id)
  )
";

$favour_model_table_sql = "
  CREATE TABLE IF NOT EXISTS ModelFavours(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL, # 外键
    model_id INT NOT NULL, # 外键
    favour_time TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (model_id) REFERENCES Models(id)
  )
";

$user_table_sql = "
    CREATE TABLE IF NOT EXISTS Users(
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      username VARCHAR(20) CHARACTER SET utf8 NOT NULL UNIQUE ,
      nickname VARCHAR(20) CHARACTER SET utf8,
      password_hash VARCHAR(120) CHARACTER SET utf8,
      phone VARCHAR(20) CHARACTER SET utf8,
      email VARCHAR(50) CHARACTER SET utf8,
      valid BOOLEAN DEFAULT TRUE  # 用于封号之类的
    )
";

$ALL_TABLES = [
    $model_table_sql,
    $photo_table_sql,
    $user_table_sql,
    $tag_table_sql,
    $comment_table_sql,
    $favour_photo_table_sql,
    $favour_model_table_sql,
    $photo_tag_association_table_sql
];