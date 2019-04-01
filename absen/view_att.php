<?php
    include 'query_helper.php';

    

    $deltaDate=0;
    if(getGet("dDt")){
        $deltaDate=getGet("dDt");
    }

    $dInterval= new DateInterval("P".abs($deltaDate)."D");
    if($deltaDate<0)$dInterval->invert=abs($deltaDate);

    $curDate=date_create(date("Y-m-d"));
    if($deltaDate!=0)date_add($curDate,$dInterval);

    $curDateDBStr=dateDBFormat(date_format($curDate,"Y-m-d"));

    function findDate($next=true){
        global $curDateDBStr,$con,$deltaDate;
        $ret=$deltaDate;
        $sql="select * from tb_absen_jam_kerja where is_libur='0' and tgl_kerja > '".$curDateDBStr."' order by tgl_kerja asc";
        if($next==false)$sql="select * from tb_absen_jam_kerja where is_libur='0' and tgl_kerja < '".$curDateDBStr."' order by tgl_kerja desc";
        $res=$con->query($sql);
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $d0=new JmoDateTime();
            $d0=new JmoDateTime($d0->toString("Y-m-d")." 00:00:00");
            $d1=new JmoDateTime($row["tgl_kerja"]." 00:00:00");
            $ret=$d0->diffInDays($d1);
        }
        return $ret;
    }

    $fltr1="cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date)='".$curDateDBStr."'";
    $fltr2="`q_check_in`.`the_date`='". $curDateDBStr ."' and `q_check_in`.`the_date`<='".date("Y-m-d")."'";
    $fltr3="`tgl_kerja`='". $curDateDBStr ."' and `tgl_kerja`<='".date("Y-m-d")."'";

    $sql="select `q_attlog_unfinal`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_unfinal`.`id_thl` AS `id_thl`,`q_attlog_unfinal`.`nm_thl` AS `nm_thl`,`q_attlog_unfinal`.`jam` AS `jam`,`q_attlog_unfinal`.`kode_rule` AS `kode_rule`,`q_attlog_unfinal`.`is_libur` AS `is_libur`,`q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,sum(`q_attlog_unfinal`.`red_add`) AS `red_add` from 
	".q_attlog_unfinal($fltr1,$fltr2,$fltr3)." 
WHERE ".$fltr3." and `is_libur`='0' and `non_aktif_thl`='0'
group by `q_attlog_unfinal`.`tgl_kerja`,`q_attlog_unfinal`.`id_thl`,`q_attlog_unfinal`.`kode_rule`
ORDER by tgl_kerja asc, nm_thl ASC
";

    //echo $sql;

    
    $res=$con->query($sql);

    
    $pagePortrait=true;
    $totalCol=4;
    $importantColW=array(5,20,20);


    $pageW=550;
    $pageH=900;
    
    $scale=1060/900;
    $pageW=(int)floor($scale*$pageW);
    $pageH=(int)floor($scale*$pageH);
    if(!$pagePortrait){
        $tmpPage=$pageW;
        $pageW=$pageH;
        $pageH=$tmpPage;
    }
    for($i=0;$i<sizeof($importantColW);$i++){
        $importantColW[$i]=(int)floor($importantColW[$i]*$pageW/100);
    }
    $wLeft=$pageW-array_sum($importantColW);
    $defW=floor($wLeft/($totalCol-sizeof($importantColW)));


    
?>



<div  class="page-header" align="center">
    <h1>Daftar Hadir</h1>
    <h4>Tenaga Harian Lepas (THL)</h4>
    <nav aria-label="Date navigation">
        <ul class="pagination">
            <li>
                <a href="?dDt=<?php echo findDate(false); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li><span id="dtSpan" class="datepicker"><?php echo dateComplete($curDateDBStr); ?></span></li>
            
            <li>
                <a href="?dDt=<?php echo findDate(); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <div style="text-align:left">
        <form action="rep_att.php" method="post" target="_blank">
            <input name="contentRep" id="contentRep" type="hidden">
            <input name="tahun" id="tahun" type="hidden" value="<?php echo $dThn; ?>">
            <input name="bulan" id="bulan" type="hidden" value="<?php echo $dBln; ?>">
            <span>Ukuran Huruf: </span><input name="font" id="font" type="number" value="10" style="width:50px">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span><span style="margin:5px">Cetak</span></button>
        </form>
    </div>
</div>

