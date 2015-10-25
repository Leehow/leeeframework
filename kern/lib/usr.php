<?php
/*验证和注册用户组件
 * $obj=register::ini()                                 初始化类
 * usr::register($usr,$psw)                             验证若没有用户名重复则将用户数据录入数据库中
 * usr::login($usr,$psw)				验证若用户合法则登录成功
 * usr::logout()                                        注销
 * usr::change_psw($usr,$psw)                           静态方法修改密码(更方便)
 * 
 * user_manage::create($userid)                         创建用户初始化参数,userid是用户的id
 * user_manage::select($kind,$userid,$page,$pagesize)   查询用户的信息,kind是类别参数,如果没有kind则获取所有用户信息
 * user_manage::change($kid,$value)                     修改用户信息,kid可以是id号或kind,value是要修改的值(仅自己)
 * user_manage::delete()                                删除用户信息(仅自己)
 * user_manage::change_admin($userid,$kind,$value)      修改用户信息,userid是用户id
 * user_manage::delete_admin($userid)                   删除用户信息,userid是用户id
 */
class usr {
	protected $table    = "usr";
        protected $idcol    = "usr_id";//用户id字段
	protected $usr_col  = "username";
	protected $psw_co   = "password";
	protected $usr;
	protected $psw;
	protected $values;
	
	protected $checkresult;
        
                        //抽象方法初始化类
	static function ini(){
            $c=new usr_check();
            return $c;
        }
	
	//设定值
	function set_values($usr,$psw=null,$values=null) {
		$this->usr=$usr;
                if(!empty($psw))
		$this->psw=$psw;
		if (!empty($values))
		$this->values=$values;
	}
	
	//-----------------验证部分----------------------
	
	//验证用户名或密码是否为空，用不为空则为1
	function check_null_up() {
		if (empty($this->usr))
		return "用户名为空";
		elseif (empty($this->psw))
		return "密码为空";
		else 
		return 1;
	}
	//在数据库中查询验证是否存在
	function db_exist($where) {
		$result=sql_use::select_row($this->table, $where);
		if (!empty($result))
		return $result;
		else
		return false;
	}
        
	//验证用户名是否存在，存在则返回1
	function check_usr_exist($usr) {
                $check=self::ini();
                $where="$this->usr_col='$usr'";
                $result=$check->db_exist($where);
                
		if (!$result)
		return "找不到用户名";
		else 
		return 1;
	}
	
	//验证密码是否正确，不正确则返回0
	function check_psw($usr,$psw) {
                $check=self::ini();
                $where=  $this->usr_col."='$usr' and ".$this->psw_col."='$psw'";
                $result=$check->db_exist($where);
		if (!$result)
		return 0;
		else 
		return $result;
	}
	
	//-----------------验证部分结束--------------------
	
	//将注册信息录入数据库(可重构)
	protected function register_in() {
		if (!empty($this->values)){
                    $val=','.data_use::format_values($this->values);
                }
		return sql_use::insert($this->table, "$this->usr_col,$this->psw_col$col", "'$this->usr','$this->psw'$val");
	}
	
	
	//$obj->changpsw("密码")		修改密码
	function changpsw($val) {
		sql_use::update_one($this->table, "$this->psw_col", "'$val'", "$this->usr_col='$this->usr'");
	}
	

        //$obj->registerf($usr,$psw)	验证若没有用户名重复则将用户数据录入数据库中
	function registerf($usr,$psw){
		$this->set_values($usr,$psw);
		if (1==($check_null_result=$this->check_null_up())){
                    if (1==$this->check_usr_exist($usr)){
                        return "用户名重复";
                    }
                    else  {
                            return $this->register_in();
    //			return 1;
                    }
                }
		else{
                    return $check_null_result;
                }
	}
        
        static function out_tkid($value){
		$token='tklogin';
		return data_use::encryption($token, $value,$_SERVER['HTTP_USER_AGENT']);
                //都没写注释了，说明这个东西跟安全有关，所以不懂的别动了！
	}
        
	//$obj->loginf($usr,$psw)	验证若用户合法则登录成功
	function loginf($usr,$psw) {
		$this->set_values($usr,$psw,null);
		if (0!=($result=$this->check_psw($usr,$psw))) {
			$id=$result[$this->idcol];
			$out=self::out_tkid($id);
			data_use::register_static_set('access_'.$out, true);
			data_use::register_static_set('userid_'.$out, $id);
			data_use::register_static_set('username_'.$out, $this->usr);
                        data_use::register_static_set('topic',1);
                        data_use::register_static_set('c',10);
			return $out;
		}
		else {
                    return $result;
                }
	}
	
        
        //usr::register($usr,$psw)      用户注册,静态
        static function register($usr,$psw){
            $c=  self::ini();
            $result=$c->registerf($usr,$psw);
            return $result;
        }
        
        //usr::change_psw($usr,$psw)    修改密码
        static function change_psw($usr,$psw){
            $c=self::ini();
            $c->set_values($usr);
            $c->changpsw($psw);
            return 1;
        }
        
        //usr::login($usr,$psw)         用户注册
        static function login($usr,$psw){
            $c=  self::ini();
            $result=    $c->loginf($usr,$psw);
            return $result;
        }
        
        //usr::logout()                 注销
	static function logout(){
		$out=data_use::register_get('tkid');
		data_use::register_static_delete('access_'.$out);
		data_use::register_static_delete('userid_'.$out);
		data_use::register_static_delete('username_'.$out);
	}
	
} 


class user_manage {
//    创建用户列表
    static function create($userid){
        $create_user    = get_kind::ini_user($userid);
//        遍历所有跟用户有关的kind
        foreach ($create_user as $k=>$v){
            sql_use_k::insert($v, $k, $userid, $userid);
        }
        return 1;
        
    }
    
//    查询用户列表，kind可以为数组,如果为空则遍历所有用户信息  userid为空的时候找自己的资料
    static function select($kind=null, $userid=null, $page=null, $pagesize=null){
        if(!$userid){
            $userid= data_use::get_usr('userid');
            $ip_now=$_SERVER["REMOTE_ADDR"];
            sql_use_k::update($ip_now, null, "usr_ip", $userid);
        }
        if(!$kind){
            $kind=  get_kind::$kind_user;
            
        }
        if(!$pagesize){
            $pagesize=20;
        }
        if(!$page){
            $page=0;
        }
        
        $where= UPID."=".$userid;
        $result=  sql_use_k::select($kind, $where, $pagesize, $page);
        
        
        return $result;
    }
    
//    修改用户列表，kind和value都可以是数组，要对应改。禁止修改别人的用户信息！！
    static function change($kid,$value){
        $ifint=  is_numeric($kid);
        if(!$ifint){
            $result= sql_use_k::update($value, null, $kid);
        }
        else{
            $result= sql_use_k::update($value, $kid);
        }
        return $result;
    }
    
//    删除自己的用户列表~会删掉所有发的信息
    static function delete(){
        $userid=data_use::get_usr('userid');
        sql_use_k::delete_w("1",$userid);
        return 1;
    }
    
    
    
    //    修改用户列表，kind和value都可以是数组，要对应
    static function change_admin($userid,$kid,$value){
        $ifint=  is_numeric($kid);
        if(!$ifint){
            $result= sql_use_k::update($value, null, $kid, $userid, $userid);
        }
        else {
            $result= sql_use_k::update($value, $kid, null, $userid, $userid);
        }
        return $result;
    }
//    删除用户列表
    static function delete_admin($userid){
        $where=  UPID."=".$userid;
        sql_use_k::delete_w($where, $userid);
    }
}


