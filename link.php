<?php
require './kern/LeeFramework.php';

$modul=$_GET[m];

$updir="./";
lee::get_ini();
lee::modul_load($modul);