<?php
class audio_mak {
    function writelog_debug($msg)
        {
                sae_set_display_errors(false);//关闭信息输出
                if (is_array($msg))
                {
                        $msg = implode(",", $msg);
                }
                sae_debug("[abcabc]".$msg);//记录日志
                sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
        }
        
    static function upradio($radio){
		$s = new SaeStorage();
		$mname = $_FILES[$radio]['name'];
		$msize = $_FILES[$radio]['size'];
		if ($mname != "") {
			if ($msize > 10240000) {
				echo '图片大小不能超过10M';
				exit;
			}
			$type = strstr($mname, '.');
			if ($type != (".mp3")) {
				echo '对不起！只支持mp3格式,如果是这几种格式请去掉处文件标识符以外的点"."';
				exit;
			}
			
			if(!$_FILES[$radio]['name']) die('没有选择文件!'); //上传文件校验，可以添加文件类型等检验、此处简略处理
			
			$file_name = "m_".time().rand(100,999);        //定义保存的文件名称
                        $m_path = $file_name.$type;              //要保存的文件路径+文件名，此处保存在根目录下
                        $link=$s->upload('upcon', $m_path, $_FILES[$radio]['tmp_name']);
			if($link){
                                
                                $out_radio=array(
				'm_name'	=>$file_name,
				'm_url'         =>$link
                                );
                                echo json_encode($out_radio);
                                }
                                else{
                                    $emsg=$s->errmsg();
                                    $this->writelog_debug($emsg);
                                    die('上传零时文件失败！');
                                }
                  
                }   
    }

}
?>
