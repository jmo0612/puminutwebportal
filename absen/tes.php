<?php
	include '../konek.php';
	include "../module.php";
	
	$d=new JmoDateTime(date("Y-m-d H:i:s"),"Y-m-d H:i:s");
	echo $d->toString();
	
	
	
	$sqlLast="select * from tb_absen_update where id_update='1'";
	$resLast=$con->query($sqlLast);
	$dLast=null;
	if($resLast->num_rows==0){
		$sqlUpLast="insert into tb_absen_update(id_update,last_update) values('1','".$d->toString()."')";
		echo $sqlUpLast;
		$con->query($sqlUpLast);
	}
	$resLast=$con->query($sqlLast);
	if($resLast->num_rows>0){
		$rowLast=$resLast->fetch_assoc();
		$dLast=new JmoDateTime($rowLast["last_update"],"Y-m-d H:i:s");
	}
	
	//echo $dLast->toString();
	
	$subM=$d->toString("");
	
	$dd=date_add($d,date_interval_create_from_date_string("-30 minutes"));
	
	echo $dd->toString();
	
	

?>