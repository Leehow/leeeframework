<?php
lee::lib_load("plugIn/audio");
if(!data_use::get_usr('userid')){
    die('请登录后再上传图片！');
}
$audio='upradio';
$result=audio_mak::upradio($audio);
$audi_name=$result[m_name];
$myid=data_use::get_usr('userid');
data_use::register_static_set('audi_'.$myid, $audi_name);
echo json_encode($result);
