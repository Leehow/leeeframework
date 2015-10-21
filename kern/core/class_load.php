<?php
/*
 * class_load::load($updir,$downdir,$class);		就这一个函数载入类
 * 
 * 扩展、重写方法见底部
 */

class class_load {
	const core_dir="kern/core/";
	const lib_dir="kern/lib/";
	const template_dir="templates/";
	const modul_dir="modul/";
	
	//载入目录
	static function load($updir="./",$downdir="./",$class) {
                if(!$updir)
                    $updir="./";
		$dir=$updir.$downdir;
		$p=".php";
//		echo $dir.$class.$p;
		require_once $dir.$class.$p;
	}
	
	static function get_depend($updir="./") {
		self::load($updir, self::core_dir, "sql_use");
                self::load($updir, self::core_dir, "sql_use_new");      //这个载入针对性写的sql_use类
		self::load($updir, self::core_dir, "data_use");
	}
	
}


























//其实没有扩展重写方法，我是骗你的


