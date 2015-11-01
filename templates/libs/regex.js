function msg_mark(re,msg){
    var mark='<font color="red">'+re+'</font>';
    var rep = new RegExp(re,"g");
    var new_msg=msg.replace(rep,mark); 
    return new_msg;
}

//替换回车符
function replaceTextarea1(str){
    var reg=new RegExp("\n","g");
    var reg1=new RegExp(" ","g");
//    var reg2=new RegExp("<","g"); 
    str = str.replace(reg,"<br>");
    str = str.replace(reg1,"&nbsp;");
    
    return str;
}


function replaceTextarea2(str){
    var reg=new RegExp("<br>","g");
    var reg1=new RegExp("&nbsp;","g");
    var reg2=new RegExp("zuojiao","g");
    
    str = str.replace(reg,"\n");
    str = str.replace(reg1," ");
    str = str.replace(reg2,"<");
    
    return str;
}

function replaceTextarea3(str){
    var reg=new RegExp("<br>","g");
    var reg1=new RegExp("&nbsp;","g");
    var reg2=new RegExp("zuojiao","g");
    
    str = str.replace(reg," ");
    str = str.replace(reg1," ");
    str = str.replace(reg2,"<");
    
    return str;
}


 function changw(str){
            //改链接，音频，视频
        function andere($1){
            var c=$1;
            alert(c);
            var patrn=/视频@/;
//            audin=/音频@/;
            var bq=/表情@/;
            wb=/网站@/;
            var youtu=/ytb@/;
            //表情
            if(bq.exec(c)){
                alert("b");
                var cn=c.substr(3);
                return '<img src="http://img.baidu.com/hi/bobo/B_'+cn+'.gif"></img>';
            }
            //网站
            if(wb.exec(c)){
                alert("web");
                var cn=c.substr(3);
                wb=/http/;
                if(!wb.exec(cn)){
                    cn="http://"+cn;
                }
                return "<a href='"+cn+"' target='_blank' style='color:gray;'>"+cn+"</a>";
            }
            //视频
            if(patrn.exec(c)){
                var cn=c.substr(3);
                patrn=/http:/;
                if(!patrn.exec(cn)){
                    cn="http://"+cn;
                }
                return '<p align="center"><embed src="'+cn+'" width="100%" /></p>';
            }
            //youtube视频
            if(youtu.exec(c)){
                alert("you");
                cn=c.substr(4);
                youtu=/https:/;
                if(!youtu.exec(cn)){
                    cn="https://"+cn;
                }
                cn=cn.substr(17);
                return '<p align="center"><iframe width="560" height="315" src="https://www.youtube.com/embed/'+cn+'" frameborder="0" allowfullscreen></iframe></p>';
            }
//            音频
//            if(audin.exec(c)){
//                 cn=c.substr(3);
//                 patrn=/http:/
//                 if(!patrn.exec(cn)){
//                     cn="http://"+cn;
//                 }
//                 return '<p align="left"><span class="mp3">'+cn+'</span></p>';
//            }

        }

        var re = /视频@\S+.swf|网站@+\d+|ytb@\d+|表情@\d+/g;
        var newstr=str.replace(re,andere); 
        return newstr;
    }