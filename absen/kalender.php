<?php

	$curThn=dateYearInt(dateDBFormat(date_format(date_create(date("Y-m-d")),"Y-m-d")));
	
	if(getPost("kYr")){
        $curThn=getPost("kYr");
    }

    $deltaMonth=0;
    if(getGet("dMn")){
        $deltaMonth=getGet("dMn");
    }

    $mInterval= new DateInterval("P".abs($deltaMonth)."M");
    if($deltaMonth<0)$mInterval->invert=abs($deltaMonth);

    $curDate=date_create($curThn."-01-01");
	
	
    if($deltaMonth!=0)date_add($curDate,$mInterval);

    $curDateDBStr=dateDBFormat(date_format($curDate,"Y-m-d"));

    //echo dateMonthInt($curDateDBStr);

    function findMonth($next=true){
        global $curDateDBStr,$con,$deltaMonth;
        $ret=$deltaMonth;
        $sql="select * from tb_absen_jam_kerja where is_libur='0' and Year(tgl_kerja)='".dateYearInt($curDateDBStr)."' and Month(tgl_kerja) > '".dateMonthInt($curDateDBStr)."' order by tgl_kerja asc";
        if($next==false)$sql="select * from tb_absen_jam_kerja where is_libur='0' and Year(tgl_kerja)='".dateYearInt($curDateDBStr)."' and Month(tgl_kerja) < '".dateMonthInt($curDateDBStr)."' order by tgl_kerja desc";
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
	
	if(getPost("genThn")){
		
	}
    
?>



<div  class="page-header" align="center">
	<div align="center">
		<h1>Kalender Kerja</h1>
		<form class="form-inline">
		  <div class="form-group mb-2">
			<label for="kalThn" class="sr-only">Tahun</label>
			<input type="number" value="<?php echo $curThn; ?>" class="form-control" id="kalThn" placeholder="Tahun" min="1">
		  </div>
		  <button type="submit" class="btn btn-primary mb-2">Buat Kalender</button>
		</form>
	</div>
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
    
</div>

<table class="table table-striped table-bordered table-fix">
    <thead>
        <tr>
            <th>Tgl</th>
            <th>Hari</th>
            <th>Check-in</th>
			<th>Check-out</th>
			<th>Ket</th>
			<th>Libur?</th>
        </tr>
    </thead>
    <tbody>
<?php
    if($res->num_rows>0){
        while($row=$res->fetch_assoc()){
            //echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
            ?>
        <tr>
            <th scope="row"><?php echo dateDayInt($row["tgl_kerja"]); ?></td>
            <td><?php echo dateDayWeekMin($row["tgl_kerja"]); ?></td>
            <td><?php echo dateClockMin($row["jam_masuk"]); ?></td>
			<td><?php echo dateClockMin($row["jam_keluar"]); ?></td>
			<td><?php echo $row["ket_tgl_kerja"]; ?></td>
			<td><?php echo $row["is_libur"]; ?></td>
        </tr>    
            <?php
        }
    }else{
?>

<form>
	<input type="hidden" id="genThn" value="<?php echo $curThn;?>">
  <div class="form-group row">
    <div>Hari Libur:</div>
	
	
	
	
	
	
	
	
    <div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hMing" >
        <label class="form-check-label" for="gridCheck1">
          Minggu
        </label>
      </div>
    </div>
	<div class="form-group row">
		<label for="jCiMing" class="col-sm-2 col-form-label">Check-in Jam</label>
		<div class="col-sm-2">
			<select class="form-control" id="jCiMing" value="08">
				<?php
					for($i=0;$i<24;$i++){
						echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
					}
				?>
			</select>
		</div>
		
	</div>
	<div class="form-group row">
		<label for="mCiMing">Menit</label>
		<select class="form-control" id="mCiMing" value="08">
			<?php
				for($i=0;$i<60;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	<div class="form-group row">
		<label for="jCoMing">Check-out Jam</label>
		<select class="form-control" id="jCoMing" value="08">
			<?php
				for($i=0;$i<24;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	<div class="form-group row">
		<label for="mCoMing">Menit</label>
		<select class="form-control" id="mCoMing" value="08">
			<?php
				for($i=0;$i<60;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	
	
	
	
	
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hSen">
        <label class="form-check-label" for="gridCheck1">
          Senin
        </label>
      </div>
    </div>
	<div class="form-group row">
		<label for="jCiSen">Check-in Jam</label>
		<select class="form-control" id="jCiSen" value="08">
			<?php
				for($i=0;$i<24;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	<div class="form-group row">
		<label for="mCiSen">Menit</label>
		<select class="form-control" id="mCiSen" value="08">
			<?php
				for($i=0;$i<60;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	<div class="form-group row">
		<label for="jCoSen">Check-out Jam</label>
		<select class="form-control" id="jCoSen" value="08">
			<?php
				for($i=0;$i<24;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	<div class="form-group row">
		<label for="mCoSen">Menit</label>
		<select class="form-control" id="mCoSen" value="08">
			<?php
				for($i=0;$i<60;$i++){
					echo "<option>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
				}
			?>
		</select>
	</div>
	
	
	
	
	
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hSel">
        <label class="form-check-label" for="gridCheck1">
          Selasa
        </label>
      </div>
    </div>
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hRab">
        <label class="form-check-label" for="gridCheck1">
          Rabu
        </label>
      </div>
    </div>
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hKam">
        <label class="form-check-label" for="gridCheck1">
          Kamis
        </label>
      </div>
    </div>
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hJum">
        <label class="form-check-label" for="gridCheck1">
          Jumat
        </label>
      </div>
    </div>
	<div class="col-sm-10">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="hSab">
        <label class="form-check-label" for="gridCheck1">
          Sabtu
        </label>
      </div>
    </div>
	
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary">Generate</button>
    </div>
  </div>
</form>


<?php
	}
?>    
    </tbody>
</table>
     