<?php
$myid=data_use::get_usr('userid');
data_use::register_static_set('pic_'.$myid, NULL);
data_use::register_static_set('audi_'.$myid, NULL);
data_use::register_static_delete('pic_'.$myid);