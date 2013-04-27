CREATE DATABASE `todo` ;
USE `todo`;
DROP TABLE IF EXISTS `todo`;
CREATE TABLE todo 
(
   `id`          bigint(11) primary key auto_increment,
   `created`     datetime,
   `modified`    datetime,
   `title`       varchar(512),
   `description` text,
   `status`      enum('PENDING','COMPLETED')
);
