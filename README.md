# LeeFramework

# mysql部分
将Leeframework.sql导入数据库中(怎么导自己百度)
修改根目录下的mysql_config.php设置自己的数据库信息



加两个重要的全局变量
usr_use::checklogin();                                  //验证用户是否登录
data_use::register_get('tkid');                         //获取tkid
data_use::get_usr('userid');                            //获取本用户id


内容类别输出的时候用散列表加用户id*2的方法加密，最后回到搜索的时候除以2减去用户id得到散列id
lib/md5.php
modul/change_psw.php
以上两个文件不是通用文件,创建新项目时需要重新拷贝









数据库是树型结构
TABLE                   数据表的表名
ID                      数据的id
UPID                    数据的上级id
AUTHOR                  录入数据的作者
KIND                    数据的种类
CONTENT                 数据的内容
TIME                    数据最后修改时间
CREATETIME              数据录入的时间
CHANGETIME              数据最后修改的时间


