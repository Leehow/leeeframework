<?php
lee::class_load( class_load::lib_dir, "md5", $updir);
lee::lib_load("usr",$updir);

class login extends usr{}
$usr=$_POST['usr'];
$psw=$_POST['psw'];
if(empty($usr) || empty($psw)){
    die('对不起！注册信息缺一不可！');
}
$npsw=md($usr,$psw);
$result=  login::login($usr, $npsw);
echo json_encode($result);
?>