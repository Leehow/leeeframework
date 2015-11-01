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


## cont组件
cont组件为静态类直接可以用content::来加载
<br />
### content::select($kind_con, $kind, $page, $pagesize, $where, $ordercolumns, $deasc)
content::select(数据类别, 从属数据类别(可数组), $page=0页码(默认0第一页), $pagesize每页数据数量(默认30), $where(sql语句里的where), $ordercolumns(sql语句里的order的字段), $deasc(排序方式asc或desc));
<br />获取数据函数,除了前两个参数其他都可以为空.由于数据库的结构使得获取数据时需要同时载入从属数据,比如blog中文章载入后需要载入浏览量,回复量等从属数据
<br />
<br />
### content::select_simple($kind, $page, $pagesize, $where, $ordercolumns, $deasc);
<br />只载入一个类别的数据,比如blog中只有文章没有点击量回复量等从属数据.






