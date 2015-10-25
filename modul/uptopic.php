<?php
lee::lib_load("plugIn/img");
$myid=data_use::get_usr('userid');
$pics=data_use::register_static_get('pic_'.$myid);
$result=img_mak::upload_pic($pics);
echo json_encode($result);