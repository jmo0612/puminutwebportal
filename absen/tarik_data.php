<?php
	include '../konek.php';
	include "../module.php";
	//include "../master.php";
	include("parse.php");
	$IP="192.168.1.8";
	$Key="0";
	
	//$d=new JmoDateTime("2019-01-01 00:00:00","Y-m-d H:i:s");
	
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
	}else echo "gagal";
	
	
	$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer=explode("\r\n",$buffer);
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$DateTime=Parse_Data($data,"<DateTime>","</DateTime>");
		//echo $DateTime;
		$dtx=(string)$DateTime;
		$Verified=Parse_Data($data,"<Verified>","</Verified>");
		$Status=Parse_Data($data,"<Status>","</Status>");
		if(strlen($DateTime)>0){
			$d=new JmoDateTime($DateTime,"Y-m-d H:i:s");
			$id=$PIN."_".$d->toString("YmdHis");
			$res=$con->query("select * from tb_absen_attlog where id_log='".$id."'");
			if($res->num_rows==0){
				$sql="insert into tb_absen_attlog(id_log,id_thl,verified,time_second,status) VALUES('".$id."','".$PIN."','".$Verified."','".$d->toString("Y-m-d H:i:s")."','".$Status."')";
				//echo $sql;
				$con->query($sql);
			}
		}
		
	}
	echo "selesai";
	
?>

