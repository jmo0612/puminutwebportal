<?php

	

	$arrDay=array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
	$arrDayCb=array("cbMing","cbSen","cbSel","cbRab","cbKam","cbJum","cbSab");
	$arrDayCiH=array("ciHMing","ciHSen","ciHSel","ciHRab","ciHKam","ciHJum","ciHSab");
	$arrDayCiM=array("ciMMing","ciMSen","ciMSel","ciMRab","ciMKam","ciMJum","ciMSab");
	$arrDayCoH=array("coHMing","coHSen","coHSel","coHRab","coHKam","coHJum","coHSab");
	$arrDayCoM=array("coMMing","coMSen","coMSel","coMRab","coMKam","coMJum","coMSab");
	//echo getPost("genThn");
	if(getPost("genThn")){
		$wDate=date_create(getPost("genThn")."-01-01");
		$wDateDBStr=date_format($wDate,"Y-m-d");
		$sqlUp="insert into tb_absen_jam_kerja(tgl_kerja,is_libur,jam_masuk,jam_keluar,ket_tgl_kerja) values ";
		while(dateYearInt($wDateDBStr)==getPost("genThn")){
			
			$theDayInd=dateDayWeekInt(date_format($wDate,"Y-m-d"));
			if($theDayInd==7)$theDayInd=0;
			
			$sqlUp.="('".$wDateDBStr."','".(getPost($arrDayCb[$theDayInd])?0:1)."','".date_format(date_create($wDateDBStr." ".getPost($arrDayCiH[$theDayInd]).":".getPost($arrDayCiM[$theDayInd]).":00"),"Y-m-d H:i:s")."','".date_format(date_create($wDateDBStr." ".getPost($arrDayCoH[$theDayInd]).":".getPost($arrDayCoM[$theDayInd]).":00"),"Y-m-d H:i:s")."',''),";
			//$sqlUp.="values('"."XXX"."','".(getPost($arrDayCb[$theDayInd])?1:0)."','YYY','".date_format(date_create(date_format($wDate,"Y-m-d")." ".getPost($arrDayCoH[$theDayInd]).":".getPost($arrDayCoM[$theDayInd]).":00"),"Y-m-d H:i:s")."','')";
			
			
			$wDate->modify("+1 day");
			$wDateDBStr=date_format($wDate,"Y-m-d");
		}
		$sqlUp=substr($sqlUp,0,-1);
		$con->query($sqlUp);
	}


	$curThn=dateYearInt(dateDBFormat(date_format(date_create(date("Y-m-d")),"Y-m-d")));
	$curBln=dateMonthInt(dateDBFormat(date_format(date_create(date("Y-m-d")),"Y-m-d")));
	//echo getPost("kalThn");
	if(getPost("kalThn")){
			
			$curThn=getPost("kalThn");
	}	
		

    $deltaMonth=0;
    if(getGet("dMn")){
        $deltaMonth=getGet("dMn");
    }

    $mInterval= new DateInterval("P".abs($deltaMonth)."M");
    if($deltaMonth<0)$mInterval->invert=abs($deltaMonth);

    $curDate=date_create($curThn."-".str_pad($curBln,2,"0",STR_PAD_LEFT)."-01");
	
	
    if($deltaMonth!=0)date_add($curDate,$mInterval);

		$curDateDBStr=dateDBFormat(date_format($curDate,"Y-m-d"));
		
		

    //echo dateMonthInt($curDateDBStr);

    function findMonth($next=true){
				global $curDateDBStr,$con,$deltaMonth;
				//echo $curDateDBStr;
        $ret=$deltaMonth;
        $sql="select * from tb_absen_jam_kerja where Year(tgl_kerja)='".dateYearInt($curDateDBStr)."' and Month(tgl_kerja) > '".dateMonthInt($curDateDBStr)."' order by tgl_kerja asc";
        if($next==false)$sql="select * from tb_absen_jam_kerja where Year(tgl_kerja)='".dateYearInt($curDateDBStr)."' and Month(tgl_kerja) < '".dateMonthInt($curDateDBStr)."' order by tgl_kerja desc";
        $res=$con->query($sql);
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $d0=new JmoDateTime();
            $d0=new JmoDateTime($d0->toString("Y-m-")."01 00:00:00");
            $d1=new JmoDateTime($row["tgl_kerja"]." 00:00:00");
            $d1=new JmoDateTime($d1->toString("Y-m-")."01 00:00:00");
            //echo $d0->toString();
            //echo $d1->toString();
            $ret=$d0->diffInMonths($d1);
        }
        return $ret;
    }

    

    $sql="select *from tb_absen_jam_kerja where Month(tgl_kerja)='".dateMonthInt($curDateDBStr)."' and Year(tgl_kerja)='".$curThn."'";

    //echo $sql;
    $res=$con->query($sql);
	
		$curThn=dateYearInt($curDateDBStr);
		$curBln=dateMonthInt($curDateDBStr);
    
?>



<div  class="page-header" align="center">
	<div align="center">
		<h1>Kalender Kerja</h1>
		<form class="form-inline" id="frmThn" method="post" action="?p=adAl">
		  <div class="form-group mb-2">
			<label for="kalThn" class="sr-only">Tahun</label>
			<input type="number" value="<?php echo $curThn; ?>" class="form-control" id="kalThn" name="kalThn" placeholder="Tahun" min="1">
		  </div>
		  <!-- <button type="submit" class="btn btn-primary mb-2">Buat Kalender</button> -->
		</form>
	</div>
	<?php
    if($res->num_rows>0){
	?>
	<div align="left">
		<nav aria-label="Date navigation">
			<ul class="pagination">
				<li>
					<a href="<?php echo updateUrlGet("dMn",findMonth(false)) ?>" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<li><span id="dtSpanMonth" class="datepicker"><?php echo dateMonth($curDateDBStr); ?></span></li>
				
				<li>
					<a href="<?php echo updateUrlGet("dMn",findMonth()) ?>" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
			</ul>
		</nav>
	</div>
  <?php
    }
	?>  
