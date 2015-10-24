<?php
/*
 * 针对新的数据库结构进行php的特殊定制和优化
 * select项目全部都用page来控制数量     无$pagesize默认30       无$order默认以id倒叙列出
 * 全是静态类,使用时只函数前加sql_use_k::就可以
 * 先介绍简单用法
 * sql_use_k::select("beutifulgirl");           选取类别为beutifulgirl的数据,限制数量30,ID倒叙列出
 * sql_use_k::insert("good","name");            加入一条新内容,类别为name,值为good
 * sql_use_k::update("sfdsd",1232);             修改id为1232的值为sfdsd
 * sql_use_k::delete(1232);                     删除id为1232的数据
 * sql_use_k::add_one(123);                     给id为123的数字型数据+1
 * sql_use_k::del_one(123);                     给id为123的数字型数据-1
 * 
 * select_c($columns,$where,$page,$pagesize,$order);        以数据项目来选取
 * select_w($where,$page,$pagesize);                        以where决定选取
 * select($kind,$where,$page,$pagesize,$order);             以内容种类决定选取
 * insert($value,$kind,$upid,$author);                      插入函数
 * update_w($columns,$value,$where,$author);                where来修改数据
 * update($value,$id,$kind,$upid,$author,$where);           根据id或者其他项目来修改数据
 * delete_w($where,$author);                                根据where或者作者来删除数据
 * delete($id,$kind,$upid,$author,$where);                  根据id,种类或其他来删除数据
 * add_one($id,$kind,$upid,$author,$where);                 增加1
 * del_one($id,$kind,$upid,$author,$where);                 减去1
 * 
*/

class sql_use_k {
    //    以后要是要改配置文件啥的别忘了改这里
    static public $data_table                 = TABLE;
    static public $data_id                    = ID;
    static public $data_columns               = array(
                        "upid"          => UPID,
                        "author"	=> AUTHOR,
                        "kind"          => KIND,
                        "content"       => CONTENT,
                        "time"          => TIME,
                        "createtime"	=> CREATETIME,
                        "changetime"	=> CHANGETIME
                );
    
    //根据数据表的项目选取数据
    static function select_c($columns,$where=null,$pagesize=null,$page=null,$order=null){
        if($columns){           //如果有column的话加个id和kind，以后用columns不加id和kind
            if("id"==$columns){
                $columns= self::$data_id;
            }
            else{
                if("kind"!=$columns){
                    $pluskind=",".self::$data_columns[kind];
                }
                else{
                    $pluskind="";
                }
                $columns= self::$data_id.$pluskind.",".$columns;
            }

        }
        if(!$order){
            $order=  self::$data_id." desc";
        }

        if(!$page){
            $page=0;
        }
        $limit_begin=$page*$pagesize;
        $limit_num=$pagesize;
        $limit=$limit_begin.",".$limit_num;
            
        sql_use::update_nowtime(self::$data_table, self::$data_columns[time], $where);//找一次更新一次时间
        $result= sql_use::select(self::$data_table,$columns,$where,$order,$limit,false);//注意最后一个变量为true则显示sql语句
        return $result;
    }
    
    //针对where对数据进行选取
    static function select_w($where,$pagesize=null,$page=null){
        $result= self::select_c(null, $where, $pagesize, $page);
        return $result;
    }
    
    //针对内容的项目来选取数据$kind是自定义类别,性别年龄等等
    static function select($kind=null,$where=null,$pagesize=null,$page=null,$order=null){
        if($kind){
            //类别是数组的情况
            if(is_array($kind)){
                foreach ($kind as $k) {
                    if(!is_numeric($kind)){
                        $k="'".$k."'";
                    }
                    $kind_where=$kind_where." or ".self::$data_columns[kind]."=".$k;
                }
                $kind_where=substr($kind_where, 3);
            }
            else{
                if(!is_numeric($kind)){
                    $kind="'".$kind."'";
                }
                $kind_where=self::$data_columns[kind]."=".$kind;
            }
            //有where的情况
            if($where){
                if($where[0]=="("){
                    $no_u=1;
                }
                $where=$where." and (".$kind_where.")";
            }
            else{
                $where=$kind_where;
            }
        }
        $result= self::select_c(null, $where, $pagesize, $page, $order);

        return $result;
    }
    
