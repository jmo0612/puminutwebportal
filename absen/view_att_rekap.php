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
    $sqlD="select * from tb_absen_status";
    $resD=$con->query($sqlD);
    if($resD->num_rows>0){
        while($rowD=$resD->fetch_assoc()){
            $theStatus[$dNum]=$rowD["kode_status"];
            $dNum++;
            $stm="IF(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',kali_pot,'') SEPARATOR '')='','-',GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',kali_pot,'') SEPARATOR '')) as "."kaliS_".$rowD["kode_status"];
            $stm.=", IF(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',CONCAT(tot_potongan,'%'),'') SEPARATOR '')='0%','-',GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',CONCAT(tot_potongan,'%'),'') SEPARATOR '')) as "."totS_".$rowD["kode_status"];
            $stm.=", IF(CONCAT(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',ket_potongan,'') SEPARATOR ''),'')='','-',CONCAT(GROUP_CONCAT(IF(kode_status='".$rowD["kode_status"]."',ket_potongan,'') SEPARATOR ''),'')) as "."ketS_".$rowD["kode_status"];
            $stm.=", IF(SUM(tot_potongan)=0,'-',SUM(tot_potongan)) as totPot";
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
</div>

<table class="table table-striped table-bordered table-fit">
    <thead>
        <tr>
            <th rowspan="3">#</th>
            <th rowspan="3">Nama</th>
            <th colspan="<?php echo $dNum+1; ?>" style="text-align:center">KEHADIRAN</th>
            <th colspan="<?php echo $dNum*2+1; ?>" style="text-align:center">POTONGAN</th>
        </tr>
        <tr>
            <th rowspan="2" style="text-align:center;vertical-align:middle">Hari<br>Kerja</th>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <th rowspan="2" style="text-align:center;vertical-align:middle"><?php echo $theStatus[$i]; ?></th>
            <?php
                }
                for($i=0;$i<$dNum;$i++){
            ?>
            <th colspan="2" style="text-align:center"><?php echo $theStatus[$i]; ?></th>
            <?php
                }
            ?>
            <th rowspan="2" style="text-align:center;vertical-align:middle">Total<br>Potongan</th>
        </tr>
        <tr>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <th style="text-align:center">Pot</th>
            <th style="text-align:center">Ket</th>
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
            <th scope="row"><?php echo $urt; ?></td>
            <td><?php echo $row["nm_thl"]; ?></td>
            <td style="text-align:center"><?php echo $row["total_hari_kerja"]; ?></td>
            <?php
                for($i=0;$i<$dNum;$i++){
            ?>
            <td style="text-align:center"><?php echo $row["kaliS_".$theStatus[$i]]; ?></td>
            <?php
                }
                for($i=0;$i<$dNum;$i++){
            ?>
            <td style="text-align:center"><?php echo $row["totS_".$theStatus[$i]]; ?></td>
            <td style="text-align:center"><?php echo $row["ketS_".$theStatus[$i]]; ?></td>
            <?php
                }
            ?>
            <td style="text-align:center"><?php echo $row["totPot"]; ?></td>
        </tr>    
            <?php
            $urt++;
        }
    }
?>    
    </tbody>
</table>
     