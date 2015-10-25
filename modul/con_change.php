<?php

lee::lib_load("cont");
$conid	 =$_GET['conid'];
$content =$_GET['con'];

class con_chg extends content {}


con_chg::change($conid,$content);
echo 1;