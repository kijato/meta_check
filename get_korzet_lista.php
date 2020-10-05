<?php
	
	$configs = include ( 'config.php' );
	
	foreach ( array_keys ( $configs->korzetek ) as $key ) {
		echo "<option value='$key'>$key</option>\n";
	}
	
?>
