<?php
//非通用文件,需要时候重新考一遍
function md($usr,$psw) {
	$val=$usr.$psw;
	return substr(md5($val),24,44);
}


//新的加密规则,还没有设计好,所以还没有真正使用
//
//散列加密类别
function conss($kind){
    $k = array(
        2001    =>  90982340923,
        3001    =>  09856092847,
        4001    =>  90823480975,
        5001    =>  68304853452
    );
    $result=($k[$kind]+data_use::get_usr('userid'))*2;
    return $result;
}

//解密散列类别
function consr($kind){
    $k = array(
        90982340923    =>  2001,
        09856092847    =>  3001,
        90823480975    =>  4001,
        68304853452    =>  5001
    );
    $ku=$kind/2-data_use::get_usr('userid');
    $result=$k[$ku];
    return $result;
}