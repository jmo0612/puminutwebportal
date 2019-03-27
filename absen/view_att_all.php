<?php

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
    $theDates=array();
    $isLibur=array();

    

    $sel="";
    $sqlD="select tgl_kerja, DAY(tgl_kerja) as tgl, is_libur from tb_absen_jam_kerja where MONTH(tgl_kerja)='".$dBln."' and YEAR(tgl_kerja)='".$dThn."' and tgl_kerja<='".$serverDateStr."' order by tgl_kerja asc";
    $resD=$con->query($sqlD);
    if($resD->num_rows>0){
        while($rowD=$resD->fetch_assoc()){
            $theDates[$dNum]=$rowD["tgl"];
            $isLibur[$dNum]=$rowD["is_libur"];
            $dNum++;
            $stm="GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',jam,'') SEPARATOR '') as "."J".$rowD["tgl"];
            $stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',red_add,'') SEPARATOR '') as "."R".$rowD["tgl"];
            $stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',kode_rule,'') SEPARATOR '') as "."S".$rowD["tgl"];
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

    $sql="SELECT
            id_thl, nm_thl".$sel."
            FROM
            q_attlog
            WHERE is_libur='0' and non_aktif_thl='0' 
            GROUP BY id_thl order by nm_thl asc";

    $fltr1="Year(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dThn."' and Month(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dBln."'";
    $fltr2="Year(`q_check_in`.`the_date`)='". $dThn ."' and Month(`q_check_in`.`the_date`)='". $dBln ."' and `q_check_in`.`the_date`<='".date("Y-m-d")."'";
    $fltr3="Year(`tgl_kerja`)='". $dThn ."' and Month(`tgl_kerja`)='". $dBln ."' and `tgl_kerja`<='".date("Y-m-d")."'";

    $sql="select id_thl, nm_thl".$sel." FROM
    (select `q_attlog_unfinal`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_unfinal`.`id_thl` AS `id_thl`,`q_attlog_unfinal`.`nm_thl` AS `nm_thl`,`q_attlog_unfinal`.`jam` AS `jam`,`q_attlog_unfinal`.`kode_rule` AS `kode_rule`,`q_attlog_unfinal`.`is_libur` AS `is_libur`,`q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,sum(`q_attlog_unfinal`.`red_add`) AS `red_add` from 
    (select `q_attlog_main`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_main`.`id_thl` AS `id_thl`,`q_attlog_main`.`id_det_status` AS `id_det_status`,`q_attlog_main`.`is_libur` AS `is_libur`,`q_attlog_main`.`jam_masuk` AS `jam_masuk`,`q_attlog_main`.`jam_keluar` AS `jam_keluar`,`q_attlog_main`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_main`.`final` AS `final`,`q_attlog_main`.`nm_thl` AS `nm_thl`,`q_attlog_main`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_main`.`masuk` AS `masuk`,`q_attlog_main`.`keluar` AS `keluar`,`q_attlog_main`.`kode_rule` AS `kode_rule`,`q_attlog_main`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_main`.`kode_status` AS `kode_status`,`q_attlog_main`.`ket_det_status` AS `ket_det_status`,`q_attlog_main`.`max_per_periode` AS `max_per_periode`,`q_attlog_main`.`min_per_periode` AS `min_per_periode`,`q_attlog_main`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_main`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_main`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_main`.`kinerja` AS `kinerja`,`q_attlog_main`.`automatic_status` AS `automatic_status`,`q_attlog_main`.`override_value` AS `override_value`,`q_attlog_main`.`red_add` AS `red_add`,`q_attlog_main`.`ket_status` AS `ket_status`,`q_attlog_main`.`potong` AS `potong`,`q_attlog_main`.`total_status` AS `total_status`,`q_attlog_main`.`total_hari_kerja` AS `total_hari_kerja`,`q_attlog_main`.`det_status_ke` AS `det_status_ke`,`q_attlog_main`.`potongan` AS `potongan`,`q_attlog_main`.`jam` AS `jam` from 
        (select `q_attlog_override_value`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_override_value`.`is_libur` AS `is_libur`,`q_attlog_override_value`.`jam_masuk` AS `jam_masuk`,`q_attlog_override_value`.`jam_keluar` AS `jam_keluar`,`q_attlog_override_value`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_override_value`.`final` AS `final`,`q_attlog_override_value`.`id_thl` AS `id_thl`,`q_attlog_override_value`.`nm_thl` AS `nm_thl`,`q_attlog_override_value`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_override_value`.`masuk` AS `masuk`,`q_attlog_override_value`.`keluar` AS `keluar`,`q_attlog_override_value`.`kode_rule` AS `kode_rule`,`q_attlog_override_value`.`id_det_status` AS `id_det_status`,`q_attlog_override_value`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_override_value`.`kode_status` AS `kode_status`,`q_attlog_override_value`.`ket_det_status` AS `ket_det_status`,`q_attlog_override_value`.`max_per_periode` AS `max_per_periode`,`q_attlog_override_value`.`min_per_periode` AS `min_per_periode`,`q_attlog_override_value`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_override_value`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_override_value`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_override_value`.`kinerja` AS `kinerja`,`q_attlog_override_value`.`automatic_status` AS `automatic_status`,`q_attlog_override_value`.`override_value` AS `override_value`,`q_attlog_override_value`.`red_add` AS `red_add`,`q_attlog_override_value`.`ket_status` AS `ket_status`,`q_attlog_override_value`.`potong` AS `potong`,`func_absen_total`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`) AS `total_status`,`func_absen_tot_hari`(`q_attlog_override_value`.`tgl_kerja`) AS `total_hari_kerja`,`func_absen_status_ke`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`) AS `det_status_ke`,`func_absen_potongan`(`q_attlog_override_value`.`max_per_periode`,`q_attlog_override_value`.`min_per_periode`,`q_attlog_override_value`.`potongan_bef_min`,`q_attlog_override_value`.`potongan_betw_min_max`,`q_attlog_override_value`.`potongan_aft_max`,`func_absen_status_ke`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`),`q_attlog_override_value`.`potong`) AS `potongan`,`func_absen_jam`(`q_attlog_override_value`.`masuk`,`q_attlog_override_value`.`keluar`) AS `jam` from 
            (select `q_attlog_detail`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_detail`.`is_libur` AS `is_libur`,`q_attlog_detail`.`jam_masuk` AS `jam_masuk`,`q_attlog_detail`.`jam_keluar` AS `jam_keluar`,`q_attlog_detail`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_detail`.`final` AS `final`,`q_attlog_detail`.`id_thl` AS `id_thl`,`q_attlog_detail`.`nm_thl` AS `nm_thl`,`q_attlog_detail`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_detail`.`masuk` AS `masuk`,`q_attlog_detail`.`keluar` AS `keluar`,`q_attlog_detail`.`kode_rule` AS `kode_rule`,`q_attlog_detail`.`id_det_status` AS `id_det_status`,`q_attlog_detail`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_detail`.`kode_status` AS `kode_status`,`q_attlog_detail`.`ket_det_status` AS `ket_det_status`,`q_attlog_detail`.`max_per_periode` AS `max_per_periode`,`q_attlog_detail`.`min_per_periode` AS `min_per_periode`,`q_attlog_detail`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_detail`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_detail`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_detail`.`kinerja` AS `kinerja`,`q_attlog_detail`.`automatic_status` AS `automatic_status`,`q_attlog_detail`.`override_value` AS `override_value`,`q_attlog_detail`.`red_add` AS `red_add`,`q_attlog_detail`.`ket_status` AS `ket_status`,`func_absen_potong`(`q_attlog_detail`.`tgl_kerja`,`q_attlog_detail`.`id_thl`,`q_attlog_detail`.`id_det_status`) AS `potong` from 
                (select `q_attlog`.`tgl_kerja` AS `tgl_kerja`,`q_attlog`.`is_libur` AS `is_libur`,`q_attlog`.`jam_masuk` AS `jam_masuk`,`q_attlog`.`jam_keluar` AS `jam_keluar`,`q_attlog`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog`.`final` AS `final`,`q_attlog`.`id_thl` AS `id_thl`,`q_attlog`.`nm_thl` AS `nm_thl`,`q_attlog`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog`.`masuk` AS `masuk`,`q_attlog`.`keluar` AS `keluar`,`q_attlog`.`kode_rule` AS `kode_rule`,`puprarsip`.`tb_absen_detstatus_rule`.`id_det_status` AS `id_det_status`,`puprarsip`.`tb_absen_detstatus_rule`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`puprarsip`.`tb_absen_detail_status`.`kode_status` AS `kode_status`,`puprarsip`.`tb_absen_detail_status`.`ket_det_status` AS `ket_det_status`,`puprarsip`.`tb_absen_detail_status`.`max_per_periode` AS `max_per_periode`,`puprarsip`.`tb_absen_detail_status`.`min_per_periode` AS `min_per_periode`,`puprarsip`.`tb_absen_detail_status`.`potongan_bef_min` AS `potongan_bef_min`,`puprarsip`.`tb_absen_detail_status`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`puprarsip`.`tb_absen_detail_status`.`potongan_aft_max` AS `potongan_aft_max`,`puprarsip`.`tb_absen_detail_status`.`kinerja` AS `kinerja`,`puprarsip`.`tb_absen_detail_status`.`automatic_status` AS `automatic_status`,`puprarsip`.`tb_absen_detail_status`.`override_value` AS `override_value`,`puprarsip`.`tb_absen_detail_status`.`red_add` AS `red_add`,`puprarsip`.`tb_absen_status`.`ket_status` AS `ket_status` from (((
                    (select `q_all_date`.`tgl_kerja` AS `tgl_kerja`,`q_all_date`.`is_libur` AS `is_libur`,`q_all_date`.`jam_masuk` AS `jam_masuk`,`q_all_date`.`jam_keluar` AS `jam_keluar`,`q_all_date`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_all_date`.`final` AS `final`,`q_all_date`.`id_thl` AS `id_thl`,`q_all_date`.`nm_thl` AS `nm_thl`,`q_all_date`.`non_aktif_thl` AS `non_aktif_thl`,`q_att_data`.`masuk` AS `masuk`,`q_att_data`.`keluar` AS `keluar`,`func_absen_rule`(`q_all_date`.`tgl_kerja`,`q_all_date`.`id_thl`,`q_att_data`.`masuk`,`q_att_data`.`keluar`,`q_all_date`.`jam_masuk`,`q_all_date`.`jam_keluar`) AS `kode_rule` from (
                        (select `puprarsip`.`tb_absen_jam_kerja`.`tgl_kerja` AS `tgl_kerja`,`puprarsip`.`tb_absen_jam_kerja`.`is_libur` AS `is_libur`,`puprarsip`.`tb_absen_jam_kerja`.`jam_masuk` AS `jam_masuk`,`puprarsip`.`tb_absen_jam_kerja`.`jam_keluar` AS `jam_keluar`,`puprarsip`.`tb_absen_jam_kerja`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`puprarsip`.`tb_absen_jam_kerja`.`final` AS `final`,`puprarsip`.`tb_absen_thl`.`id_thl` AS `id_thl`,`puprarsip`.`tb_absen_thl`.`nm_thl` AS `nm_thl`,`puprarsip`.`tb_absen_thl`.`non_aktif_thl` AS `non_aktif_thl` from (`puprarsip`.`tb_absen_jam_kerja` join `puprarsip`.`tb_absen_thl`)) AS `q_all_date`
                    left join 
                        (select `q_check_in`.`the_date` AS `the_date`,`q_check_in`.`id_thl` AS `id_thl`,`q_check_in`.`pagi` AS `masuk`,`q_check_out`.`sore` AS `keluar` from (
                            (select cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,`puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,min(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `pagi` from `puprarsip`.`tb_absen_attlog` WHERE ".$fltr1." group by cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),`puprarsip`.`tb_absen_attlog`.`id_thl`) AS `q_check_in`
                        left join 
                            (select cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,`puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,max(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `sore` from `puprarsip`.`tb_absen_attlog` WHERE ".$fltr1." group by cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),`puprarsip`.`tb_absen_attlog`.`id_thl`) AS `q_check_out`
                        on(((`q_check_in`.`the_date` = `q_check_out`.`the_date`) and (`q_check_in`.`id_thl` = `q_check_out`.`id_thl`) and (`q_check_in`.`pagi` <> `q_check_out`.`sore`)))) where ".$fltr2.") AS `q_att_data`
                    on(((`q_all_date`.`tgl_kerja` = `q_att_data`.`the_date`) and (`q_all_date`.`id_thl` = `q_att_data`.`id_thl`)))) where ".$fltr3.") AS `q_attlog`
                join `puprarsip`.`tb_absen_detstatus_rule` on((`puprarsip`.`tb_absen_detstatus_rule`.`kode_rule` = `q_attlog`.`kode_rule`))) join `puprarsip`.`tb_absen_detail_status` on((`puprarsip`.`tb_absen_detail_status`.`id_det_status` = `puprarsip`.`tb_absen_detstatus_rule`.`id_det_status`))) join `puprarsip`.`tb_absen_status` on((`puprarsip`.`tb_absen_status`.`kode_status` = `puprarsip`.`tb_absen_detail_status`.`kode_status`))) where ".$fltr3.") AS `q_attlog_detail`
            where ".$fltr3.") AS `q_attlog_override_value`
        where ".$fltr3.") AS `q_attlog_main`
    where (`q_attlog_main`.`final` = '0' and ".$fltr3.")) AS `q_attlog_unfinal` 
WHERE ".$fltr3." and `is_libur`='0' and `non_aktif_thl`='0'
group by `q_attlog_unfinal`.`tgl_kerja`,`q_attlog_unfinal`.`id_thl`,`q_attlog_unfinal`.`kode_rule`
ORDER by tgl_kerja asc, nm_thl ASC) AS qtb_det
GROUP BY id_thl order by nm_thl asc";

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
            <th rowspan="2">#</th>
            <th rowspan="2">Nama</th>
            <?php
                for($i=0;$i<$dNum;$i++){
                    if($isLibur[$i]){
            ?>
            <th style="text-align: center;background-color: #f2dede;width: 10px"><?php echo $theDates[$i]."<br>(".dateDayWeekMin($dThn."-".str_pad($dBln,2,"0",STR_PAD_LEFT)."-".str_pad($theDates[$i],2,"0",STR_PAD_LEFT)).")"; ?></th>
            <?php
                    }else{
            ?>
            <th colspan="2"  style="text-align: center"><?php echo $theDates[$i]."<br>(".dateDayWeekMin($dThn."-".str_pad($dBln,2,"0",STR_PAD_LEFT)."-".str_pad($theDates[$i],2,"0",STR_PAD_LEFT)).")"; ?></th>
            <?php
                    }
                }
            ?>
        </tr>
        <tr>
            <?php
                for($i=0;$i<$dNum;$i++){
                    if($isLibur[$i]){
            ?>
            <th style="text-align: center;background-color: #f2dede">Libur</th>
            <?php
                    }else{
            ?>
            <th style="text-align: center;width: 10px;min-width: 10px">Jam</th>
            <th style="text-align: center;width: 10px;min-width: 10px">Ket</th>
            <?php
                    }
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
            <?php
                for($i=1;$i<=$dNum;$i++){
                    if($isLibur[$i-1]){
            ?>
            <td style="text-align: center;background-color: #f2dede">&nbsp;</td>
            <?php
                    }else{
                        $persen=(int) $row["R".$i];
                        $red=round($persen/100*255);
                        if($red>255)$red=255;
            ?>
            <td style="text-align: center"><?php echo (($row["J".$i]=="Tidak Hadir")?"-":$row["J".$i]); ?></td>
            <th style="text-align: center;color:rgb(<?php echo $red; ?>,0,0)"><?php echo $row["S".$i]; ?></th>
            <?php
                    }
                }
            ?>
        </tr>    
            <?php
            $urt++;
        }
    }
?>    
    </tbody>
</table>
     