<div id="pdfRep">
<div style="text-align:center">
<table class="table table-striped table-bordered table-fit table-hover" style="text-align:left">
    <thead>
        <tr>
            <th width="<?php echo $importantColW[0]; ?>"><strong>#</strong></th>
            <th width="<?php echo $defW; ?>"><strong>Nama</strong></th>
            <th width="<?php echo $importantColW[1]; ?>"><strong>Jam</strong></th>
            <th width="<?php echo $importantColW[2]; ?>"><strong>Status</strong></th>
        </tr>
    </thead>
    <tbody>
<?php
    if($res->num_rows>0){
        $urt=1;
        while($row=$res->fetch_assoc()){
            //echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
            $persen=(int) $row["red_add"];
            $red=round($persen/100*255);
            if($red>255)$red=255;
            ?>
        <tr onclick="window.location='<?php echo getUrl('absen/index.php?p=detAtt&idThl='.$row['id_thl']); ?>';">
            <th scope="row" width="<?php echo $importantColW[0]; ?>"><?php echo $urt; ?></td>
            <td width="<?php echo $defW; ?>"><?php echo $row["nm_thl"]; ?></td>
            <td width="<?php echo $importantColW[1]; ?>"><?php echo $row["jam"]; ?></td>
            <th style="color:rgb(<?php echo $red; ?>,0,0)" width="<?php echo $importantColW[2]; ?>"><?php echo $row["kode_rule"]; ?></th>
        </tr>    
            <?php
            $urt++;
        }
    }
    
?>    
    </tbody>
</table>
</div>
</div>
<?php


    $sqlKop="select * from tb_absen_kop where id_kop='1'";
    $resKop=$con->query($sqlKop);
    if($resKop->num_rows>0){
        $rowKop=$resKop->fetch_assoc();

        $pageWKop=$pageW-(20/100*$pageW);

?>
<div id="pdfKop" style="visibility:hidden">
    <table nobr="true" align="center">
        <tr>
            <td style="border:1px solid white" rowspan="4" align="right" width="<?php echo ((int)30/100*$pageWKop); ?>"><img src="images/logo.png" width="60" height="55"></td>
            <td style="border:1px solid white" align="center" width="<?php echo ($pageWKop-((int)30/100*$pageWKop)); ?>">&nbsp</td>
        </tr>
        <tr>
            <td style="border:1px solid white;font-size:10pt" align="center"><h5><?php echo $rowKop["kop1"]; ?></h5></td>
        </tr>
        <tr>
            <td style="border:1px solid white;font-size:10pt" align="center"><h4><?php echo $rowKop["kop2"]; ?></h4></td>
        </tr>
        <tr>
            <td style="border:1px solid white;font-size:10pt" align="center"><small><i><?php echo $rowKop["kop3"]; ?><i></small></td>
        </tr>
    </table>
    <hr>
    <div align="center" >
        <h5 style="font-size:10pt">DAFTAR HADIR<br>
        TENAGA HARIAN LEPAS<br>
        <?php echo strtoupper(dateComplete($curDateDBStr));?></h5>
    </div>
</div>
<?php
    }
?>

<?php


    $sqlTT="SELECT 
	`tb_absen_tanda_tangan`.`kd_jab_tt` AS `kd_jab`,
    `tb_absen_tanda_tangan`.`nm_jab_tt` AS `nm_jab`,
    `tb_absen_tanda_tangan`.`nip_pejabat` AS `nip_pejabat`,
    `tb_pegawai`.`nm_pegawai` AS `nm_pegawai`
FROM 
	`tb_absen_tanda_tangan`,
    `tb_pegawai`
WHERE 
	`tb_absen_tanda_tangan`.`nip_pejabat`=`tb_pegawai`.`id_pegawai` AND `tb_absen_tanda_tangan`.`kd_jab_tt`='KASUB_KEPEG'";
    $resTT=$con->query($sqlTT);
    if($resTT->num_rows>0){
        $rowTT=$resTT->fetch_assoc();


?>
<div id="pdfTT" style="visibility:hidden;font-size:10pt" >
    <div align="center">
    <table nobr="true" align="center" style="font-size:10px">
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td align="center" style="border:1px solid white"><?php echo $rowTT['nm_jab']; ?></td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td style="border:1px solid white">&nbsp</td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td style="border:1px solid white">&nbsp</td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td style="border:1px solid white">&nbsp</td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td style="border:1px solid white">&nbsp</td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td align="center" style="border:1px solid white"><strong><?php echo $rowTT['nm_pegawai']; ?></strong></td>
        </tr>
        <tr>
            <td style="border:1px solid white">&nbsp</td>
            <td align="center" style="border:1px solid white">NIP. <?php echo $rowTT['nip_pejabat']; ?></td>
        </tr>
        
    </table>
    </div>
</div>
<?php
    }
?>
     