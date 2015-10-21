<?php
class img_mak {
	//头像图片剪裁
	static function clip_topic($width,$height,$pic,$location='center',$domain){
                $s = new SaeStorage();	
                $img = new SaeImage();
		$w=$width;
		$h=$height;
                if(!$img_data = $s->read($domain,$pic)){
                    echo $domain."&&&&".$pic;
                    die('图片读取失败！');
                }
                $img->setData($img_data);
		//获取预设剪裁高宽比
		$x2 = $h/$w;
		if(!$image_size=$img->getImageAttr())
                    die('获取属性失败！');
		
		//图片实际高宽比
		$x1 = $image_size[1]/$image_size[0];
		
		//图片实际高宽比和预设高宽比之比
		$x12=$x1/$x2;
		
		
		if ($x12<1){						//如果实际图片比预设值高
			
			if ($location=='center')		//用halb值决定其剪裁位置
			$halb=0.5-$x12/2;				//为了将图片居中剪裁算出其halb值
			elseif ($location=='right')
			$halb=1-$x12;
			else 
			$halb=0;
			
			$img->resize(0,$h);				//按预设高度等比缩小
			$img->crop($halb,$x12+$halb,null,null);	//图片居中预设宽度剪裁
		} 
		else{								//如果实际图片比预设值宽
			
			$x21=1/$x12;					//将比例反转
			
			if ($location=='center')		//用halb值决定其剪裁位置
			$halb=0.5-$x21/2;
			elseif ($location=='right')
			$halb=1-$x12;
			else 
			$halb=0;
			
			$img->resize($w);				//按预设宽度等比缩小
			$img->crop(null,null,$halb,$x21+$halb);	//图片中间预设高度剪裁
		}
                if(!$img->improve())
                    die('图片优化失败!');
		$result = $img->exec();
                if ($result == false){
                    echo "剪切失败！";
                    var_dump($img->errno(), $img->errmsg());
                }
                else 
		return $result;
	}
	
	
        //图片剪裁
	static function clip_pic($width,$height,$pic,$domain){
                $s = new SaeStorage();
		$img = new SaeImage();
		$w=$width;
		$h=$height;
                if(!$img_data = $s->read($domain,$pic)){
                    echo $domain."&&&&".$pic;
                    die('图片读取失败！');
                }
                $img->setData($img_data);
		//获取预设剪裁高宽比
		$x2 = $h/$w;
		if(!$image_size=$img->getImageAttr())
                    die('获取属性失败！');
		//图片实际高宽比
                $he=$image_size[1];
                $we=$image_size[0];
		$x1 = $he/$we;
		
		//图片实际高宽比和预设高宽比之比
		$x12=$x1/$x2;
		
		
		
		if ($x12<1){						//如果实际图片比预设值高
                    if($we>$w)
                    $img->resize($w);				//按预设宽度等比缩小
		} 
		else{							//如果实际图片比预设值宽
                    if($he>$h)
                    $img->resize(0,$h);				//按预设高度等比缩小
		}
                $result=$img->exec();
                if ($result == false){
                    echo "剪切失败！";
                    var_dump($img->errno(), $img->errmsg());
                }
                else 
		return $result;
	}
        
        
        
        //获取图片显示，生成小图
	static function show_upload_pic($pic){
		define('TEMP','temp');        	//要上传的缓存storage名称定义
		$s = new SaeStorage();
		$picname = $_FILES[$pic]['name'];
		$picsize = $_FILES[$pic]['size'];
                $temp=$_FILES[$pic]['tmp_name'];
                if(empty($temp))
                    die ("文件没上传完".$picname.$picsize);
//                die($temp.$picsize);
		if ($picname != "") {
			if ($picsize > 5120000) {
				echo '图片大小不能超过2M';
				exit;
			}
			$type = strstr($picname, '.');
			if ($type != ".gif" && $type != ".jpg" && $type != ".png" && $type != ".jpeg") {
				echo '图片格式不对！只支持jpg,png,gif';
				exit;
			}
			
			if(!$_FILES[$pic]['name']) die('没有选择文件!'); //上传文件校验，可以添加文件类型等检验、此处简略处理
			
			$file_name = "pic_".time().rand(100,999);        //定义保存的文件名称
			$ext = end(explode('.',$_FILES[$pic]['name'])); //获得扩展名
			$pic_path = $file_name.'.'.$ext;              //要保存的文件路径+文件名，此处保存在根目录下
			if($s->upload(TEMP, $pic_path, $_FILES[$pic]['tmp_name'])){
			    //裁成500x500大小
			    $poc=array(500,500,'_normal.jpg');
                            //重新写入jpg格式
                            $pic_uname=$file_name.'.jpg';
                            if($ext!='jpg'){
                                $data=$s->read(TEMP,$pic_path);
                                $pics =$s->write(TEMP,$pic_uname , $data);
                                if(!$pics)
                                    die('图片复写失败！');
                                unset($pics);
                            }
			    //将图片剪裁之后存入storage
                            $pict = $file_name.$poc[2];
                            if($ext!="gif"){ 
                                unset($data);
//                                $data = self::clip_pic($poc[0],$poc[1],$pic_uname,TEMP);
                                $data = self::clip_topic($poc[0],$poc[1],$pic_uname,'center',TEMP);
                            }
			    $pics =$s->write(TEMP, $pict, $data);
                            if(!$pics)
                                die('图片剪切写入失败！');
			}
                        else{
                            $emsg='零时文件上传失败！'.$s->errmsg();
                            sae_debug($emsg);
                            die($emsg);
                        }
			//将三种地址放入数组输出
			$out_pic=array(
				'pic_name'	=>$file_name,
				'pic_url'	=>$pics
			);
			
			return $out_pic;
		}
	}
        
        
        	//图片上传，三种格式
	static function upload_pic($pic_name){
		define('DOMAIN','upcon');        //要上传的storage名称定义
		$temp='temp';
		$s = new SaeStorage();
		
		$poc=array('.jpg','_normal.jpg');
		foreach ($poc as $p){
			$pic  = $pic_name.$p;
                        $img = $s->read($temp ,$pic);
                        if(!$img)
                            die('图片读取失败!');
			if ($s->write(DOMAIN, $pic, $img)==false)
                            var_dump($s->errno(), $s->errmsg());
			unset($pic);unset($pics);unset($img);
		}
		
		self::delete_pic($pic_name);		
		return 1;
	}
        
        
        static function delete_pic($pics,$storage='temp'){
		define('TEMP',$storage);	//缓存storage
		if (is_array($pics)==true){
		$file_name = $pics['pic_name'][0];
		}
		else 
		$file_name=$pics;
		
		$pic_big = $file_name.'.jpg';
		$pic_midde = $file_name.'_normal.jpg'; 
		
		$s = new SaeStorage();
		$s->delete(TEMP,$pic_big);
		$s->delete(TEMP,$pic_midde);
		return 1;
	}
        
        
	
