<?php

lee::lib_load("cont");

$content    = $_POST['content'];     //商品名称
$kind       = "feedback";
if(!$content){
    die("对不起！您必须输入有效内容！");
}
$result = content::insert($content, $kind);
if(is_numeric($result)){
    $result=1;
}
else{
    $result=0;
}
echo $result;