</div>



<?php
    if($res->num_rows>0){
?>
<table class="table table-bordered table-hover table-fit">
    <thead>
      <tr>
				<th style="text-align:center">Tgl</th>
				<th style="text-align:center">Hari</th>
				<th style="text-align:center">in</th>
				<th style="text-align:center">out</th>
				<th style="text-align:center">Ket</th>
				<th style="text-align:center">Hari<br>Kerja</th>
      </tr>
    </thead>
    <tbody>
<?php
				while($row=$res->fetch_assoc()){
					//echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
					?>
	
				<tr <?php echo ($row["is_libur"])?'class="danger"':''; ?> onclick="window.location='<?php echo getUrl('absen/index.php?p=detHk&edHk='.str_replace('-','',$row['tgl_kerja']).((getGet('dMn'))?'&dMn='.getGet('dMn'):'')); ?>';">
				
						<th style="text-align:center" scope="row"><?php echo dateDayInt($row["tgl_kerja"]); ?></td>
						<td style="text-align:center"><?php echo dateDayWeekMin($row["tgl_kerja"]); ?></td>
						<td style="text-align:center"><?php echo dateClockMin($row["jam_masuk"]); ?></td>
						<td style="text-align:center"><?php echo dateClockMin($row["jam_keluar"]); ?></td>
						<td style="text-align:center"><?php echo $row["ket_tgl_kerja"]; ?></td>
						<td style="text-align:center"><?php echo ($row["is_libur"])?"<span class='glyphicon glyphicon-remove' aria-hidden='true' style='color:red'></span>":"<span class='glyphicon glyphicon-ok' aria-hidden='true' style='color:green'></span>"; ?></td>
				
				</tr>    
				
					<?php
				}
?>
    </tbody>
</table>
<?php
    }else{
?>

<div>Hari Kerja:</div>
<form id="frmGen" method="post">
	<input type="hidden" name="genThn" id="genThn" value="<?php echo $curThn;?>">
	<table>
<?php
		
		for($i=0;$i<7;$i++){
?>
		<tr>
			<td rowspan="2" style="padding:3px;border-left:1px solid;border-top:1px solid;border-bottom:1px solid">
				<input class="form-check-input" type="checkbox" name="<?php echo $arrDayCb[$i];?>" id="<?php echo $arrDayCb[$i];?>" <?php if($i!=0 && $i!=6)echo "checked"; ?> >
			</td>
			<td rowspan="2" style="padding:1px;border-top:1px solid;;border-bottom:1px solid"><?php echo $arrDay[$i]; ?></td>
			<td rowspan="2" style="border-top:1px solid;border-right:1px solid;border-bottom:1px solid">&nbsp;</td>
			<td style="padding:3px;text-align:right;border-top:1px solid">Check-in : </td>
			<td style="padding:3px;border-top:1px solid">Jam</td>
			<td style="padding:3px;border-top:1px solid">
				<select class="form-control" name="<?php echo $arrDayCiH[$i]; ?>" id="<?php echo $arrDayCiH[$i]; ?>" value="<?php if($i==5){echo "07";}else{echo "08";} ?>">
					<?php
						$hh=8;
						if($i==5)$hh=7;
						for($j=0;$j<24;$j++){
							echo "<option>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							if($j==$hh){
								echo "<option selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							}
							
						}
					?>
				</select>
			</td>
			<td style="padding:3px;border-top:1px solid">Menit</td>
			<td style="padding:3px;border-top:1px solid;border-right:1px solid">
				<select class="form-control" name="<?php echo $arrDayCiM[$i]; ?>" id="<?php echo $arrDayCiM[$i]; ?>" value="00">
					<?php
						for($j=0;$j<60;$j++){
							echo "<option>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							if($j==0){
								echo "<option selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							}
						}
					?>
				</select>
			</td>
		</tr>
			
		<tr>
			<td style="padding:3px;text-align:right">Check-out : </td>
			<td style="padding:3px">Jam</td>
			<td style="padding:3px">
				<select class="form-control" name="<?php echo $arrDayCoH[$i]; ?>" id="<?php echo $arrDayCoH[$i]; ?>" value="16">
					<?php
						$hh=16;
						if($i==5)$hh=12;
						for($j=0;$j<24;$j++){
							echo "<option>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							if($j==$hh){
								echo "<option selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							}
						}
					?>
				</select>
			</td>
			<td style="padding:3px">Menit</td>
			<td style="padding:3px;border-right:1px solid">
				<select class="form-control" name="<?php echo $arrDayCoM[$i]; ?>" id="<?php echo $arrDayCoM[$i]; ?>" value="00">
					<?php
						$mm=0;
						if($i==5)$mm=30;
						for($j=0;$j<60;$j++){
							echo "<option>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							if($j==$mm){
								echo "<option selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
							}
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td colspan="8">&nbsp;</td></tr>
<?php
		}
?>
			
	</table>

	
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary">Generate</button>
    </div>
  </div>
</form>


<?php
	}
?>































     