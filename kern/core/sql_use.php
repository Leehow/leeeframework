<?php
/*抽象类sql_base
 * 提供静态方法
 * sql_base::conn("localhost","root","000000","DbName")			链接数据库
 * sql_base::close()											关闭数据库
 * sql_base::sltdb("dbname")									选择数据库
 * sql_base::erro()												报错
 * sql_base::select()											选择语句生成	
 * sql_base::insert()											插入语句生成
 * sql_base::update()											修改语句生成
 * sql_base::delet()											删除语句生成
 * $obj->mq("query")											执行此sql语句
 * 
 * <!--话说如果你不想扩展或重写的话你根本不需要知道这些,sql_use类更实用-->
 */
abstract class sql_base {
	
	static function get_sae() {
		return new SaeMysql();
	}
	
	//关闭数据库sql_base::close()
	static function close($sae) {
		$sae->closeDb();
	}
	
	//select语句的使用  sql_base::select()
	static function select($table,$columns,$where,$order,$limit) {
		return "select $columns from $table".$where.$order.$limit;
	}
	
	//insert语句使用sql::insert()
	static function insert($table,$columns,$values) {
		return "insert into $table ($columns) values ($values)";
	} 
	
	//update语句的使用sql::update()
	static function update($table,$update,$where) {
		return "update $table set $update where $where";
	}
	
	//delet语句的使用sql::delet()
	static function delet($table,$where) {
		return "delete from $table where $where";
	} 
        
	//转义处理
        static function escape($words){
            return self::get_sae()->escape($words);
        }
        
        //执行语句 $obj->mq("query")
	protected static function mq($query){
//            return $query;
            $sae=self::get_sae();
            $sae->runSql( $query );
            if( $sae->errno() != 0 ){
                $errormsg=$sae->errmsg();
                sae_debug($errormsg);
                return "Error:" . $errormsg;
//                die( "Error:" . $errormsg);
            }
            $lastId=$sae->lastId();
            $sae->closeDb();
            return $lastId;
	}
        
} 

/*实例化sql_use类
 * sql_use::select("table name","col1,col2","col='val'","col desc/asc","1,1")		select语句的使用,直接返回结果
 * sql_use::select_all("table name","col desc/asc")									按某一顺序搜索某表中所有数据
 * sql_use::select_one("table name","col","col='val'")								从一个字段中截取一条数据
 * sql_use::select_row("table name","col='val'")									从一个表中截取一行符合条件的数据
 * sql_use::select_count("table name","col","col='val'")							统计相应条件下数据条数总数
 * sql_use::insert("table name","col1,col2,col3","'val1','val2','val3'")			输入数据库(可用数组带入)
 * sql_use::update("table name","col1='val1'","col2='val2'")						修改数据库资料
 * sql_use::update_one("table name","col","val","col2='val2'")						修改一条数据
 * sql_use::update_addone("table name","col","col='val'")							将数据做加一操作
 * sql_use::delet("table name","col='val'")											删除一条数据
 */
class sql_use extends sql_base {
	
	//select语句的使用   sql_use::select("table name","col1,col2","col='val'","col desc/asc","1,1")
	static function select_q($table=null,$columns=null,$where=null,$order=null,$limit=null) {
		if (!isset($columns))
		$columns="*";
                elseif(is_array($columns))
                    $columns=data_use::join($columns, ",");
		if (isset($where))
		$where=" where ".$where;
		if (isset($order))
		$order=" order by ".$order;
		if (isset($limit))
		$limit=" limit ".$limit;
//		echo parent::select($table, $columns, $where, $order, $limit)."<br>";
		return parent::select($table, $columns, $where, $order, $limit);
	}
	
        static function select($table,$columns=NULL,$where=NULL,$order=NULL,$limit=NULL,$show=false){
		$query=self::select_q($table, $columns, $where, $order, $limit);
                if($show==true)
                    echo $query;
//                echo $query."<br/><br/><br/>";
//                return $query;
		$data=self::get_sae()->getData ($query);
		if( self::get_sae()->errno() != 0 )
		die( "Error:" . self::get_sae()->errmsg() );
                self::get_sae()->closeDb();
		if (!empty($data))
		return $data;
	}
	
	//按某一顺序搜索某表中所有数据  sql_use::select_all("table name","col desc/asc")
	static function select_all($table,$order) {
		return self::select($table, NULL, NULL, $order, NULL);
	}
        
        //找到满足条件的所有数据
        static function select_all_where($table,$where,$order=NULL){
            return self::select($table, NULL, $where, $order, NULL);
        }
        
        //随机抽取数
        static function select_rand($table,$columns,$where,$limit){
            return self::select($table, $columns, $where, "rand()", $limit);
        }
        
