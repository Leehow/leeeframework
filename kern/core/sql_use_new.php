 <?php

/*这个就是根据这个数据库content表创建的一个sql_usef类直接把配置文件结构搬过来了
 *  select($columns=null,$where=null,$kind=null,$order=null,$limit=null,$show=false)
 *  select_all($order,$kind=null)
 *  select_where($select_columns,$where_columns,$where_value,$kind=null,$order=null)
 *  select_rand($columns,$where,$limit,$kind=null)
 *  select_one($columns,$where,$kind=null,$order=null)
 *  select_count($where, $kind = null, $columns = null)
 *  select_sum($where,$columns=null,$kind=null)
 *  insert($content,$author,$kind,$upid=null)
 */
class sql_use_f {
	//主要是加了一个$kind类型变量，方便取数据的时候取出类型，改了那么多项。。。！！！   $kind变量直接把整形数据往上放就可以了
    
//    以后要是要改配置文件啥的别忘了改这里
        static public $data_table                 = "data";
        static public $data_id                    = "data_id";
        static public $data_columns               = array(
                            upid	=> "data_upid",
                            author	=> "data_author",
                            kind	=> "data_kind",
                            content     => "data_content",
                            time	=> "data_time",
                            createtime	=> "data_createtime",
                            changetime	=> "data_changetime"
                    );

        static protected $dc_table                 = "data_collect";
        static protected $dc_id                    = "dc_id";
        static protected $dc_columns               = array(
                            cid         => "dc_cid",
                            author	=> "dc_author",
                            time	=> "dc_time",
                            kind        => "dc_kind",
                            ckind       => "dc_ckind",
                            ext         => "dc_ext"
                    );
        
        //搜集信息的函数
        private static function collect($cid, $kind, $ckind, $author=null, $ext=null){
            if(!$author){
                $author=0;
            }
            $values=array(
                            cid         => $cid,
                            author	=> $author,
                            time	=> time(),
                            kind	=> $kind,
                            ckind	=> $ckind,
                            ext         => $ext
                    );
            return sql_use::insert(self::$dc_table, self::$dc_columns, $values);
        }

        //这个函数直接从结果中搜集信息
        private static function collect_result($result, $ckind, $author=null, $ext=null){
            if(!$result){
                return 0;
            }
            if(empty($author)){
                $myid=data_use::get_usr('userid');
                if($myid){
                    $author=$myid;
                }
                else {
                    $myid=null;
                }
            }
            foreach ($result as $r) {
                $cid    =$r[self::$data_id];
                $kind    =$r[self::$data_columns[kind]];
                self::collect($cid, $kind, $ckind, $author, $ext);
            }
            return 1;
        }

        
        
        
        
        //支持$kind是数组的情况select(columns,where,kind,order,limit,show)
        static function select($columns=null,$where=null,$kind=null,$order=null,$limit=null,$show=false){
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
            if($columns){           //如果有column的话加个id和kind，以后用columns不加id和kind
                if("id"==$columns){
                    $columns= self::$data_id;
                }
                else{
                    $columns= self::$data_id.",".self::$data_columns[kind].",".$columns;
                }
                
            }
            if(!$order)
                $order=  self::$data_id." desc";
            
            if(!$no_u){
                sql_use::update_nowtime(self::$data_table, self::$data_columns[time], $where);//找一次更新一次时间
            }
            $result= sql_use::select(self::$data_table,$columns,$where,$order,$limit,$show);
            
            //这里就是将数据收藏到收藏表中
            
            
//            self::collect_result($result, 11);//最后那个11意思是查看
            
            return $result;
	}
	
	//按某一顺序搜索某表中所有数据  sql_use::select_all("col desc/asc",kind)
	static function select_all($order,$kind=null) {
		return self::select( null, null, $kind, $order, null);
	}
        
        //找到满足条件的所有数据    sql_use::select_all_where("col='val'",kind,"col desc/asc")
        static function select_all_where($where,$kind=null,$order=null){
            return self::select(null, $where, $kind, $order, null);
        }
        
        //找到所有content为某值的数据    sql_use::select_all_content(upid,kind,"col desc/asc")
        static function select_all_content($content,$kind=null,$columns=null,$order=null){
            $where=self::$data_columns[content]."=".$content;
            return self::select($columns, $where, $kind, $order, null);
        }
        
        //找到所有upid为某值的数据    sql_use::select_all_upid(upid,kind,"col desc/asc")
        static function select_all_upid($upid,$kind=null,$columns=null,$order=null){
            $where=self::$data_columns[upid]."=".$upid;
            return self::select($columns, $where, $kind, $order, null);
        }
        //找到所有author为某值的数据    sql_use::select_all_author(author,kind,"col desc/asc")
        static function select_all_author($author,$kind=null,$columns=null,$order=null){
            $where=self::$data_columns[author]."=".$author;
            return self::select($columns, $where, $kind, $order, null);
        }
        
