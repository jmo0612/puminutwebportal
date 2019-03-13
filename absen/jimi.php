<?php
    include 'konek.php';
	include("parse.php");	
	
	$IP="192.168.1.8";
	$Key="0";
	
	
?>
<html>
<head><title>Jimi</title></head>
<body bgcolor="#caffcb">

<H3>Upload THL</H3>

<?

//uploadThl("6","jimot");

$res=$con->query("select * from tb_absen_thl");
if($res->num_rows>0){
	while($row=$res->fetch_assoc()){
		//echo "jimi";
		$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
		//stream_set_timeout ( $Connect , 300 ); 
		if($Connect){
			$id=$row["id_thl"];
			$nama=$row["nm_thl"];
			$soap_request="<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN>".$id."</PIN><Name>".$nama."</Name></Arg></SetUserInfo>";
			$newLine="\r\n";
			fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
			fputs($Connect, "Content-Type: text/xml".$newLine);
			fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
			fputs($Connect, $soap_request.$newLine);
			$buffer="";
			while($Response=fgets($Connect, 1024)){
				$buffer=$buffer.$Response;
			}
			//fclose($Connect);
		}else echo "Koneksi Gagal";
		
		$buffer=Parse_Data($buffer,"<Information>","</Information>");
		echo "<B>Result:</B><BR>";
		echo $buffer;
		//$buffer=null;
		
		//sleep();
		//fclose($Connect);
		//break;
	}
	
	echo "====SELESAI====";
}
?>



</body>
</html>

