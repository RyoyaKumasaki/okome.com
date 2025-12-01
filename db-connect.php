<?php
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607615-okome';
const USER = 'LAA1607615';
const PASS = 'okome';
$connect = 'mysql:host='. SERVER . ';dbname='. DBNAME . ';charset=utf8';
$pdo = new PDO($connect, USER, PASS);