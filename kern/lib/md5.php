<?php
function md($usr,$psw) {
	$val=$usr.$psw;
	return substr(md5($val),5,25);
}

//散列加密类别
function conss($kind){
    $k = array(
        2001    =>  14697328665,
        3001    =>  85216304726,
        4001    =>  49341205739,
        5001    =>  33487217345
    );
    $result=($k[$kind]+data_use::get_usr('userid'))*2;
}

//解密散列类别
function consr($kind){
    $k = array(
        14697328665    =>  2001,
        85216304726    =>  3001,
        49341205739    =>  4001,
        33487217345    =>  5001
    );
    $ku=$kind/2-data_use::get_usr('userid');
    $result=$k[$ku];
}