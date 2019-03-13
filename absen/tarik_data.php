<?php
	include 'konek.php';
	include "jmo_datetime.php";
	include("parse.php");
	$Key=0;
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
	}else echo "Koneksi Gagal";
	
	
	$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer=explode("\r\n",$buffer);
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$DateTime=Parse_Data($data,"<DateTime>","</DateTime>");
		$Verified=Parse_Data($data,"<Verified>","</Verified>");
		$Status=Parse_Data($data,"<Status>","</Status>");
		$d=new JmoDateTime($DateTime,"Y-m-d H:i:s");
		$id=$PIN."_".$d->toString("YmdHis");
		$res=$con->query("select * from tb_absen_attlog where id_log='".$id."'");
		if($res->num_rows==0){
			$sql="insert into tb_absen_attlog(id_log,id_thl,verified,time_second,status) VALUES('".$id."',(int)$PIN,(int)$Verified,'".$d->toString("Y-m-d H:i:s")."',(int)$Status)";
			$con->query($sql);
		}
	}
	
?>

