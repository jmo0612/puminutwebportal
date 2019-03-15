<?php
	include 'konek.php';
	
	$sql="update tes set updated='".date("Y-m-d H:i:s")."' where id_tes='1'";
	//echo $sql;
	
	$con->query($sql);
?>