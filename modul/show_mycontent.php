<?php

//lee::lib_load("usr");
lee::lib_load("cont");
$page    =$_GET['page'];
$pagesize  =$_GET['pagesize'];
$id  =$_GET['tapid'];
$search  =$_GET['search'];
//echo json_encode($page) ;

//这个函数只获取自己的数据或者某个特定ID的数据
class con extends content {
    static function select_con($kindkey,$page,$pagesize=null,$tid=null,$search=null){
        $where="1=1";
        //在有tid的情况下查询id为tid的数据
        if($tid){
            $where=$where." and ".ID."=".$tid;
            $result[] = con::select("con_content",$kindkey,0,1,$where);
            $id_where=UPID."=".$tid." and ".KIND."=con_content";
            $id=sql_use_k::select_c(ID, $id_where);
//            $id=sql_use_f::select("id",UPID."=".$tid,"con_content");
            if(!$id){
                return $result;
            }
            $id = array_reverse($id);//倒序数组 按时间顺序出现
            foreach ($id as $eid) {
                $where=ID."=".$eid[ID];
                $result[] = con::select("con_content",$kindkey,0,1,$where);
            }
            return $result;
        }
        else{       
            //在没有tid的情况下只获取自己的数据
            $myid=data_use::get_usr('userid');
            $where=$where." and ".AUTHOR."=".$myid;
        }
        //有search的情况下搜索相关数据
        if($search){
            $where=$where." and ".CONTENT." like '%".$search."%'";
        }
        //查找数据
        $result = con::select("con_content",$kindkey,$page,$pagesize,$where);
        return $result;
    }
}

////如果类别是数组的情况下
//if("all_list"==$kindkey){
////    $kind=array("con_pic","con_type");
//    $kind="con_pic";
//}
$kind=array("con_pic","con_audi","con_swf","con_ytb");

$result=con::select_con($kind, $page,$pagesize,$id,$search);

echo json_encode($result) ; 

