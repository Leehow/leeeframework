<?php
/*验证和注册用户组件
 * $obj=register::ini(new config)		初始化类
 * $obj->register($usr,$psw)			验证若没有用户名重复则将用户数据录入数据库中
 * $obj->login($usr,$psw)				验证若用户合法则登录成功
 * $obj->setting($values)				修改用户资料
 * $obj->changpsw($val)					修改密码
 * $obj->changusr($values)				修改用户数据
 * $obj->logout()						注销
 * usr_use::checklogin()				验证是否登录
 * 重构见尾
 */
abstract class usr {
	protected $table;
	protected $usr_col;
	protected $psw_col;
	protected $columns;
	protected $usr;
	protected $psw;
	protected $values;
	protected $idcol;//用户id字段
	protected $checkresult;
        
//      name是名字的类别id号
//      ip是用户ip的类别id号
//      以后可以添加类别和增加id号，具体看首页文档
        protected $kind_user    = array(
                        name	=> 1001,
                        ip	=> 1002
                        );
        
        
	//抽象方法初始化类
	abstract static function ini(config $config);
	
	function __construct($table=null,$usr_id=null,$usr_col=null,$psw_col=null) {
		$this->set($table, $usr_id, $usr_col, $psw_col,NULL);
	}
	
	//设定字段
	function set($table,$usr_id,$usr_col,$psw_col,$columns) {
		$this->table=$table;
		$this->idcol=$usr_id;
		$this->usr_col=$usr_col;
		$this->psw_col=$psw_col;
		if (!empty($columns))
		$this->columns=$columns;
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
	
	//验证用户名是否存在，存在则返回1
	function check_usr_exist($usr) {
		$check=data_db_use::ini($this->table, $this->usr_col);
		if (!$check->check_exist($usr))
		return "找不到用户名";
		else 
		return 1;
	}
	
	//验证密码是否正确，不正确则返回0
	function check_psw($usr,$psw) {
		$check=data_db_use::ini($this->table, array($this->usr_col,$this->psw_col));
		if (!$result=$check->check_psw($this->usr, $this->psw))
		return 0;
		else 
		return $result;
	}
	
	//-----------------验证部分结束--------------------
	
	//将注册信息录入数据库(可重构)
	protected function register_in() {
		if (!empty($this->columns))
		$col=','.data_use::format_columns($this->columns);
		if (!empty($this->values))
		$val=','.data_use::format_values($this->values);
		return sql_use::insert($this->table, "$this->usr_col,$this->psw_col$col", "'$this->usr','$this->psw'$val");
	}
	
	
	//$obj->changpsw("密码")		修改密码
	function changpsw($val) {
//		if ($this->checkup("p")!=120)
		sql_use::update_one($this->table, "$this->psw_col", "'$val'", "$this->usr_col='$this->usr'");
//		else 
//		exit();
	}
	

        //$obj->register($usr,$psw)	验证若没有用户名重复则将用户数据录入数据库中
	function register($usr,$psw,$values=null){
		$this->set_values($usr,$psw,$values);
		if (1==($check_null_result=$this->check_null_up()))
		if (1==$this->check_usr_exist($usr))
		return "用户名重复";
		else  {
			return $this->register_in();
//			return 1;
		}
		else
		return $check_null_result;
	}
	
	//修改数据库内容(可重构)
	protected function setting_in($where){
		for($i=0;$i<count($this->columns);$i++) 
		if (!empty($this->values[$i]))
		$update[]="$this->columns[$i]='$this->values[$i]'";
		
		sql_use::update($this->table, $update, $where);
	}
	
	//$obj->setting($values)修改用户资料
	function setting($values) {
		$where=$this->idcol."=".data_use::register_get('userid');
		$this->set_values(NULL,NULL,$values);
		
		if (1==($checklogin_result=self::checklogin()))
		$this->setting_in($where);
		else
		return 
		$checklogin_result;
	}
	
	static function out_tkid($value){
		$token='tklogin';
		return data_use::encryption($token, $value,$_SERVER['HTTP_USER_AGENT']);
                //都没写注释了，说明这个东西跟安全有关，所以不懂的别动了！
	}
	
	//$obj->login($usr,$psw)	验证若用户合法则登录成功
	function login($usr,$psw) {
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
		else 
		return $result;
	}
	
	//$obj->checklogin()	验证是否登录
	static function checklogin($tkid){
		if (true!=(data_use::register_static_get('access_'.$tkid)))
		return "用户未登录";
		elseif ($tkid!=self::out_tkid(data_use::register_static_get('userid_'.$tkid)))
		return "用户登录错误，请重新登录";
		else
		return 1;
	}
	
	//$obj->logout()	注销
	static function logout(){
		$out=data_use::register_get('tkid');
		data_use::register_static_delete('access_'.$out);
		data_use::register_static_delete('userid_'.$out);
		data_use::register_static_delete('username_'.$out);
	}
	
} 

/*
 * register_in()和setting_in($where)分别是注册时录入数据库和修改用户资料
 * 重构时只需要修改这两个函数的规则即可
 * 
 */


class usr_use extends usr {
    
