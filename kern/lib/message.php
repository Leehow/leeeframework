<?php
class message {
	static function ret() {
		$m=new message;
	}
	var $numbers_error=array(
			110=>"用户名找不到",
			111=>"用户名重复",
			112=>"用户名为空",
			120=>"密码错误",
			121=>"密码为空",
			125=>"两次密码不一致",
			130=>"用户未登录",
			135=>"用户登录错误，请重新登录",
			140=>"内容为空",
			141=>"内容已存在",
                        311=>"内容已存在"
			
			);
			
	var $numbers_right=array(
			510=>"成功！",
			511=>"成功！3秒后自动跳转"
	);
			
	private $numbers_ext=array();
}


class error extends message {
	static function show($number) {
		//$back="<a href='javascript:history.back(-1)'>返回</a>";
		$m=new error();
		if (1!=$number) {
		if (!empty($m->numbers_error[$number]))
		$show=$m->numbers_error[$number];
		else
		$show=$m->numbers_ext[$number];
		die($show.$back);
		}
	}
}

//success::show(511, "注册");
class success extends message {
	static function show($number,$msg) {
		$back="<meta http-equiv='refresh' content='3;url=http://".$_SERVER['HTTP_HOST']."'>";
		$m=new error;
		if (!empty($m->$numbers_right[$number]))
		$show=$m->$numbers_right[$number];
		else
		$show=$m->numbers_ext[$number];
		echo "<h1>".$msg.$show."</h1>".$back;
	}
}
