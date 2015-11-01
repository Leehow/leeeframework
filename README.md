# LeeFramework

### mysql部分
<p>将Leeframework.sql导入数据库中(怎么导自己百度)</p>
<p>修改根目录下的mysql_config.php设置自己的数据库信息</p>

### 数据表的字段
<p>数据库是树型结构</p>
<ul>
<li>TABLE                   数据表的表名</li>
<li>ID                      数据的id</li>
<li>UPID                    数据的上级id</li>
<li>AUTHOR                  录入数据的作者</li>
<li>KIND                    数据的种类</li>
<li>CONTENT                 数据的内容</li>
<li>TIME                    数据最后修改时间</li>
<li>CREATETIME              数据录入的时间</li>
<li>CHANGETIME              数据最后修改的时间</li>
</ul>

### 其他

加几个重要的全局变量
<p>usr_use::checklogin();                                  //验证用户是否登录</p>
<p>data_use::register_get('tkid');                         //获取tkid</p>
<p>data_use::get_usr('userid');                            //获取本用户id</p>


内容类别输出的时候用散列表加用户id*2的方法加密，最后回到搜索的时候除以2减去用户id得到散列id
lib/md5.php
modul/change_psw.php
以上两个文件不是通用文件,创建新项目时需要重新拷贝
# 
# LeeFramework使用手册
一般情况下只需要在modul中添加你需要的模块,然后用js调用到页面当中
### 举个简单的栗子:
在modul文件夹中建立一个insert.php文件,作用是将数据传入数据库当中
<br />

lee::lib_load("cont");  
<br />
//用这句话先载入cont组件,组件位置在core/lib/里面,cont组件主要作用是获取和存储内容型数据,比如一个blog的文章,而用户信息处理则不用这个组件存取.具体参数后面回讲
<br />
<br />
$content    = $_POST['content'];     <br />//获取内容,将数据存在$content中<br /><br />
content::insert($content,"kind");  <br />//将数据存入数据库,kind是数据的类别,类别名称可以自定义,方便管理使用.例如blog中可以存储一个数据类别是wenzhang(文章),
riji(日记).尽量用英文命名更加高速快捷
<br /><br />
好了,那么这个存储数据进数据库的模块就写好了,3行搞定,有木有很简单高效,那么完美来介绍下各个组件

<br /><br />
## cont组件
cont组件为静态类直接可以用content::来加载,本组件作用用于查看和修改数据
<br />
### content::select($kind_con, $kind, $page, $pagesize, $where, $ordercolumns, $deasc)
content::select(数据类别, 从属数据类别(可数组), 页码(默认0第一页), 每页数据数量(默认30), $where(sql语句里的where), $ordercolumns(sql语句里的order的字段), $deasc(排序方式asc或desc));
<br /><br />获取数据函数,除了前两个参数其他都可以为空.由于数据库的结构使得获取数据时需要同时载入从属数据,比如blog中文章载入后需要载入浏览量,回复量等从属数据
<br />
<br />
### content::select_simple($kind, $page, $pagesize, $where, $ordercolumns, $deasc);
只载入一个类别的数据,比如blog中只有文章没有点击量回复量等从属数据.
### content::check($where)
只通过where来获取数据
### content::insert($content,$kind,$upid,$limitwords);
content::insert(要录入数据库的数据,类别,父id(当此类别为从属类别时使用),字符数限制(默认1000字符))
<br /><br />这个函数是将数据录入数据库,很简单不解释,但仅仅登录过的用户才能使用
### content::insert_sudo($content,$author,$kind, $upid, $limitwords);
$author是作者,也就是录入数据的用户.这个函数可以所有用户使用,无论是否登录过
### content::change($conid,$content,$columns);
content::change(要修改的id,要修改的内容,字段);
<br />修改数据不解释,同样需要登录后使用
### content::change_sudo($conid,$content,$where,$columns);
这个也是所有用户都可以使用,但是加了个where参数
### content::delete($conid,$upid,$kind)
content::delete(内容的id,父id,类别)
<br />需要删除的位置,这个也容易理解

