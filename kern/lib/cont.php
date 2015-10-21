<?php
//

//class cont{
//    
//}




//根据页查找回复
//添加回复，同时加一的回复数
//修改回复
//删除回复
//

class content{
    static protected $kind_content    = array(
                        usr_name	=> 1001,
                        usr_ip          => 1002,
                        usr_gender	=> 1010,    //性别
                        usr_local	=> 1011,    //地点
                        usr_tel         => 1012,    //电话
                        usr_qq          => 1013,     //QQ
                        usr_pic         => 1014,     //头像

        //--------------------------------------------------分割线       
                        con_content	=> 2001,    //商品名称
                        con_renum       => 2002,    //回复数量
                        con_recontent   => 2003,    //回复内容
                        con_booknum     => 2004,    //预定数量
                        con_book        => 2005,    //预定
        
                        con_price       => 2010,    //价格
                        con_deteil      => 2011,    //产品细节
                        con_pic         => 2012,    //图片
                        con_clocal      => 2013,    //地点
        
        
                        con_content_hide=> 2101,     //已过期商品名称
                        con_content_myhide=> 2201,     //被和谐的商品名称
                        
        //--------------------------------------------------分割线
                        local_country   => 5001,     //国名
                        local_bundes    => 5002,     //州名
                        local_city      => 5003,      //地名
        
        //--------------------------------------------------分割线     
                        feedback   => 6001     //问题反馈
        
        //--------------------------------------------------分割线 
                        );
    static protected $kind_user    = array(
                        usr_name	,
                        usr_ip          ,
                        usr_gender	,    //性别
                        usr_local	,    //地点
                        usr_tel         ,    //电话
                        usr_qq          ,    //QQ
                        usr_email          ,    //email
                        usr_pic              //头像
                        );
    
    //content::select('con_content','con_recontent', 页码, 每页条数, "createtime", "desc")        通过页码选择内容并输出,kind_con是索引类别，kind是除了索引以外的类别
    static function select($kind_con, $kind, $page=0, $pagesize=NULL, $where=null, $orderkind=null, $deasc=null){
        if(!$kind){
            $kind=array('con_content','con_booknum','con_renum');
        }
        if(!$page){
            $page=0;
        }
        //先将kind转成可以用来数据库查询的语句
        if(is_array($kind)){
            $i=0;
            foreach ($kind as $k){
//                $ki     =self::$kind_content[$k];
//                $kk[]   = $ki;
                if(0==$i){
                    $wherekind1="'".$k."'";
                }
                else{
                    $wherekind1=$wherekind1.",'".$k."'";
                }
                ++$i;
            }
            $wherekind1=sql_use_f::$data_columns[kind]." in (".$wherekind1.")";
//            $kind=$kk;
        }
        else{
//            $kind=  self::$kind_content[$kind];
            $wherekind1=sql_use_f::$data_columns[kind]."=".$kind;
        }
        //先将user转成可以用来数据库查询的语句,在结果集中加入user信息
        $i=0;
        foreach (self::$kind_user as $user_kind){
             if(0==$i){
                $wherekind2="'".$user_kind."'";
            }
            else{
                $wherekind2=$wherekind2.",'".$user_kind."'";
//                $wherekind2=$wherekind2." or ".sql_use_f::$data_columns[kind]."=".$user_kind;
            }
            ++$i;
        }
        $wherekind2=sql_use_f::$data_columns[kind]." in (".$wherekind2.")";
        
        //------------------------------------页码
        if(!$pagesize){
            $pagesize=30;
        }
        //------------------------------------排序
        if(!$orderkind){
            if(!$deasc){
                $order= sql_use_f::$data_id." desc";
            }
            else{
                $order= sql_use_f::$data_id." ".$deasc;
            }
        }
        else{ 
            $order= sql_use_f::$data_columns[$orderkind]." ".$deasc;
        }
        //------------------------------------先取第一个数据content
//        $kind_con=self::$kind_content[$kind_con];
        $result=sql_use_f::select_page($kind_con,$page,$pagesize,$order,$where);//只取数据里的content
        if(empty($result)){
            return $result;
            exit();
        }
        
        
        //--------------------------------------将类别转成文字,并且根据content的id取其他数据
        if(is_array($result)){
            $i=-1;
            foreach ($result as $r){
                $upid   =$r[sql_use_f::$data_id];
                $author =$r[sql_use_f::$data_columns[author]];
                $where_follow=  "(".sql_use_f::$data_columns[upid]."=".$upid." and (".$wherekind1.")) or (".sql_use_f::$data_columns[upid]."=".$author." and (".$wherekind2."))";
                $re= sql_use_f::select(null, $where_follow, null, $order);
                if($re){
                    array_push($re, $r);
                    $result2[]=$re;
                }
                else{
                    $result2[]=$r;
                }
                
                
            }
            $result=$result2;
//            $result=$re;
        }
        else{
            $upid   =$result[sql_use_f::$data_id];
            $author =$result[sql_use_f::$data_columns[author]];
            $where_follow=  "(".sql_use_f::$data_columns[upid]."=".$upid." and (".$wherekind1.")) or (".sql_use_f::$data_columns[upid]."=".$author." and (".$wherekind2."))";
            $re= sql_use_f::select(null, $where_follow, null, $order);
            if($re){
                    array_push($re, $result);
                    $result2=$re;
                }
                else{
                    $result2=$r;
                }
            $result=$result2;
        }
        //--------------------------------------将类别专成文字,并且根据content的id取其他数据结束
        return $result;
    }
    
    
    
    
    //content::select_simple('con_recontent', 页码, 每页条数, "createtime", "desc")        通过页码选择内容并输出,只取一个数据的时候用
    static function select_simple($kind, $page=0, $pagesize=NULL, $where=null, $orderkind=null, $deasc=null){
        if(!$pagesize){
            $pagesize=30;
        }
        $kind_con=$kind;
        if(!$deasc){
            $deasc="desc";
        }
        if($orderkind){
            $order= sql_use_f::$data_columns[$orderkind]." ".$deasc;
        }
        else{ 
            $order= sql_use_f::$data_id." ".$deasc;
        }
        $result=sql_use_f::select_page($kind_con,$page,$pagesize,$order,$where);//只取数据里的content
        if(empty($result)){
            return $result;
            exit();
        }
        
        //--------------------------------------将类别专成文字,并且根据content的id取其他数据结束
        return $result;
    }
    
