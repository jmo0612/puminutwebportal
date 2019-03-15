<?php
	include 'konek.php';
	include "module.php";
	include("absen/parse.php");
	$IP="192.168.1.8";
	$Key="0";
	$failed=false;
	
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
	}else{
		$failed=true;
		echo "gagal";
	}
	
	if(!$failed){
		$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
		$buffer=explode("\r\n",$buffer);
		
		
		$dNow=new JmoDateTime(date("Y-m-d H:i:s"),"Y-m-d H:i:s");
		$sqlLast="select * from tb_absen_update where id_update='1'";
		$resLast=$con->query($sqlLast);
		$dLast=null;
		if($resLast->num_rows==0){
			$sqlUpLast="insert into tb_absen_update(id_update,last_update) values('1','".$dNow->toString()."')";
			$con->query($sqlUpLast);
		}
		$resLast=$con->query($sqlLast);
		if($resLast->num_rows>0){
			$rowLast=$resLast->fetch_assoc();
			$dLast=new JmoDateTime($rowLast["last_update"],"Y-m-d H:i:s");
		}
		
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
					$sqlCekIlegal="select * from tb_absen_attlog_ilegal where id_log='".$id."'";
					$resCekIlegal=$con->query($sqlCekIlegal);
					if($resCekIlegal->num_rows==0){
						//exclude future
						$dNowTmp=new JmoDateTime(date("Y-m-d H:i:s"),"Y-m-d H:i:s");
						//$dNowTmp=date_add($dNowTmp,date_interval_create_from_date_string("-30 minutes"));
						if($d>$dNowTmp){
							$con->query("insert into tb_absen_attlog_ilegal(id_log,id_thl,verified,time_second,status) values('".$id."','".$PIN."','".$Verified."','".$d->toString("Y-m-d H:i:s")."','".$Status."')");
						}else{
							$dLast=date_add($dLast,date_interval_create_from_date_string("-30 minutes"));
							if($d<$dLast){
								$con->query("insert into tb_absen_attlog_ilegal(id_log,id_thl,verified,time_second,status) values('".$id."','".$PIN."','".$Verified."','".$d->toString("Y-m-d H:i:s")."','".$Status."')");
							}else{
								//LEGAL
								$sql="insert into tb_absen_attlog(id_log,id_thl,verified,time_second,status) VALUES('".$id."','".$PIN."','".$Verified."','".$d->toString("Y-m-d H:i:s")."','".$Status."')";
								//echo $sql;
								$con->query($sql);
							}
						}
					}
				}
			}
			
		}
		
		$dNow=new JmoDateTime(date("Y-m-d H:i:s"),"Y-m-d H:i:s");
		$sqlUpLast="update tb_absen_update set last_update='".$dNow->toString()."'";
		$con->query($sqlUpLast);
		echo "selesai";
	}
	
	
	
	
?>

