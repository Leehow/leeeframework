<?php
/*
 * data_use::equal($value1,$value2)					判定两个数据是否相等
 * data_use::get($value1,$value2)					将后一个数据赋予前一变量
 * data_use::check_null($value)						验证变量是否为NULL
 * data_use::get_null($value)						将变量设为NULL
 * data_use::register($space,$value)				注册对象，将其存入session
 * data_use::register_get($space)					获取注册的对象
 * data_use::register_check_null($space)			验证注册对象是否为空
 * data_use::register_delet($space)					毁掉一个注册的对象，若不填写，则毁掉所有session
 */
class data_use {
	
	//设定memcache 如果不用memcache注册可以修改这个地方
	static function register_static_set($key,$value){
		$mmc=memcache_init();
	    if($mmc==false)
	    echo "那个坑爹的Memcache加载失败啦！笨蛋！\n";
	    else{
	        memcache_set($mmc,'tk_'.$key,$value);
	    }
	}
	
	//获取memcache
	static function register_static_get($key){
		$mmc=memcache_init();
	    if($mmc==false)
	    echo "那个坑爹的Memcache加载失败啦！笨蛋！\n";
	    else{
			if($out=memcache_get($mmc,'tk_'.$key))
			return $out;
			else
			return false;
	    }
	}
	
	
	//删除memcache
	static function register_static_delete($key){
		$mmc=memcache_init();
	    if($mmc==false)
	    echo "那个坑爹的Memcache加载失败啦！笨蛋！\n";
	    else{
		memcache_delete($mmc,$key);
                return 1;
	    }
	}
	
	
	//注册对象，将其存入cookie
	static function register($space,$value) {
		setcookie($space, $value, time()+1800);
	}
	
	//获取注册的对象
	static function register_get($space) {
            $out=$_GET[$space];
            $out2=$_COOKIE[$space];
            if($out)
		return $out;
            elseif($out2)
                return $out2;
            else
                return null;
	}
	
	//验证注册对象是否为空
	static function register_check_null($space) {
		if (NULL==self::register_get($space))
		return true;
		else 
		return false;
	}
	
	//毁掉一个注册的对象
	static function register_delet($space) {
		if (isset($space))
		setcookie($space,null, time()-1800);
	}
	
	
	//直接获取用户信息
	static function get_usr($name){
		return self::register_static_get($name.'_'.self::register_get('tkid'));
	}
	
	//将两个数据hash加密
	static function encryption($token,$con,$con2){
		return md5($token.$con).md5($token.$con2);
	}
	
	
	//从数组中格式化字段录入数据库
	static function format_columns($columns,$out=0) {
		if (is_array($columns))
		foreach ($columns as $c){
                    if($out==1){
                        if($c!=0)
                            $col=$col.",".$c;
                            }
                    else
                    $col=$col.",".$c;
                
                }
		return substr($col, 1);
	}
	
	//从数组中格式化值录入数据库
	static function format_values($values) {
		if (is_array($values))
		foreach ($values as $v) {
			if (is_numeric($v))
			$val=$val.",".$v;
			else 
			$val=$val.",'".$v."'";
		}
		return substr($val, 1);
	}
	
//        格式化后结果是xxx='sss',xxx='sss',xxx='sss',用于sql语句中update
	static function format_twoequl($values1,$values2){
		if (is_array($values1) && is_array($values2)){
			foreach ($values1 as $v1)
			$vv1[]=$v1;
			foreach ($values2 as $v2)
			$vv2[]=$v2;
                        for ($i=0;$i<count($vv1);$i++){
                                if (!self::check_null($vv1[$i]) && !self::check_null($vv2[$i]))
                                $val=$val.",$vv1[$i]='$vv2[$i]'";
                        }
                        return substr($val, 1);
                }
                else{
                    return $values1."='".$values2."'";
                }
	
	}
        
//	这个就是把数组连成字符串，￥array是数组$key是中间隔着的值
        static function join($array, $key, $before=NULL){
            if(is_array($array)){
                foreach ($array as $a){
                    if(empty($result)){
                        if(empty($before)){
                            $result=$a;
                        }
                        elseif(1==$before){
                            $result=$key.$a;
                        }
                    }
                    else
                        $result=$result.$key.$a;
                }
            }
            else
                $result=$array;
            return $result;
        }

        
                //限制字数
        static function cutstr($str,$cutleng){
            $cout=count($str);
            if($cout>10000){
                die('字符太长，别太坑爹了！');
            }
            $str = $str; //要截取的字符串
            $cutleng = $cutleng; //要截取的长度
            $strleng = strlen($str); //字符串长度
            if($cutleng>$strleng)
                return $str;//字符串长度小于规定字数时,返回字符串本身
            $notchinanum = 0; //初始不是汉字的字符数
            for($i=0;$i<$cutleng;$i++){
                if(ord(substr($str,$i,1))<=128){
                $notchinanum++;
                }
            }
            if(($cutleng%2==1)&&($notchinanum%2==0)){ //如果要截取奇数个字符，所要截取长度范围内的字符必须含奇数个非汉字，否则截取的长度加一
                $cutleng++;
            }
            if(($cutleng%2==0)&&($notchinanum%2==1)){ //如果要截取偶数个字符，所要截取长度范围内的字符必须含偶数个非汉字，否则截取的长度加一
                $cutleng++;
            }
            return substr($str,0,$cutleng);
        }
        
        
//        static function limit_words($str,$num){
//            $cout=count($str);
//            if($cout>10000){
//                die('字符太长，别太坑爹了！');
//            }
//            $result=null;
//            foreach ($str as $w){
//                $result=$result.$w;
//                --$num;
//                if($num<=0){
//                    break;
//                }
//            }
//            return $result;
//        }
        
	
}