# LeeFramework

# mysql部分
<p>将Leeframework.sql导入数据库中(怎么导自己百度)</p>
<p>修改根目录下的mysql_config.php设置自己的数据库信息</p>

# 数据表的字段
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

# 其他

加几个重要的全局变量
<p>usr_use::checklogin();                                  //验证用户是否登录</p>
<p>data_use::register_get('tkid');                         //获取tkid</p>
<p>data_use::get_usr('userid');                            //获取本用户id</p>


内容类别输出的时候用散列表加用户id*2的方法加密，最后回到搜索的时候除以2减去用户id得到散列id
lib/md5.php
modul/change_psw.php
以上两个文件不是通用文件,创建新项目时需要重新拷贝






