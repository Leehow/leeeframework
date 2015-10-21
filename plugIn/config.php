<?php
class config {

	var $usr_table			= "usr";
	var $usr_id			= "usr_id";
	var $usr_username		= "username";
	var $usr_password		= "password";
	
	var $data_table 		= "data";
        var $data_id                    = "data_id";
	var $data_columns               = array(
                            upid	=> "data_upid",
                            author	=> "data_author",
                            kind	=> "data_kind",
                            content     => "data_content",
                            time	=> "data_time",
                            createtime	=> "data_createtime",
                            changetime	=> "data_changetime"
                    );
								
}