        //找到满足条件的content    sql_use::select_where("col","col","val",kind,"col desc/asc")
        static function select_where($select_columns,$where_columns,$where_value,$kind=null,$order=null){
            $where=self::$data_columns[$where_columns]."='".$where_value."'";
            return self::select(self::$data_columns[$select_columns], $where, $kind, $order, null);
        }
        //找到满足条件的content    sql_use::select_where("col","col","val",kind,"col desc/asc")
        static function select_where_content($where_columns,$where_value,$kind=null,$order=null){
            return self::select_where("content", $where_columns, $where_value, $kind);
        }
        
        
        //随机抽取数
        static function select_rand($columns,$where,$limit,$kind=null){
            return self::select( $columns, $where, $kind, "rand()", $limit);
        }
        
        //从一个字段中截取一条数据  sql_use::select_one("col","col='val'",kind,"col desc/asc")
	static function select_one($columns,$where,$kind=null,$order=null) {
            if($kind)
                $where=$where." and ".self::$data_columns[kind]."='".$kind."'";
            if("id"==$columns){
                $columns=self::$data_id;
            }
            else{
                $columns=self::$data_columns[$columns];
            }
            return sql_use::select_one(self::$data_table, $columns, $where, $order);
	}
        
	//从一个字段中截取一条数据  sql_use::select_one_where("col","col","val",kind,"col desc/asc")
	static function select_one_where($select_columns,$where_columns,$where_value,$kind=null,$order=null) {
            if("id"==$where_columns){
                $where=self::$data_id."=".$where_value;
            }
            else{
                $where=self::$data_columns[$where_columns]."='".$where_value."'";
            }
            return self::select_one($select_columns, $where, $kind);
	}
        
        //从一个字段中截取一条数据  sql_use::select_one_content(col","val",kind,"col desc/asc")
	static function select_one_content($where_columns,$where_value,$kind=null,$order=null) {
            return self::select_one_where("content", $where_columns, $where_value, $kind);
	}
        
        
        
        //有限制的取数据,取content中的数据      !!!cont里常用!!!
        //sql_usef::select_page(kind,页码,每页条数,"columns desc")
        static function select_page($kind,$page=null,$pagesize,$order=null,$where=null){
            if(!$page){
                $page=0;
            }
            $limit_begin=$page*$pagesize;
            $limit_num=$pagesize;
            $limit=$limit_begin.",".$limit_num;
            return self::select(null, $where, $kind, $order, $limit);
        }
        
        //sql_usef::select_page_con(kind,页码,每页条数,"columns desc")  这个跟上面那个差不多，只是只取内容而已
        static function select_page_con($kind,$page=null,$pagesize,$order=null,$where=null){
            if(!$page){
                $page=0;
            }
            $limit_begin=$page*$pagesize;
            $limit_num=$pagesize;
            $limit=$limit_begin.",".$limit_num;
            return self::select(self::$data_columns[content], $where, $kind, $order, $limit);
        }
        
        
        
        //从一个表中截取一行符合条件的数据  sql_use::select_row("col='val'",kind,"col desc/asc")
	static function select_row($where,$kind=null,$order=null) {
		return self::select_row_c($where,null,$kind,$order);
	}
        
	//统计相应条件下数据条数总数sql_use::select_count("col='val'",kind, "col")
	static function select_count($where, $kind = null, $columns = null) {
            if($kind)
                $where=$where." and ".self::$data_columns[kind]."=".$kind;
            return sql_use::select_count(self::$data_table, $columns, $where);
	}
        
        
        //统计相应条件下数据数量总数sql_use::select_sum("col='val'","col",kind)
	static function select_sum($where,$columns=null,$kind=null) {
            if($kind)
                $where=$where." and ".self::$data_columns[kind]."=".$kind;
            if(!$columns)
                $columns=self::$data_columns[content];
            return sql_use::select_sum(self::$data_table, $columns, $where);
	}
        
	//！！！！前面是跟查找相关的下面是跟数据库互动个的！！！








