<?php
//注意!!!!!!!:所有sql_use_k::select;函数最多取30条数据
//    

//根据页查找回复
//添加回复，同时加一的回复数
//修改回复
//删除回复
//

class content{
    //content::select('con_content','con_recontent', 页码, 每页条数, "createtime", "desc")        通过页码选择内容并输出,kind_con是索引类别，kind是除了索引以外的类别
    static function select($kind_con, $kind, $page=0, $pagesize=NULL, $where=null, $ordercolumns=null, $deasc=null){
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
                if(0==$i){
                    $wherekind1="'".$k."'";
                }
                else{
                    $wherekind1=$wherekind1.",'".$k."'";
                }
                ++$i;
            }
            $wherekind1=  KIND." in (".$wherekind1.")";
//            $kind=$kk;
        }
        else{
            $wherekind1=  KIND."=".$kind;
        }
        //先将user转成可以用来数据库查询的语句,在结果集中加入user信息
        $i=0;
        $kind_user=  get_kind::$kind_user_con;
        foreach ($kind_user as $user_kind){
             if(0==$i){
                $wherekind2="'".$user_kind."'";
            }
            else{
                $wherekind2=$wherekind2.",'".$user_kind."'";
            }
            ++$i;
        }
        $wherekind2= KIND." in (".$wherekind2.")";
        
        //------------------------------------页码
        if(!$pagesize){
            $pagesize=30;
        }
        //------------------------------------排序
        if(!$ordercolumns){
            if(!$deasc){
                $order= ID." desc";
            }
            else{
                $order= ID." ".$deasc;
            }
        }
        else{ 
            $order= $ordercolumns." ".$deasc;
        }
        //------------------------------------先取第一个数据content
        $result=  sql_use_k::select($kind_con, $where, $pagesize, $page, $order);
        if(empty($result)){
            return $result;
            exit();
        }
        
        
        //--------------------------------------将类别转成文字,并且根据content的id取其他数据
        if(is_array($result)){
            $i=-1;
            foreach ($result as $r){
                $upid   =$r[ID];
                $author =$r[AUTHOR];
                $where_follow=  "(".UPID."=".$upid." and (".$wherekind1.")) or (".UPID."=".$author." and (".$wherekind2."))";
                $re= sql_use_k::select(null, $where_follow, null, null, $order);//注意:最多获取30个数据
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
            $upid   =$result[ID];
            $author =$result[AUTHOR];
            $where_follow=  "(".UPID."=".$upid." and (".$wherekind1.")) or (".UPID."=".$author." and (".$wherekind2."))";
            $re= sql_use_k::select(null, $where_follow, null, null, $order);//注意:最多获取30个数据
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
    static function select_simple($kind, $page=0, $pagesize=NULL, $where=null, $ordercolumns=null, $deasc=null){
        if(!$pagesize){
            $pagesize=30;
        }
        $kind_con=$kind;
        if(!$deasc){
            $deasc="desc";
        }
        if($ordercolumns){
            $order= $ordercolumns." ".$deasc;
        }
        else{ 
            $order= ID." ".$deasc;
        }
        $result=  sql_use_k::select($kind_con, $where, $pagesize, $page, $order);
        if(empty($result)){
            return $result;
            exit();
        }
        
        //--------------------------------------将类别专成文字,并且根据content的id取其他数据结束
        return $result;
    }
    
    static function check($where){
        $result=  sql_use_k::select_w($where, 1);
        return $result;
    }
    //content::insert('内容', 'con_content',限制字数);      插入内容信息
    static function insert($content,$kind,$upid=null,$limitwords=null){
        if(!$limitwords){//限制字数的参数，默认为1000个字符
            $limitwords=1000;
        }
        $content=data_use::cutstr($content, $limitwords);//咔嚓字数的函数，要是多余10000自动跳出
        $result=  sql_use_k::insert($content, $kind, $upid);
        return $result;
    }
    
    //content::insert_sudo('内容', 'author', 'con_content',限制字数);      插入内容信息
    static function insert_sudo($content,$author,$kind, $upid=null, $limitwords=null){
        if(!$limitwords){//限制字数的参数，默认为1000个字符
            $limitwords=1000;
        }
        $content=data_use::cutstr($content, $limitwords);//咔嚓字数的函数，要是多余10000自动跳出
        $result= sql_use_k::insert($content, $kind, $upid, $author);
        return $result;
        }
   
    //content::change(内容的id号, 内容);        修改内容信息(限制过权限的，要想不限制权限的童鞋请自行添加)
    static function change($conid,$content,$columns=null){
        $myid=data_use::get_usr('userid');
        $where= ID."=".$conid;
        if("+1"==$content){
            $result= sql_use_k::add_one($conid, null, null, $myid);
            return $result;
        }
        elseif("-1"==$content){
            $result= sql_use_k::del_one($conid, null, null, $myid);
            return $result;
        }
        else{
            
            $result= sql_use_k::update_w($columns, $content, $where, $myid);
        }
        
    }
    //content::change(内容的id号, 内容);        修改内容信息(限制过权限的，要想不限制权限的童鞋请自行添加)
    static function change_sudo($conid,$content,$where=null,$columns=null){
        $where_plus= ID."=".$conid.$where;
        if("+1"==$content){
            $result= sql_use_k::add_one($conid, null, null, null, $where);
            return $result;
        }
        elseif("-1"==$content){
            $result= sql_use_k::del_one($conid, null, null, null, $where);
            return $result;
        }
        else{
            $result= sql_use_k::update_w($columns, $content, $where_plus);
            return $result;
        }
        
    }
    
    //content::delete(内容id号);    (限制过权限的，要想不限制权限的童鞋请自行添加)
    static function delete($conid=null,$upid=null,$kind=null){
        if($upid && $kind){
            $result= sql_use_k::delete($conid, $kind, $upid);
            return $result;
        }
        elseif($conid){
            $result= sql_use_k::delete($conid);
            return $result;
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
    
    //reply::select(内容的id, 页码, 每页条数, "createtime", "desc")        通过页码选择内容并输出
    static function select($cid, $page, $pagesize=NULL, $ordercolumns=null, $deasc=null){
        $rkind=self::$kind_content;
        if(!$ordercolumns){
            $order= CREATETIME." asc";
        }
        else{ 
            $order= $ordercolumns." ".$deasc;
        }
        
        $where=  UPID."=".$cid;
        
        $result=  sql_use_k::select($rkind, $where, $pagesize, $page, $order);
        if(!$result){
            return NULL;
            exit();
        }
        //遍历加上作者信息
        foreach ($result as $r){
            $author=$r[AUTHOR];
            $where=UPID."=".$author;
            $kind_user=  get_kind::$kind_user_con;
            $rr=  sql_use_k::select($kind_user, $where);
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
        
        $result= sql_use_k::insert($content, $rkind, $cid);
        
        sql_use_k::add_one(null, $rnum, $cid);
        
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