<?php

    if(getPost("genThn")){
        $dThn=getPost("genThn");
        $dBln=getPost("genBln");
        $sqlLook="select * from tb_absen_thl";
        $resLook=$con->query($sqlLook);
        if($resLook->num_rows>0){
            $sqlGen="";
            while($rowLook=$resLook->fetch_assoc()){
                if($sqlGen!="")$sqlGen.=", ";
                $sqlGen.="('".$dThn."','".$dBln."','".$rowLook["id_thl"]."','".$rowLook["nm_thl"]."','".$rowLook["non_aktif_thl"]."')";
            }
            if($sqlGen!=""){
                $sqlGen="insert into tb_absen_thl_bulan(thn,bln,id_thl,nm_thl,nonaktif_thl) values".$sqlGen;
                $con->query($sqlGen);
            }
            
        }
    }

    $serverDate=date_create(date("Y-m-d"));
    $serverDateStr=dateDBFormat(date_format($serverDate,"Y-m-d"));

    $deltaMonth=0;
    if(getGet("dMn")){
        $deltaMonth=getGet("dMn");
    }

    $mInterval= new DateInterval("P".abs($deltaMonth)."M");
    if($deltaMonth<0)$mInterval->invert=abs($deltaMonth);

    $curDate=date_create(date("Y-m-d"));
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

    //echo isDate("2019-03-01 00:00:00");

    //echo findMonth(false);

    //$dBln=dateMonthInt($curDateDBStr);
    //$dThn=dateYearInt($curDateDBStr);

    $dBln=dateMonthInt($curDateDBStr);
    $dThn=dateYearInt($curDateDBStr);

    

    $sql="select * from tb_absen_thl_bulan where thn='".$dThn."' and bln='".$dBln."' order by nm_thl asc";

    //echo $sql;
    $res=$con->query($sql);

    //echo $dBln;
    
?>



<div  class="page-header">
    <h1>Daftar Tenaga Harian Lepas</h1>
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

    if($res->num_rows>0){
?>
<table class="table table-striped table-bordered table-fit">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Aktif</th>
        </tr>
    </thead>
    <tbody>
<?php
        $urt=1;
        while($row=$res->fetch_assoc()){
            //echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
            ?>
        <tr>
            <th scope="row"><?php echo $row["id_thl"]; ?></td>
            <td><?php echo $row["nm_thl"]; ?></td>
            <td><?php echo (($row["nonaktif_thl"])?"<span class='glyphicon glyphicon-remove' aria-hidden='true' style='color:red'></span>":"<span class='glyphicon glyphicon-ok' aria-hidden='true' style='color:green'></span>"); ?></td>
            
        </tr>   
            <?php
        }
?>
    </tbody>
</table> 
<?php
    }else{
?>
<form method="post">
<?php
        if(!getPost("genThn")){
?>
    <input type="hidden" name="genThn" value="<?php echo $dThn; ?>">
    <input type="hidden" name="genBln" value="<?php echo $dBln; ?>">
    <div class="form-group row">
        <div class="col-sm-10">
        <button type="submit" class="btn btn-primary">Generate</button>
        </div>
    </div>
<?php
        }else{

        }
    }
?>    
    
</form>     