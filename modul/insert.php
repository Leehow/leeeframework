<?php

//lee::lib_load("usr");
lee::lib_load("cont");

$content    = $_POST['content'];     //内容
$swf        = $_POST['con_swf'];     //swf视频
$ytb        = $_POST['con_ytb'];     //youtube视频
$tid        = $_POST['tid'];     //upid
$myid=data_use::get_usr('userid');
$pic=data_use::register_static_get('pic_'.$myid);
$audi=data_use::register_static_get('audi_'.$myid);
data_use::register_static_set('pic_'.$myid, null);
data_use::register_static_delete('pic_'.$myid);
data_use::register_static_set('audi_'.$myid, null);
data_use::register_static_delete('audi_'.$myid);
if(!$pic){
    $pic=0;
}
if(!$audi){
    $audi=0;
}
if(!$swf){
    $swf=0;
}
if(!$ytb){
    $ytb=0;
}
//之所以要手敲出来这些是因为要考虑到安全性啊！别瞎改！
$content    = array(
    con_content     => $content,
    con_pic         => $pic,
    con_audi        => $audi,
    con_swf        => $swf,
    con_ytb        => $ytb
    );

class con extends content {
    static function insert_con($content,$tid=null,$limitwords){
        $i=0;
        foreach ($content as $key=>$value){
            if(0==$i){
                if(is_numeric($tid)){
                    $upid=$tid;
                }
                else{
                    $upid=null;
                }
                $result=self::insert($value, $key, $upid, $limitwords);
            }
            else{
                
                self::insert($value, $key, $result, $limitwords);
            }
            ++$i;
        }
        return $result;
    }
}
$result=con::insert_con($content,$tid,1000);
echo $result;
//print_r($result);
//echo con::insert_sudo($content,$author,$kindkey);
