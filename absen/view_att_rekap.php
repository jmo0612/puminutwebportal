<?php
    include 'query_helper.php';

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

    $dNum=0;
    $theStatus=array();

    



    $sel="";
    /*$sqlD="select * from tb_absen_detail_status";
    $resD=$con->query($sqlD);
    if($resD->num_rows>0){
        while($rowD=$resD->fetch_assoc()){
            $theStatus[$dNum]=$rowD["id_det_status"];
            $dNum++;
            $stm="count(IF(id_det_status='".$rowD["id_det_status"]."' and is_libur='0',1,NULL)) as "."DetStatus_".$rowD["id_det_status"];
            $stm.=", count(IF(id_det_status='".$rowD["id_det_status"]."' and is_libur='0' and potong='1',1,NULL)) as "."DetStatusPotong_".$rowD["id_det_status"];
            $stm.=", count(IF(id_det_status='".$rowD["id_det_status"]."' and is_libur='0' and potong='0',1,NULL)) as "."DetStatusUnpotong_".$rowD["id_det_status"];
            

            
            //$stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',red_add,'') SEPARATOR '') as "."R".$rowD["tgl"];
            //$stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',kode_rule,'') SEPARATOR '') as "."S".$rowD["tgl"];
            if($sel==""){
                $sel=$stm;
            }else{
                $sel.=", ".$stm;
            }
        }
    }

    */


    //$sel="";
    $sqlD="select * from tb_absen_status order by urt_status asc";
    $resD=$con->query($sqlD);
    if($resD->num_rows>0){
        while($rowD=$resD->fetch_assoc()){
            $theStatus[$dNum]=$rowD["kode_status"];
            $dNum++;
            $stm="IF(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',kali_pot,'') SEPARATOR '')='','-',GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',kali_pot,'') SEPARATOR '')) as "."kaliS_".$rowD["kode_status"];
            $stm.=", IF(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',CONCAT(tot_potongan,'%'),'') SEPARATOR '')='','0%',GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',CONCAT(tot_potongan,'%'),'') SEPARATOR '')) as "."totS_".$rowD["kode_status"];
            $stm.=", IF(CONCAT(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',ket_potongan,'') SEPARATOR ''),'')='','0x0% = 0%',CONCAT(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',ket_potongan,'') SEPARATOR ''),'')) as "."ketS_".$rowD["kode_status"];
            $stm.=", CONCAT(SUM(tot_potongan),'%') as totPot";
            //$stm=", count(IF(kode_status='".$rowD["kode_status"]."' and is_libur='0',1,NULL)) as "."Status_".$rowD["kode_status"];
            //$stm.=", GROUP_CONCATen(IF(tgl_kerja='".$rowD["tgl_kerja"]."',red_add,'') SEPARATOR '') as "."R".$rowD["tgl"];
            //$stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',kode_rule,'') SEPARATOR '') as "."S".$rowD["tgl"];
            if($sel==""){
                $sel=$stm;
            }else{
                $sel.=", ".$stm;
            }
        }
    }
    

    //echo dateDayWeekMin($dThn."-".str_pad($dBln,2,"0",STR_PAD_LEFT)."-".str_pad(1,2,"0",STR_PAD_LEFT));

    if($sel!="")$sel=", ".$sel;
    //$sel="";

   

    $fltr1="Year(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dThn."' and Month(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dBln."'";
    $fltr2="Year(`q_check_in`.`the_date`)='". $dThn ."' and Month(`q_check_in`.`the_date`)='". $dBln."'";
    $fltr3="Year(`tgl_kerja`)='". $dThn ."' and Month(`tgl_kerja`)='". $dBln ."'";
    $fltr4="tahun='". $dThn ."' and bulan='". $dBln ."'";

    $sql="
        select id_thl, nm_thl, total_hari_kerja".$sel." FROM
            ".q_attlog_unfinal($fltr1,$fltr2,$fltr3)."
        WHERE ".$fltr3."
        GROUP BY id_thl order by nm_thl asc";

        $sql="
        select id_thl, nm_thl, total_hari_kerja".$sel." FROM
            ".qtb_det_pot_grouped($fltr1,$fltr2,$fltr3,$fltr4)."
        WHERE ".$fltr4."
        GROUP BY tahun,bulan,id_thl order by nm_thl asc";

        
    //$sql=qtb_det_pot_grouped($fltr1,$fltr2,$fltr3,$fltr4);
    //echo $sql;
    $res=$con->query($sql);

    $pagePortrait=false;
    $totalCol=$dNum*3+4;
    $importantColW=array($dNum+1);


    $importantColW[0]=(20/100)*30;
    for($i=1;$i<$dNum+1;$i++){
        $importantColW[$i]=(75/$dNum)/100*70;
    }

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
    <div style="text-align:left">
        <form action="rep_rekap.php" method="post" target="_blank">
            <input name="contentRep" id="contentRep" type="hidden">
            <input name="tahun" id="tahun" type="hidden" value="<?php echo $dThn; ?>">
            <input name="bulan" id="bulan" type="hidden" value="<?php echo $dBln; ?>">
            <span>Ukuran Huruf: </span><input name="font" id="font" type="number" value="7" style="width:50px">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-print"></span><span style="margin:5px">Cetak</span></button>
        </form>
    </div>
