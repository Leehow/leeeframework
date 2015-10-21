以后一些备注请放在这里！！




我在kern/core/sql_use_new.php是个新文件对sql_use里面的函数根据新数据库的结构进行了重写
但是直接把配置文件里的内容往里放了，以后改config.php的时候别忘了该这里！！！





加两个重要的全局变量
usr_use::checklogin();                                  //验证用户是否登录
data_use::register_get('tkid');                         //获取tkid
data_use::get_usr('userid');                            //获取本用户id



内容类别输出的时候用散列表加用户id*2的方法加密，最后回到搜索的时候除以2减去用户id得到散列id















数据库是树型结构
$config->data_table                     数据表的表名
$config->data_columns[id]               数据的id
$config->data_columns[upid]             数据的上级id
$config->data_columns[author]           录入数据的作者
$config->data_columns[kind]             数据的种类
$config->data_columns[content]          数据的内容
$config->data_columns[time]             数据最后修改时间
$config->data_columns[ctime]            数据录入的时间


