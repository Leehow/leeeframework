<?php
//这也不是通用代码
$mail = new SaeMail();
lee::lib_load("md5",$updir);
$usr=$_POST['usr'];
$psw=$_POST['psw'];
$rpsw=$_POST['psw'];
if($psw!=$rpsw){
    die("两次密码不一致请重新输入");
}
$token=md($usr, $psw);
function randomkeys($length)
{
 $pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
 for($i=0;$i<$length;$i++)
 {
   $key .= $pattern{mt_rand(0,35)};    //生成php随机数
 }
 return $key;
}
$rand=randomkeys(20);
 
$msg="您好！请点击这里http://freetofind.sinaapp.com/link.php?m=change_psw&check=".$rand."&u=".$usr."&p=".$token."完成修改密码，本次密码修改来自IP".$_SERVER["REMOTE_ADDR"]."，若不是本人请举报！感谢您对自由寻的支持！";
data_use::register_static_set('getp_'.$usr,$rand);
$ret = $mail->quickSend( $usr , '你好！我是您忠臣的客服!' , $msg, 'email_username' , 'email_passowrd' );
 
    //发送失败时输出错误码和错误信息
    if ($ret === false)
        var_dump($mail->errno(), $mail->errmsg());
    else
        echo 1;
?>