        static function ini(config $config) {
		$usr=new usr_use($config->usr_table, $config->usr_id, $config->usr_username, $config->usr_password);
		return $usr;
	}
	
	//验证两次密码是否相同
//	static function check_twopsw($psw1,$psw2) {
//		if($psw1!= $psw2)
//                return "两次密码不一致";
////		error::show(125);
//		else 
//		return 1;
//	}
	
	//usr_use::checklogin()	验证是否登录
	static function checklogin($tkid=null){
            if($tkid==null)
                self::checklogin(data_use::register_get('tkid'));
            else{
		if (1!=($result=parent::checklogin($tkid)))
		return $result;
		else
		return true;
                
                }
	}
	
	//usr_use::checkloginout()	验证是否登录输出对错
	static function checkloginout($tkid){
		if (1==($result=parent::checklogin($tkid)))
		return true;
		else
		return false;
	}
	
        //根据id获取名称，若没有id则获取自己的名称
        static function getname_id($id=NULL){
            if(!$id){
                $id=data_use::get_usr('userid');
            }
            $name=  sql_use_f::select_one_content( "upid", $id, self::$kind_user[name]);
            return $name;
        }

	
        //获取本名称的upid
	static function getid_name($name) {
            $num= sql_use_f::select_one_where("upid", "content", $name, self::$kind_user[name]);
            return $num;
			
	}
        
        //修改密码
        static function change_psw(config $config,$usr,$psw){
            $c=self::ini($config);
            $c->set_values($usr);
            $c->changpsw($psw);
            return 1;
        }
        
        //获取用户资料,如果没有写userid则默认是自己的id
//        static function get_msg($userid=NULL,$kind=NULL){
//            if(empty($userid))
//                $userid=data_use::get_usr('userid');
//                
//            return sql_use_f::select_where("content","upid",$userid,$kind);
//        }
	
}


class user_rel {
//    规定id
    static protected $kind_user    = array(
                        usr_name,
                        usr_ip,
                        usr_gender,    //性别
                        usr_local,    //地点
                        usr_tel,    //电话
                        usr_qq,    //QQ
                        usr_email,    //email
                        usr_pic,     //头像
                        usr_msg,     //消息数量
                        usr_msg_tap,     //消息id
                        usr_msg_hide     //过期消息id
                        );
//    static protected $kind_user    = array(
//                        name	=> 1001,
//                        ip	=> 1002,
//                        gender	=> 1010,    //性别
//                        local	=> 1011,    //地点
//                        tel	=> 1012,    //电话
//                        qq	=> 1013,    //QQ
//                        pic	=> 1014     //头像
//                        );
    static function ini(config $config) {
            $usr=new usr_change;
            $usr->set($config->data_table, $config->data_id, NULL, NULL, $config->data_columns);
            return $usr;
    }

//    创建用户列表
    static function create($userid){
        $create_user    = array(
                        usr_name	=> "用户_".$userid,
                        usr_ip      => $_SERVER["REMOTE_ADDR"],
                        usr_gender	=> 0,    //性别
                        usr_local	=> 0,    //地点
                        usr_tel	=> 0,    //电话
                        usr_qq	=> 0,    //QQ
                        usr_email	=> 0,    //QQ
                        usr_pic	=> 0,     //pic
                        usr_msg	=> 0     //消息
                        );
//        $ku=self::$kind_user;
//        $num=count($ku);
//        遍历所有跟用户有关的kind
        
        foreach ($create_user as $k=>$v){
            sql_use_f::insert($v, $userid, $k, $userid);
        }
        return 1;
        
    }
    
//    查询用户列表，kind可以为数组,如果为空则遍历所有用户信息  userid为空的时候找自己的资料
    static function select($kind=null, $userid=null, $page=null, $pagesize=null){
        if(!$userid){
            $userid= data_use::get_usr('userid');
            sql_use_f::update_one_ku($userid, "usr_ip", $_SERVER["REMOTE_ADDR"]);
        }
        if(!$kind){
            $kind=self::$kind_user;
            
        }
        if(!$pagesize){
            $pagesize=20;
        }
        if(!$page){
            $page=0;
        }
        $where=  sql_use_f::$data_columns[upid]."=".$userid;
        $result= sql_use_f::select_page($kind,$page,$pagesize,null,$where);
//        $result= sql_use_f::select_where(null,"upid", $userid, $kind);
        
        
        return $result;
    }
    
//    修改用户列表，kind和value都可以是数组，要对应改。禁止修改别人的用户信息！！
    static function change($id,$value){
        return sql_use_f::update_one_id($id, $value);
    }
//    删除自己的用户列表~会删掉所有那啥发的信息
    static function delete(){
        $userid=data_use::get_usr('userid');
        sql_use_f::delete($userid, 1);
    }
    
    
    
    //    修改用户列表，kind和value都可以是数组，要对应
    static function change_admin($upid,$kind,$value){
        return sql_use_f::update_one_ku($upid, $kind, $value, $upid);
    }
//    删除用户列表
    static function delete_admin($userid){
        sql_use_f::delete($userid, 1);
    }
}


