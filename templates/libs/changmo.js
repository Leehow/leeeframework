function changmo(str){
    function rep($1,$2,$3,$4){
        var nn=Math.floor(Math.random()*10);
        var id='c'+(new Date).getTime()+nn+nn;
        chanmof(id,$2,$3,$4);
        return "<c id='"+id+"'>"+$2+"</c>";
    }
            
            
     function remor($1,$2,$3,$4,$5){
         setTimeout(function() {
                      var id='c'+(new Date).getTime();
                      chanmof(id,$2,$3,$4);
                      return "<c id='"+id+"'>"+$2+"</c>";
                  }, $5);
     }
            //c[需要替换的字符,替换的字符,周期,开始时间]
            var re = /c\[([^,]+),([^,]+),(\d+),(\d+)\]/;
            if(re.exec(str)){
                return str.replace(re,remor);
            }
            //c[需要替换的字符,替换的字符,周期]
            var re1 = /c\[([^,]+),([^,]+),(\d+)\]/;
            return str.replace(re1,rep);
}

function chanmof(id,word,reword,time){
            var twtime=time*2;
            var ttime=(new Date).getTime();
            var sti = setInterval(function(){
                $("#"+id).html(word);
                setTimeout(function() {
                    $("#"+id).html(reword);
                }, time); 
                
                if((ttime+65000)<(new Date).getTime()){
                    clearInterval(sti);
                }
                    
            }, twtime);
        }