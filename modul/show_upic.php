<?php
lee::lib_load("plugIn/img");
if(!data_use::get_usr('userid')){
    die('请登录后再上传图片！');
}
$type=$_GET['type'];
$pic='mypic';
$result=img_mak::show_upload_pic($pic);
$pic_name=$result[pic_name];
$myid=data_use::get_usr('userid');
data_use::register_static_set('pic_'.$myid, $pic_name);
if(1==$type){
    echo 1;
    return;
}
echo json_encode($result);