        //如果$content是一大串的话
        //1.如果有$upid，则所有数据装入数据库中，每个$content装一个表格，所有upid都是$upid
        //2.如果没$upid，则同上，但是后面的upid全都指向第一个content的id
        //如果$content只有一个值，则这个值直接装入数据库中，灰常方便
        static function insert($content,$author,$kind,$upid=null){
            if(is_array($content)){
                if(!$upid){//在没有upid的情况下自动将第一个content的id作为根节点
                    $i=1;
                    foreach ($content as $c){
                        $values=array($upid,$author,$kind,$c,time(),time(),time());
                        $o=sql_use::insert(self::$data_table, self::$data_columns, $values);
                        if(1==$i){
                            $out=$o;
                            $i--;
                        }
                        else{
                            $upid=$out;
                        }
                    }
                }
                else{
                    foreach ($content as $c){
                        $values=array($upid,$author,$kind,$c,time(),time(),time());
                        $out=sql_use::insert(self::$data_table, self::$data_columns, $values);
                    }
                }
                
                return $out;
            }
            else{
                $values=array($upid,$author,$kind,$content,time(),time(),time());
                return sql_use::insert(self::$data_table, self::$data_columns, $values);
            }
            
        }
        
        static function insert_my($content,$kind,$upid=null){
            $myid=data_use::get_usr('userid');
            if(!$myid){
                return false;
            }
            return self::insert($content, $myid, $kind, $upid);
        }
        
        //在$columns是需要修改的字段，$value是需要修改的数值
        static function update_one($columns=null,$values,$where,$author=NULL){
            if(!$author){
                $author= data_use::get_usr('userid');
                if(!$author){
                    return false;
                }
            }
            if(!$columns){
                $columns=self::$data_columns[content];
            }
            
//            $where= $where." and ".self::$data_columns[author]."=".$author;    //只能修改自己的
            sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
            $result=sql_use::update_one(self::$data_table, $columns, $values, $where);
            return $result;
        }
        static function update_one_ku($upid,$kind,$values,$author=NULL){
            $where= self::$data_columns[upid]."=".$upid." and ".self::$data_columns[kind]."='".$kind."'";
            self::collect(0, $kind, 13, $author, "upid=".$upid."|change_value=".$values);
            return self::update_one(null, $values, $where, $author);
        }

        //只需要id和需要修改的值就可以修改内容
        static function update_one_id($id,$values,$author=NULL){
            $where=self::$data_id."=".$id;
            self::collect($id, 0, 13, $author, "change_value=".$values);
            return self::update_one(null, $values, $where, $author);
        }
        
        //sql_usef::update_addone("col=val/123","content",123);     如果where是数字的话则找id是这个数字的加一
        static function update_addone($where,$columns=null,$author=NULL){
            if(!$author){
                $author= data_use::get_usr('userid');
                if(!$author){
                    return false;
                }
            }
            if(!$columns){
                $columns=self::$data_columns[content];
            }
            if(is_numeric($where)){                 //如果where是数字的话则找id是这个数字的加一
                $chid=self::$data_id."=".$where;
                $where=$chid;
            }
            else{
                $chid=0;
            }
//            $where= $where." and ".self::$data_columns[author]."=".$author;    只能修改自己的
            sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
            $result=sql_use::update_addone(self::$data_table, $columns, $where);
            self::collect($chid, 0, 13, $author, $chid."|change_value=+1");
            return $result;
        }
        //sql_usef::update_delone("col=val/123","content",123);     如果where是数字的话则找id是这个数字的减一
        static function update_delone($where,$columns=null,$author=NULL){
            if(!$author){
                $author= data_use::get_usr('userid');
                if(!$author){
                    return false;
                }
            }
            if(!$columns){
                $columns=self::$data_columns[content];
            }
            if(is_numeric($where)){                 //如果where是数字的话则找id是这个数字的加一
                $chid=self::$data_id."=".$where;
                $where=$chid;
            }
            else{
                $chid=0;
            }
//            $where= $where." and ".self::$data_columns[author]."=".$author;    只能修改自己的
            sql_use::update_nowtime(self::$data_table, self::$data_columns[changetime], $where);
            $result=sql_use::update_delone(self::$data_table, $columns, $where);
            self::collect($chid, 0, 13, $author, $chid."|change_value=-1");
//            self::collect($chid, 0, 13, $author, $where."|change_value=-1");
            return $result;
        }

        //删除id的值以及所有它的下级的值
        static function delete($id, $ifupid=null,$where=null){
            if (!$where){
                $where="1=1";
            }
            if(1==$ifupid){
                $where_plus=  " and ".self::$data_columns[upid]."=".$id;
            }
            else{
                $where_plus=  " and (".self::$data_id."=".$id." or ".self::$data_columns[upid]."=".$id.")";
            }
            if($where_plus){
                $where=$where.$where_plus;
            }
            return sql_use::delet(self::$data_table, $where);
        }

        
	
}
?>
