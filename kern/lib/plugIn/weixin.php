<?php
class weixin{
    //获取商家名称
    static function show_res(config $config){
        $rs = sql_use::select($config->d_table, $config->d_id.",".$config->d_columns[content], $config->d_columns[kind]." = 11");
        foreach ($rs as $r){
            if(empty($result))
                $result=NULL;
            $result=$result.$r[$config->d_columns[content]]."(".$r[$config->d_id].")\n订餐热线:".sql_use::select_one($config->d_table, $config->d_columns[content], $config->d_columns[kind]." = 13 and ".$config->d_columns[upid]."=".$r[$config->d_id])."\n\n";
        }
        return $result;
    }
    //获取菜单
    static function show_menu(config $config,$num){
        $result = sql_use::select_one($config->d_table, $config->d_columns[content], $config->d_columns[kind]." = 12  and ".$config->d_columns[upid]."=".$num);
        $match="/(元)\s+|(元)$|(\/例)\s+|(\/例)$|(：)/";
        $result = preg_replace($match, "$1\n", $result);
//        $match="/：/";
//        $result = preg_replace($match, "：\n", $result);
        return $result;
    }
    
    //获取拼车信息
    static function getcon_car(config $config,$local=NULL){
        if(empty($local))
            $local="拼车";
        $content=content::ini($config);
//        $content_data=$content->select_all_search(NULL, $local,20);
        $rtime=  time()-(3600*24*3);
        $where=$config->content_columns[content]." like '%@".$local."%' and ".$config->content_columns[time].">".$rtime;
        $content_data=$content->select_lim_where($where,8);
        while ($content_data){
            $c=array_pop($content_data);
            $time=$c[$config->content_columns[time]];
            $time=date(" m月d日H:i:s发布",$time);
            $content=  self::misover($c[$config->content_columns[content]]);
            if(!empty($content))
                $result=$result."ID".$c[$config->content_columns[author]].":".$content."\n".$time."\n\n";
        }
        if(empty($result))
            $result="对不起，这里暂时没有信息或信息已过期";
        return $result;
    }
    
    //获取其他信息
    static function getcon_c(config $config,$local){
        $content=content::ini($config);
        $content_data=$content->select_all_search(NULL, $local,8);
        while ($content_data){
            $c=array_pop($content_data);
            $time=$c[$config->content_columns[time]];
            $qt=self::matime($time);
            $content=  self::misover($c[$config->content_columns[content]]);
            if(!empty($content))
                $result=$result.$content." ".$qt."发布\n\n";
        }
        if(empty($result))
            $result="对不起，这里暂时没有信息或信息已过期";
        
        return $result;
    }
    
    //强制录入拼车信息
    static function incon_car(config $config,$con,$sendto=NULL){
        $re=  preg_match("/^秦皇岛\s|^北戴河\s|^山海关\s/", $con);
        if(1!=$re)
            return null;
        $con="@拼车 @".$con." @来自微信";
        $result=content::insertat_stark($config, $con,5,2,$sendto);
        return $result;
    }
    
    //强制录入拼车信息
    static function incon_mycar(config $config,$con,$sendto=NULL){
        $re=  preg_match("/^@拼车\s/", $con);
        if(1!=$re)
            return null;
        $con=$con." @来自微信";
        $result=content::insertat_stark($config, $con,5,2,$sendto);
        return $result;
    }
    
    //强制录入信息
    static function incon_c(config $config,$con,$sendto=NULL,$before=null){
        if(!empty($before))
            $con=$before."@".$con." @来自微信";
        else
            $con="@".$con." @来自微信";
        $result=content::insertat_stark($config, $con,360,2,$sendto);
        return $result;
    }
    
    
    static function teer($match,$keyword,$re,$msg){
        $result=  preg_match($match, $keyword);
        if(1==$result){
            if(1==$re)
               $contentStr=$msg;
            else
               $contentStr="发送错误~再发一次呗~";
        }
        return $contentStr;
    }
    
    static function matime($time){
        $now=  time();
        $re=$now-$time;
        if($re<60){
            return "刚刚";
        }
        elseif($re>60 && $re<3600){
            $c=intval($re/60);
            return $c."分钟前";
        }
        elseif($re>3600 && $re<3600*24){
            $c=intval($re/3600);
            return $c."小时前";
        }
        else{
            return date("m月d日H:i:s",$time);
        }
    }
    
    static function misover($words){
        $match="/@来自微信/i";
        $replace="";
        return preg_replace($match,$replace, $words);
    }
    
     //取出正则中所需
        static function get_arst($str,$arr){
            if (!is_array($arr)){
                if(empty($arr))
                    return NULL;
                $match="/^$arr\s";
                $result=  preg_match($match, $str);
                if(1==$result){
                    return $arr;
                }
            }
            else
            foreach ($arr as $a){
                $match="/^$a\s/";
                $result=  preg_match($match, $str);
                if(1==$result){
                    return $a;
                }
            }
        }
}

?>
