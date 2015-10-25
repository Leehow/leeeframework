<?php

$allow   =$_POST['allow'];
if($allow!="super geil"){
    die('对不起！邀请码错误！sorry! Invitation code error!');
}

lee::class_load(class_load::lib_dir, "md5",$updir);
lee::lib_load("usr",$updir);
$usr	 =$_POST['usr'];
$psw	 =$_POST['psw'];
$rpsw	 =$_POST['rpsw'];
if(empty($usr) || empty($psw)){
    die('对不起！注册信息缺一不可!Sorry! Registration indispensable!');
}
if($psw!=$rpsw){
    die("两次密码不一样！Not the same password twice！");
}

//先录入数据库
$npsw=md($usr,$psw);
class regin extends usr{}
$userid=  regin::register($usr, $npsw);
if(!is_numeric($userid)){
    echo $userid;
    exit();
}
class regmsg extends user_manage {
    static function register($userid,$usr){ //前面的参数的用户的id,后面的参数是用户的email地址
        self::create($userid);
        self::change_admin($userid,"usr_email",$usr);
        return 1;
    }
    
}

echo regmsg::register($userid,$usr);