    static function check($where){
        $result=sql_use_f::select_one(null, $where);
        return $result;
    }
    //content::insert('内容', 'con_content',限制字数);      插入内容信息
    static function insert($content,$kind,$upid=null,$limitwords=null){
        if(!$limitwords){//限制字数的参数，默认为1000个字符
            $limitwords=1000;
        }
        $content=data_use::cutstr($content, $limitwords);//咔嚓字数的函数，要是多余10000自动跳出
//        $kind=  self::$kind_content[$kind];
        return sql_use_f::insert_my($content, $kind, $upid);  
    }
    
    //content::insert_sudo('内容', 'author', 'con_content',限制字数);      插入内容信息
    static function insert_sudo($content,$author,$kind, $upid=null, $limitwords=null){
        if(!$limitwords){//限制字数的参数，默认为1000个字符
            $limitwords=1000;
        }
        $content=data_use::cutstr($content, $limitwords);//咔嚓字数的函数，要是多余10000自动跳出
//        $kind=  self::$kind_content[$kind];
        return sql_use_f::insert($content, $author, $kind, $upid);
        }
   
    //content::change(内容的id号, 内容);        修改内容信息(限制过权限的，要想不限制权限的童鞋请自行添加)
    static function change($conid,$content,$columns=null){
        $myid=data_use::get_usr('userid');
        $where_limit=" and ".sql_use_f::$data_columns[author]."=".$myid;//对权限进行限制（只能自己才能修改）
        $where= sql_use_f::$data_id."=".$conid.$where_limit;
        if("+1"==$content){
            return sql_use_f::update_addone($where);
        }
        elseif("-1"==$content){
            return sql_use_f::update_delone($where);
        }
        else{
            return sql_use_f::update_one($columns,$content,$where);
        }
        
    }
    //content::change(内容的id号, 内容);        修改内容信息(限制过权限的，要想不限制权限的童鞋请自行添加)
    static function change_sudo($conid,$content,$where=null,$columns=null){
        $where= sql_use_f::$data_id."=".$conid.$where;
        if("+1"==$content){
            return sql_use_f::update_addone($where);
        }
        elseif("-1"==$content){
            return sql_use_f::update_delone($where);
        }
        else{
            return sql_use_f::update_one($columns,$content,$where);
        }
        
    }
    
