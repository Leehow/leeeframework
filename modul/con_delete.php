<?php
lee::lib_load("usr");
lee::lib_load("cont");
$conid	 =$_POST['content_id'];

class con_del extends content {}


echo con_del::delete($conid);