    //录入信息$value为值,$kind为内容的种类
    static function insert($value,$kind,$upid=null,$author=null){
        if(!$author){
            $author=data_use::get_usr('userid'); 
            if(!$author){
                die("请登录后重试！");
            }
        }
        if(is_array($value)){
                foreach ($value as $c){
                    $values=array($upid,$author,$kind,$c,time(),time(),time());
                    $out=sql_use::insert(self::$data_table, self::$data_columns, $values);
                }
                
                return $out;
            }
            else{
                $values=array($upid,$author,$kind,$value,time(),time(),time());
                return sql_use::insert(self::$data_table, self::$data_columns, $values);
            }
    }
    
    //根据where来决定更新项目 $value同样为种类
    static function update_w($columns=null,$value,$where,$author=null){
        if(!$author){
                $author= data_use::get_usr('userid');
                if(!$author){
                    die("请登录后重试！");
                }
                
                $where= $where." and ".self::$data_columns[author]."=".$author;    //只能修改自己的
        }
        if(!$columns){
            $columns=self::$data_columns[content];
        }
        else{
            if("id"==$columns){
                $columns=  self::$data_id;
            }
            else{
                $columns=  self::$data_columns[$columns];
            }
        }
        
        sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
        $result=sql_use::update_one(self::$data_table, $columns, $value, $where);
        return $result;
    }
    
    //直接根据id来更新数据
    static function update($value,$id=null,$kind=null,$upid=null,$author=null,$where=null){
        if(!$where){
            $where="1=1";
        }
        if($id){
            $where_plus= " and ".self::$data_id."=".$id;
        }
        if($kind && $upid){
            $where_plus= " and ".self::$data_columns[upid]."=".$upid." and ".self::$data_columns[kind]."='".$kind."'";
        }
        if($where_plus){
            $where=$where.$where_plus;
        }
        return self::update_w(null, $value, $where, $author);
    }
    
    //删除where决定的的数据
    static function delete_w($where,$author=null){
        if(!$author){
            $author= data_use::get_usr('userid');
            if(!$author){
                die("请登录后重试！");
            }
            $where= $where." and ".self::$data_columns[author]."=".$author;    //只能修改自己的
        }
        return sql_use::delet(self::$data_table, $where);
    }
    
    //删除id决定的数据
    static function delete($id=null,$kind=null,$upid=null,$author=null,$where=null){
        if (!$where){
            $where="1=1";
        }
       if($id){
            $where_plus= " and ".self::$data_id."=".$id;
        }
        if($upid){
            $where_plus= " and ".self::$data_columns[upid]."=".$upid;
        }
        if($kind){
            $where_plus= $where_plus." and ".self::$data_columns[kind]."='".$kind."'";
        }
        if($where_plus){
            $where=$where.$where_plus;
        }
        self::delete_w($where, $author);
    }
    
    
    //------------------------------------------------------分割线，下面是一些常用的方便使用的函数
    
    //根据id决定,数据+1
    static function add_one($id=null,$kind=null,$upid=null,$author=null,$where=null){
        if(!$author){
            $author= data_use::get_usr('userid');
            if(!$author){
                die("请登录后重试！");
            }
            $where= $where." and ".self::$data_columns[author]."=".$author;    //只能修改自己的
        }
        $columns=self::$data_columns[content];
        if($id){
            $where_plus= " and ".self::$data_id."=".$id;
        }
        if($kind && $upid){
            $where_plus= " and ".self::$data_columns[upid]."=".$upid." and ".self::$data_columns[kind]."='".$kind."'";
        }
        if($where_plus){
            $where=$where.$where_plus;
        }
        sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
        $result=sql_use::update_addone(self::$data_table, $columns, $where);
        return $result;
    }
    //根据id决定,数据-1
    static function del_one($id=null,$kind=null,$upid=null,$author=null,$where=null){
        if(!$author){
            $author= data_use::get_usr('userid');
            if(!$author){
                die("请登录后重试！");
            }
            $where= $where." and ".self::$data_columns[author]."=".$author;    //只能修改自己的
        }
        $columns=self::$data_columns[content];
        if($id){
            $where_plus= " and ".self::$data_id."=".$id;
        }
        if($kind && $upid){
            $where_plus= " and ".self::$data_columns[upid]."=".$upid." and ".self::$data_columns[kind]."='".$kind."'";
        }
        if($where_plus){
            $where=$where.$where_plus;
        }
        sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
        $result=sql_use::update_delone(self::$data_table, $columns, $where);
        return $result;
    }
}