</div>

<div id="pdfRep">
<div>
<table class="table table-striped table-bordered table-fit">
    <thead>
        <tr>
            <th rowspan="3" width="<?php echo $defW; ?>"><strong>#</strong></th>
            <th rowspan="3" width="<?php echo $importantColW[0]; ?>"><strong>Nama</strong></th>
            <th width="<?php echo $defW*($dNum+1); ?>" colspan="<?php echo $dNum+1; ?>" style="text-align:center"><strong>KEHADIRAN</strong></th>
            <th width="<?php echo ($defW*($dNum+1))+($importantColW[2]*$dNum); ?>" colspan="<?php echo $dNum*2+1; ?>" style="text-align:center"><strong>POTONGAN</strong></th>
        </tr>
        <tr>
            <th width="<?php echo $defW; ?>" rowspan="2" style="text-align:center;vertical-align:middle"><strong>Hari</strong><br><strong>Kerja</strong></th>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <th width="<?php echo $defW; ?>" rowspan="2" style="text-align:center;vertical-align:middle"><strong><?php echo $theStatus[$i]; ?></strong></th>
            <?php
                }
                for($i=0;$i<$dNum;$i++){
            ?>
            <th width="<?php echo $defW+$importantColW[1]; ?>" colspan="2" style="text-align:center"><strong><?php echo $theStatus[$i]; ?></strong></th>
            <?php
                }
            ?>
            <th width="<?php echo $defW; ?>" rowspan="2" style="text-align:center;vertical-align:middle"><strong>Total</strong><br><strong>Pot</strong></th>
        </tr>
        <tr>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <th width="<?php echo $defW; ?>" style="text-align:center"><strong>Pot</strong></th>
            <th width="<?php echo $importantColW[1]; ?>" style="text-align:center"><strong>Ket</strong></th>
            <?php
                }
            ?>
        </tr>
    </thead>
    <tbody>
<?php
    if($res->num_rows>0){
        $urt=1;
        while($row=$res->fetch_assoc()){
            //echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
            ?>
        <tr>
            <th width="<?php echo $defW; ?>" scope="row"><?php echo $urt; ?></td>
            <td width="<?php echo $importantColW[0]; ?>"><?php echo $row["nm_thl"]; ?></td>
            <td width="<?php echo $defW; ?>" style="text-align:center"><?php echo $row["total_hari_kerja"]; ?></td>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <td width="<?php echo $defW; ?>" style="text-align:center"><?php echo $row["kaliS_".$theStatus[$i]]; ?></td>
            <?php
                }
                for($i=0;$i<$dNum;$i++){
            ?>
            <td width="<?php echo $defW; ?>" style="text-align:center"><?php echo $row["totS_".$theStatus[$i]]; ?></td>
            <td width="<?php echo $importantColW[1]; ?>"><?php echo $row["ketS_".$theStatus[$i]]; ?></td>
            <?php
                }
            ?>
            <td width="<?php echo $defW; ?>" style="text-align:center"><?php echo $row["totPot"]; ?></td>
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

        $pageWKop=$pageW-(30/100*$pageW);

?>
<div id="pdfKop" style="visibility:hidden">
    <table nobr="true" align="center">
        <tr>
            <td style="border:1px solid white" rowspan="4" align="right" width="<?php echo ((int)45/100*$pageWKop); ?>"><img src="images/logo.png" width="60" height="55"></td>
            <td style="border:1px solid white" align="center" width="<?php echo ($pageWKop-((int)45/100*$pageWKop)); ?>">&nbsp</td>
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
        <h5 style="font-size:10pt">REKAPITULASI KEHADIRAN<br>
        TENAGA HARIAN LEPAS<br>
        BULAN <?php echo strtoupper(dateMonth($curDateDBStr));?></h5>
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