<br /><br />
## reply组件
reply组件用来查看和修改回复
### reply::select($cid, $page, $pagesize, $ordercolumns, $deasc)
查看回复函数.$cid是内容的id,这是哪个内容的回复就是哪个内容的id.其他参数和cont组件一致,可以参考上面
### reply::insert($content,$cid,$limitwords)
录入回复函数,参数同样根cont一致
### reply::change($reid,$content)
修改回复内容
### reply::delete($reid)
删除回复内容

<br /><br />
## usr组件
usr组件用于管理用户信息,针对用户usr表,与data表无关
### usr::register($usr,$psw)
注册用户,仅录入到usr表,两个参数分别是用户名和密码
### usr::change_psw($usr,$psw)
修改密码,参数分别是用户名和新密码
### usr::login($usr,$psw)
用户注册,同样用户名密码
### usr::logout()
注销用户

<br /><br />
## user_manage组件
user_manage组件用于用户扩展信息的管理
### user_manage::select($kind, $userid, $page, $pagesize)
user_manage::select(类别, 用户id, 页码, 每页多少数据)
<br />可以载入用户扩展信息,比如用户名称性别等等,$kind可以为数组,如果没有$kind则载入所有用户扩展信息,若没有$userid则载入自己的用户信息
### user_manage::create($userid)
创建一个新用户的所有扩展信息,$userid是用户的id,没有则创建登录用户的信息.扩展信息项目在kern/core/lib/config.php中.可以直接修改
### user_manage::change($kid,$value)
修改id为$kid的信息
### user_manage::delete()
删除登录用户的所有信息(慎用)
### user_manage::change_admin($userid,$kid,$value)
未登录状态下的用户信息修改
### user_manage::delete_admin($userid)
未登录状态下用户信息删除(更慎用)

<br /><br /><br /><br />

## 核心组件
核心组件包括以下组件,位置在./kern/core/文件当中
<ul>
<li>class_load.php</li>
<li>data_use.php</li>
<li>sql_use.php</li>
<li>sql_use_k.php</li>
</ul>
<br />
class_load.php作用是文件系统,文件的位置的处理
<br />
data_use.php对数据的一些简单处理
<br />
sql_use.php和sql_use_k.php是最重要的!!!它们作用是数据库的交互,执行差删改查以及一些常用库函数
<br />
<br />
## sql_use_k组件
这个组件的作用是对数据进行差删改查等简单操作,
<p>先介绍简单用法</p>
<ul>
<li>sql_use_k::select("beutifulgirl");           选取类别为beutifulgirl的数据,限制数量30,ID倒叙列出</li>
<li>sql_use_k::insert("good","name");            加入一条新内容,类别为name,值为good</li>
<li>sql_use_k::update("sfdsd",1232);             修改id为1232的值为sfdsd</li>
<li>sql_use_k::delete(1232);                     删除id为1232的数据</li>
<li>sql_use_k::add_one(123);                     给id为123的数字型数据+1</li>
<li>sql_use_k::del_one(123);                     给id为123的数字型数据-1</li>
</ul>
### select_c($columns,$where,$page,$pagesize,$order);        
以数据项目来选取
### select_w($where,$page,$pagesize);                        
以where决定选取
### select($kind,$where,$page,$pagesize,$order);             
以内容种类决定选取
### insert($value,$kind,$upid,$author);                      
插入函数
### update_w($columns,$value,$where,$author);                
where来修改数据
### update($value,$id,$kind,$upid,$author,$where);           
根据id或者其他项目来修改数据
### delete_w($where,$author);                                
根据where或者作者来删除数据
### delete($id,$kind,$upid,$author,$where);                  
根据id,种类或其他来删除数据
### add_one($id,$kind,$upid,$author,$where);                 
增加1
### del_one($id,$kind,$upid,$author,$where);                 
减去1




