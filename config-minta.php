<?php

# https://stackoverflow.com/questions/14752470/creating-a-config-file-in-php
#
#   $configs = include('config.php');
#	echo json_encode($configs->app_info);
#

return (object) array(

    'korzetek' => array(

		'Baja' => array (
			'username' => 'guest',
			'password' => 'guest',
			'connection_string' => 'localhost/XE',
			'character_set' => 'AL32UTF8'
		),
		'Bácsalmás' => array (
			'username' => 'guest',
			'password' => 'guest',
			'connection_string' => 'localhost/XE',
			'character_set' => 'AL32UTF8'
		),
		'Kalocsa' => array (
			'username' => 'guest',
			'password' => 'guest',
			'connection_string' => 'localhost/XE',
			'character_set' => 'AL32UTF8'
		),
		'Kecskemét' => array (
			'username' => 'guest',
			'password' => 'guest',
			'connection_string' => 'localhost/XE',
			'character_set' => 'AL32UTF8'
		)
	),
	
    'app_info' => array(
        'appName'=>"DATR - META ellenőrző",
        'appURL'=> "http://yourURL/#/"
    )

);

?>