	//获取图片显示，生成三种格式
	static function show_upload_topic($pic){
		define('TEMP','topic');        	//要上传的缓存storage名称定义
		$s = new SaeStorage();
		$picname = $_FILES[$pic]['name'];
		$picsize = $_FILES[$pic]['size'];
		if ($picname != "") {
			if ($picsize > 5120000) {
				echo '图片大小不能超过2M';
				exit;
			}
			$type = strstr($picname, '.');
			if ($type != ".gif" && $type != ".jpg" && $type != ".png" && $type != ".jpeg") {
				echo '图片格式不对！只支持jpg,png,gif';
				exit;
			}
			
			if(!$_FILES[$pic]['name']) die('没有选择文件!'); //上传文件校验，可以添加文件类型等检验、此处简略处理
			
			$file_name = "topic_".time().rand(100,999);        //定义保存的文件名称
			$ext = end(explode('.',$_FILES[$pic]['name'])); //获得扩展名
			$pic_path = $file_name.'.'.$ext;              //要保存的文件路径+文件名，此处保存在upload/目录下
			if($s->upload(TEMP, $pic_path, $_FILES[$pic]['tmp_name'])){
				
			    //裁成三种大小
			    $poc=array(
			    array(200,200,'_big.jpg'),
			    array(64,64,'_midde.jpg'),
			    array(22,22,'_small.jpg')
			    );
				
			    //将图片剪裁之后存入storage
			    foreach ($poc as $p){
                                $pict = $file_name.$p[2];
			    	$data = self::clip_topic($p[0],$p[1],$pic_path,"center",TEMP);
			    	$url=$s->write(TEMP, $pict, $data);
                                    if ($url==FALSE)
                                        die('图片剪裁写入失败！');
			    	$pics[] = $url;
			    	unset($data);unset($pict);
			    }
                            //如果不是jpg格式则重新写入jpg格式  
                            if($ext!='jpg'){
                                $data=$s->read(TEMP,$pic_path);
                                if(!$s->write(TEMP, $file_name.'.jpg', $data))
                                        die('图片写入失败！');
                            }
                            
                            
				//获取三个图片地址
				$pics_b = $pics[0];
				$pics_m = $pics[1];
				$pics_s = $pics[2];
				
			}
                        else {
                            $emsg='上传零时文件失败'.$s->errmsg();
                            sae_debug($emsg);
                            die($emsg);
                        }
			
			//将三种地址放入数组输出
			$out_pic=array(
				'pic_name'	=>array($file_name,$ext),
				'pic_url'	=>array($pics_b,$pics_m,$pics_s)
			);
			
			return $out_pic;
		}
	}
	
	
	
	//图片上传，三种格式
	static function upload_topic($pic_name,$change=0,$oldpic=null){
		define('DOMAIN','upload');        //要上传的storage名称定义
		$temp='temp';
		$s = new SaeStorage();
		
		$poc=array('_big.jpg','_midde.jpg','_small.jpg','.jpg');
		foreach ($poc as $p){
			$pic  = $pic_name.$p;
                        $img = $s->read($temp ,$pic);
			if ($s->write(DOMAIN, $pic, $img)==false){
                            echo "图片写入失败！请重新上传，如果还是失败请联系客服！但是不要放弃对咱们原创的希望";
                            var_dump($s->errno(), $s->errmsg());
                        }
			unset($pic);unset($pics);unset($img);
		}
		
		self::delete_topic($pic_name);		
                if($change==1)
                    self::delete_topic($oldpic,'jpg','upload');
                
                
		unset($s);
		
		return 1;
	}
	
	
	static function delete_topic($pics,$ext='jpg',$storage='temp'){
		define('TEMP',$storage);	//缓存storage
		if (is_array($pics)==true){
		$file_name	=$pics['pic_name'][0];
		$ext		=$pics['pic_name'][1];
		}
		else 
		$file_name=$pics;
		
		$pic_big = 	 $file_name.'_big.jpg';
		$pic_midde = $file_name.'_midde.jpg'; 
		$pic_small = $file_name.'_small.jpg'; 
		$pic_y =	 $file_name.".".$ext;
		
		$s = new SaeStorage();
		$s->delete(TEMP,$pic_big);
		$s->delete(TEMP,$pic_midde);
		$s->delete(TEMP,$pic_small);
		if($s->delete(TEMP,$pic_y)==false){
			$s->delete(TEMP,$file_name.".png");
			$s->delete(TEMP,$file_name.".gif");
		}
		return 1;
	}
	
	
	
}
