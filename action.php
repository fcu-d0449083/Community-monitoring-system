<?php //把index.php get到的數據傳送到webcamClass.php//
require_once 'webcamClass.php';
$webcamClass=new webcamClass();
if(isset($_GET['file_name'])){
    $webcamClass->setFileName($_GET['file_name']);
}
echo $webcamClass->showImage();