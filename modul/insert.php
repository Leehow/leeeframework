<?php

//lee::lib_load("usr");
lee::lib_load("cont");

$content    = $_POST['content'];     //内容
$plus       = $_POST['plus'];     //

//之所以要手敲出来这些是因为要考虑到安全性啊！别瞎改！
$content    = array(
    con_content     => $content,
    con_pic         => $pic,
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
