<?php
/******************************************************************
*
##  Project:LeePHP,A concise and easy framework for PHP
##  Copyright: 2014 All rights reserved
##  version: 0.10.1
##  Author: leehow <leehow2005@126.com>
*
##  File: LeeFramework.php
*
******************************************************************/
class lee {

	
	static function get_ini($updir="./"){
		require_once $updir.'kern/core/class_load.php';
		self::class_load( "plugIn/","config",$updir );
		class_load::get_depend($updir);
	}
	
	//直接获取类文件
	static function class_load($downdir, $class, $updir="./") {
		class_load::load($updir, $downdir, $class);
	}
	
	//获取模块
	static function modul_load($class, $updir="./") {
		self::class_load( class_load::modul_dir, $class, $updir);
	}
	
	//获取组件包
	static function lib_load($class, $updir="./") {
		self::class_load(class_load::lib_dir, $class, $updir);
	}
	
        //获取模板文件（不常用）
	static function template_load($class, $updir="./"){
		require_once $updir.class_load::template_dir.$class;
	}
	
	
	//--------------------------------------------载入文件
	
}
