<?php
 $dbconn = pg_connect("host=localhost dbname=fosstrans user=redhog password=saltgurka")
	    or die('Could not connect: ' . pg_last_error());
?>
