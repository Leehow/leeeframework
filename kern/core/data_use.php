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
	
	static function match($value1,$value2) {
		if (preg_match("/\b".$value1."\b/i", $value2))
		return true;
		else 
		return false;
	}


        
	//设定memcache
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

//        测试是否是wap网页
        static function check_wap(){
		if (isset ($_SERVER['HTTP_USER_AGENT'])) {
			  $clientkeywords = array (
			   'nokia',
			   'sony',
			   'ericsson',
			   'mot',
			   'samsung',
			   'htc',
			   'sgh',
			   'lg',
			   'sharp',
			   'sie-',
			   'philips',
			   'panasonic',
			   'alcatel',
			   'lenovo',
			   'iphone',
			   'ipod',
			   'blackberry',
			   'meizu',
			   'android',
			   'netfront',
			   'symbian',
			   'ucweb',
			   'windowsce',
			   'palm',
			   'operamini',
			   'operamobi',
			   'openwave',
			   'nexusone',
			   'cldc',
			   'midp',
			   'wap',
			   'mobile'
			  );
			
			  // 从HTTP_USER_AGENT中查找手机浏览器的关键字
			
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
			   return true;
			  }
			  else 
			  return false;
 		}
		
	}
        
//        这个也是，但是没上面那个好用
        static function is_mobile() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte","Windows Phone");
	$is_mobile = $_SERVER['HTTP_USER_AGENT'];
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {
			$is_mobile = true;
			break;
		}
	}
	return $is_mobile;
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
/*
 * 带常量的数据处理
 * 
 * 这个得改！！！新框架里常量变了太多
 * data_const_use::getmsg("college",1);
 * 
 */
/*
 * data_db_use::ini($table,$columns)	初始化数据
 * $obj->db_exist($value)						在数据库中查询验证是否存在
 * 
 */
class data_db_use {
	var $table;
	var $columns;
	
	//初始化数据
	static function ini($table,$columns) {
		$self=new data_db_use();
		$self->set_all($table, $columns);
		return $self;
	}
	
	//设定表和字段
	function set_all($table,$columns){
		$this->table=$table;
		$this->columns=$columns;
	}
	
	function set_col($columns) {
		$this->columns=$columns;
	}
	
	//在数据库中查询验证是否存在
	function db_exist($where) {
		$result=sql_use::select_row($this->table, $where);
		if (!empty($result))
		return $result;
		else
		return false;
	}
	
	//检查数据库中是否存在某一个值
	function check_exist($value) {
		$where="$this->columns='$value'";
		return $this->db_exist($where);
	}
	
	//验证用户名密码
	function check_psw($usr,$psw) {
		$where=$this->columns[0]."='$usr' and ".$this->columns[1]."='$psw'";
		return $this->db_exist($where);
	}
	
	
	
	//快速接口检测是否存在
	static function exist($table,$where) {
		$result=sql_use::select_row($table, $where);
		if (!empty($result))
		return $result;
		else
		return false;
	}
	
}