        //从一个字段中截取一条数据  sql_use::select_one("table name","col","col='val'")
	static function select_one($table,$columns,$where,$order=NULL) {
		$query=self::select_q($table, $columns, $where,$order=NULL);
//                echo $query;
                $oneMsg=self::get_sae()->getVar($query);
                self::get_sae()->closeDb();
		return $oneMsg;
	}
	
        //从一个表中截取一行符合条件的数据  sql_use::select_row("table name","col","col='val'")
	static function select_row_c($table,$columns=null,$where,$order=null) {
		$query=self::select_q($table, $columns, $where,$order);
                $lineMsg=self::get_sae()->getLine($query);
                self::get_sae()->closeDb();
		return $lineMsg;
	}
        
        //左表 (table1) 那里返回所有的行，即使在右表 (table2) 中没有匹配的行
        static function select_left_join($table1, $table2, $columns=null, $on, $where=null, $order=null, $limit=null){
            if("ein"==$limit)
                return self::select_row_c("$table1 left join $table2 on $on", $columns, $where, $order);
            else
                return self::select("$table1 left join $table2 on $on", $columns, $where, $order, $limit);
        }
        
        //group by
        static function select_group($table, $columns=null, $where=null, $group_c, $order=null, $limit=null){
                return self::select($table, $columns, $where." group by ".$group_c, $order, $limit);
        }

        //从一个表中截取一行符合条件的数据  sql_use::select_row("table name","col='val'")
	static function select_row($table,$where,$order=null) {
		return self::select_row_c($table,null,$where,$order);
	}
        
	//统计相应条件下数据条数总数sql_use::select_count("table name","col","col='val'")
	static function select_count($table,$columns=null,$where) {
		if (empty($columns))
		$columns="*";
		$columns="count($columns)";
		if (isset($where))
		$where=" where ".$where;
		$query=parent::select($table, $columns, $where,NULL,NULL);
                $result=self::get_sae()->getVar($query);
                self::get_sae()->closeDb();
		return $result;
	}
        
        
        //统计相应条件下数据数量总数sql_use::select_sum("table name","col","col='val'")
	static function select_sum($table,$columns,$where) {
		if (!isset($columns))
		$columns="*";
		$columns="sum($columns)";
		if (isset($where))
		$where=" where ".$where;
		$query=parent::select($table, $columns, $where,NULL,NULL);
		$result= self::get_sae()->getVar($query);
                self::get_sae()->closeDb();
		return $result;
	}
	
	//输入数据库 sql_use::insert("table name","col1,col2,col3","'val1','val2','val3'")
	//或 sql_use::insert("table name",array(col1,col2,col3),array(val1,val2,val3))
	static function insert($table,$columns,$values) {
		if (is_array($columns) && is_array($values)) {
			foreach ($columns as $c)
			$col=$col.",".$c;
			$columns=substr($col, 1);
			foreach ($values as $v) {
				if (is_numeric($v))
				$val=$val.",".$v;
				elseif (!isset($v))
				$val=$val.",NULL";
				else 
				$val=$val.",'".$v."'";
			}
			$values=substr($val, 1);
		}
		$query=parent::insert($table, $columns, $values);
//                return $query;
//                echo "<br>".$query."<br>";
                return parent::mq($query);
		
	}
	
	//修改数据库资料 sql_use::update("table name","col1='val1'","col2='val2'")
	//或 sql_use::update("table name",array("col1='val1'","col2='val2'"),"col3='val3'")
	static function update($table,$update,$where) {
		if (is_array($update)) {
			foreach ($update as $u)
			$up=$up.",".$u;
			$update=substr($up, 1);
		}
		$query=parent::update($table, $update, $where);
//                die($query);
//          echo "<br>".$query."<br>";
		return parent::mq($query);
	}
	
	//修改一条数据sql_use::update_one("table name","col","val","col2='val2'")
	static function update_one($table,$columns,$values,$where) {
                if(!is_numeric($values)){
                    $values="'".$values."'";
                }
		return self::update($table, "$columns=$values", $where);
	}
	
        static function update_add($table,$columns,$values,$where) {
                return self::update_one($table, $columns,"concat(".$columns.",".$values.")" , $where);
        }

                
	//将数据做加一操作sql_use::update_addone("table name","col","col='val'")
	static function update_addone($table,$columns,$where) {
                return self::update($table, "$columns=$columns+1", $where);
	}
	
	//将数据做减一操作sql_use::update_delone("table name","col","col='val'")
	static function update_delone($table,$columns,$where) {
                return self::update($table, "$columns=$columns-1", $where);
	}
        
        //将时间定为现在时间
	static function update_nowtime($table,$columns,$where) {
		return self::update_one($table, $columns, time(), $where);
	}
	
	//删除一条数据sql_use::delet("table name","col='val'")
	static function delet($table,$where) {
		$query=parent::delet($table, $where);
		return parent::mq($query);
	}
	
}