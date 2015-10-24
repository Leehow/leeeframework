<?php
//对数据表的columns做定义
define("TABLE"     ,"data" );
define("ID"        ,"data_id" );
define("UPID"      ,"data_upid" );
define("AUTHOR"    ,"data_author" );
define("KIND"      ,"data_kind" );
define("CONTENT"   ,"data_content" );
define("TIME"      ,"data_time" );
define("CREATETIME","data_createtime" );
define("CHANGETIME","data_changetime" );

//类别的定义
class get_kind{
    //定义用户属性的参数
    static $kind_user    = array(
                        usr_name,
                        usr_ip,
                        usr_gender,     //性别
                        usr_local,      //地点
                        usr_tel,        //电话
                        usr_qq,         //QQ
                        usr_email,      //email
                        usr_pic,        //头像
                        usr_msg,        //消息数量
                        usr_msg_tap,    //消息id
                        usr_msg_hide    //过期消息id
                        );
    //初始化用户数据
    static function ini_user($userid){
        $create_user    = array(
                        usr_name	=> "用户_".$userid,
                        usr_ip          => $_SERVER["REMOTE_ADDR"],
                        usr_gender	=> 0,    //性别
                        usr_local	=> 0,    //地点
                        usr_tel         => 0,    //电话
                        usr_qq          => 0,    //QQ
                        usr_email	=> 0,    //QQ
                        usr_pic         => 0,     //pic
                        usr_msg         => 0     //消息
                        );
        return $create_user;
    }
    
    //需要在content中加入的用户参数
    static $kind_user_con = array(
                        usr_name	,
                        usr_ip          ,
                        usr_gender	,    //性别
                        usr_local	,    //地点
                        usr_tel         ,    //电话
                        usr_qq          ,    //QQ
                        usr_email       ,    //email
                        usr_pic              //头像
                        );
    
}