    //content::delete(内容id号);    (限制过权限的，要想不限制权限的童鞋请自行添加)
    static function delete($conid=null,$upid=null,$kind=null){
        $myid=data_use::get_usr('userid');
        $where_limit=" and ".sql_use_f::$data_columns[author]."=".$myid;//对权限进行限制（只能自己才能修改）
        if($upid && $kind){
//            $kind=self::$kind_content[$kind];
            $where=  sql_use_f::$data_columns[kind]."='".$kind."'".$where_limit;
            return sql_use_f::delete($upid,1,$where);
        }
        elseif($conid){
            $where=  sql_use_f::$data_id."=".$conid.$where_limit;
            return sql_use_f::delete($conid,null,$where);
        }
        else{
            return FALSE;
        }
        
        
    }
    

}

class reply{
    static protected $kind_content    = array(
                        con_recontent   ,        //回复内容
                        con_recontent_se        //已阅回复内容
                        );
        static protected $kind_user    = array(
                        usr_name	,
                        usr_ip          ,
                        usr_gender	,    //性别
                        usr_local	,    //地点
                        usr_tel         ,    //电话
                        usr_qq          ,    //QQ
                        usr_email          ,    //email
                        usr_pic              //头像
                        );
    
    //reply::select(内容的id, 页码, 每页条数, "createtime", "desc")        通过页码选择内容并输出
    static function select($cid, $page, $pagesize=NULL, $orderkind=null, $deasc=null){
        $rkind=self::$kind_content;
        if(!$pagesize){
            $pagesize=30;
        }
        if(!$orderkind){
            $order= sql_use_f::$data_columns[createtime]." asc";
        }
        else{ 
            $order= sql_use_f::$data_columns[$orderkind]." ".$deasc;
        }
        
        //页码的偏移
        if(!$page){
            $page=0;
        }
        $limit_begin=$page*$pagesize;
        $limit_num=$pagesize;
        $limit=$limit_begin.",".$limit_num;
        //页码的偏移完
        $where=  sql_use_f::$data_columns[upid]."=".$cid;
        
        $result=sql_use_f::select(null, $where, $rkind, $order, $limit);
        if(!$result){
            return NULL;
            exit();
        }
        //遍历加上作者信息
        foreach ($result as $r){
            $author=$r[sql_use_f::$data_columns[author]];
            $where=sql_use_f::$data_columns[upid]."=".$author;
            $rr=sql_use_f::select(null, $where, self::$kind_user);
            array_push($rr, $r);
            $res[]=$rr;
        }
        $result=$res;
        
        return $result;
        
    }
    

    //content::insert(内容, 'con_content',限制字数);      插入内容信息
    static function insert($content,$cid,$limitwords=null){
        if(!$limitwords){//限制字数的参数，默认为1000个字符
            $limitwords=1000;
        }
        $content=data_use::cutstr($content, $limitwords);//咔嚓字数的函数，要是多余10000自动跳出
        $rkind= 'con_recontent';
        $rnum= 'con_renum';//获取回复数量的kind_id
        
        $result=  sql_use_f::insert_my($content, $rkind, $cid);//插入回复
        
        $where=  sql_use_f::$data_columns[kind]."='".$rnum."' and ".sql_use_f::$data_columns[upid]."=".$cid;
        sql_use_f::update_addone($where);//回复数量+1
        return $result;
    }
    
     //content::change(内容的id号, 内容);        修改内容信息(限制过权限的，要想不限制权限的童鞋请自行添加)
    static function change($reid,$content){
        //限制权限只能修改自己的权限
        return content::change($reid, $content);
    }
    
    //content::delete(内容id号);   (限制过权限的，要想不限制权限的童鞋请自行添加)
    static function delete($reid){
        //限制权限
        return content::delete($reid);
    }
    
}
?>