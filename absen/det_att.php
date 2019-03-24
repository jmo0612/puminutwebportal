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

    


    $dBln=dateMonthInt($curDateDBStr);
    $dThn=dateYearInt($curDateDBStr);

    

    $sql="SELECT
            *
            FROM
            (SELECT
            all_date.tgl_kerja as tgl_kerja,
            all_date.is_libur as is_libur,
            all_date.jam_masuk as jam_masuk,
            all_date.jam_keluar as jam_keluar,
            all_date.ket_tgl_kerja as ket_tgl_kerja,
            all_date.id_thl as id_thl,
            all_date.nm_thl as nm_thl,
            all_date.nonaktif_thl as nonaktif_thl,
            att_data.masuk AS masuk,
            att_data.keluar AS keluar,
            func_absen_rule(all_date.tgl_kerja,all_date.id_thl,att_data.masuk,att_data.keluar,all_date.jam_masuk,all_date.jam_keluar) as kode_rule
            
            FROM
            (SELECT
            tb_absen_jam_kerja.tgl_kerja as tgl_kerja,
            tb_absen_jam_kerja.is_libur as is_libur,
            tb_absen_jam_kerja.jam_masuk as jam_masuk,
            tb_absen_jam_kerja.jam_keluar as jam_keluar,
            tb_absen_jam_kerja.ket_tgl_kerja as ket_tgl_kerja,
            tb_absen_thl_bulan.id_thl as id_thl,
            tb_absen_thl_bulan.nm_thl as nm_thl,
            tb_absen_thl_bulan.nonaktif_thl as nonaktif_thl
            FROM
            tb_absen_jam_kerja
            cross JOIN
            tb_absen_thl_bulan
            ON tb_absen_thl_bulan.tahun='".dateYearInt($curDateDBStr)."' AND tb_absen_thl_bulan.bln='".dateMonthInt($curDateDBStr)."' order by tgl_kerja asc, id_thl asc ) AS all_date
            LEFT JOIN
            (SELECT
            check_in.the_date AS the_date,
            check_in.id_thl AS id_thl,
            check_in.pagi AS masuk,
            check_out.sore AS keluar
            FROM
            (SELECT
            Date(tb_absen_attlog.time_second) AS the_date,
            tb_absen_attlog.id_thl AS id_thl,
            Min(tb_absen_attlog.time_second) AS pagi
            FROM
            tb_absen_attlog
            GROUP BY
            Date(tb_absen_attlog.time_second),
            tb_absen_attlog.id_thl) AS check_in
            LEFT JOIN
            (SELECT
            Date(tb_absen_attlog.time_second) AS the_date,
            tb_absen_attlog.id_thl AS id_thl,
            Max(tb_absen_attlog.time_second) AS sore
            FROM
            tb_absen_attlog
            GROUP BY
            Date(tb_absen_attlog.time_second),
            tb_absen_attlog.id_thl) AS check_out
            ON
            check_in.the_date = check_out.the_date AND
            check_in.id_thl = check_out.id_thl AND
            check_in.pagi <> check_out.sore) AS att_data
            ON
            all_date.tgl_kerja=att_data.the_date AND all_date.id_thl=att_data.id_thl ) AS att0
            WHERE id_thl='".getGet("idThl")."' and Month(tgl_kerja)='".$dBln."' and Year(tgl_kerja)='".$dThn."' and nonaktif_thl='0' order by id_thl asc";

$fltr1="Year(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dThn."' and Month(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dBln."'";
$fltr2="id_thl='".getGet("idThl")."' and Year(`tgl_kerja`)='". $dThn ."' and Month(`tgl_kerja`)='". $dBln ."' and `non_aktif_thl`='0'";

    $sql="select `q_attlog_unfinal`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_unfinal`.`id_thl` AS `id_thl`,`q_attlog_unfinal`.`nm_thl` AS `nm_thl`,`q_attlog_unfinal`.`jam` AS `jam`,`q_attlog_unfinal`.`kode_rule` AS `kode_rule`,`q_attlog_unfinal`.`is_libur` AS `is_libur`,`q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_unfinal`.`jam_target` AS `jam_target`,`q_attlog_unfinal`.`ket_tgl_kerja` AS `ket_tgl_kerja`,sum(`q_attlog_unfinal`.`red_add`) AS `red_add` from 
    (select `q_attlog_main`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_main`.`id_thl` AS `id_thl`,`q_attlog_main`.`id_det_status` AS `id_det_status`,`q_attlog_main`.`is_libur` AS `is_libur`,`q_attlog_main`.`jam_masuk` AS `jam_masuk`,`q_attlog_main`.`jam_keluar` AS `jam_keluar`,`q_attlog_main`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_main`.`final` AS `final`,`q_attlog_main`.`nm_thl` AS `nm_thl`,`q_attlog_main`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_main`.`masuk` AS `masuk`,`q_attlog_main`.`keluar` AS `keluar`,`q_attlog_main`.`kode_rule` AS `kode_rule`,`q_attlog_main`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_main`.`kode_status` AS `kode_status`,`q_attlog_main`.`ket_det_status` AS `ket_det_status`,`q_attlog_main`.`max_per_periode` AS `max_per_periode`,`q_attlog_main`.`min_per_periode` AS `min_per_periode`,`q_attlog_main`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_main`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_main`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_main`.`kinerja` AS `kinerja`,`q_attlog_main`.`automatic_status` AS `automatic_status`,`q_attlog_main`.`override_value` AS `override_value`,`q_attlog_main`.`red_add` AS `red_add`,`q_attlog_main`.`ket_status` AS `ket_status`,`q_attlog_main`.`potong` AS `potong`,`q_attlog_main`.`total_status` AS `total_status`,`q_attlog_main`.`total_hari_kerja` AS `total_hari_kerja`,`q_attlog_main`.`det_status_ke` AS `det_status_ke`,`q_attlog_main`.`potongan` AS `potongan`,`q_attlog_main`.`jam` AS `jam`,`q_attlog_main`.`jam_target` AS `jam_target` from 
        (select `q_attlog_override_value`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_override_value`.`is_libur` AS `is_libur`,`q_attlog_override_value`.`jam_masuk` AS `jam_masuk`,`q_attlog_override_value`.`jam_keluar` AS `jam_keluar`,`q_attlog_override_value`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_override_value`.`final` AS `final`,`q_attlog_override_value`.`id_thl` AS `id_thl`,`q_attlog_override_value`.`nm_thl` AS `nm_thl`,`q_attlog_override_value`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_override_value`.`masuk` AS `masuk`,`q_attlog_override_value`.`keluar` AS `keluar`,`q_attlog_override_value`.`kode_rule` AS `kode_rule`,`q_attlog_override_value`.`id_det_status` AS `id_det_status`,`q_attlog_override_value`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_override_value`.`kode_status` AS `kode_status`,`q_attlog_override_value`.`ket_det_status` AS `ket_det_status`,`q_attlog_override_value`.`max_per_periode` AS `max_per_periode`,`q_attlog_override_value`.`min_per_periode` AS `min_per_periode`,`q_attlog_override_value`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_override_value`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_override_value`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_override_value`.`kinerja` AS `kinerja`,`q_attlog_override_value`.`automatic_status` AS `automatic_status`,`q_attlog_override_value`.`override_value` AS `override_value`,`q_attlog_override_value`.`red_add` AS `red_add`,`q_attlog_override_value`.`ket_status` AS `ket_status`,`q_attlog_override_value`.`potong` AS `potong`,`func_absen_total`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`) AS `total_status`,`func_absen_tot_hari`(`q_attlog_override_value`.`tgl_kerja`) AS `total_hari_kerja`,`func_absen_status_ke`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`) AS `det_status_ke`,`func_absen_potongan`(`q_attlog_override_value`.`max_per_periode`,`q_attlog_override_value`.`min_per_periode`,`q_attlog_override_value`.`potongan_bef_min`,`q_attlog_override_value`.`potongan_betw_min_max`,`q_attlog_override_value`.`potongan_aft_max`,`func_absen_status_ke`(`q_attlog_override_value`.`tgl_kerja`,`q_attlog_override_value`.`id_thl`,`q_attlog_override_value`.`id_det_status`),`q_attlog_override_value`.`potong`) AS `potongan`,`func_absen_jam`(`q_attlog_override_value`.`masuk`,`q_attlog_override_value`.`keluar`) AS `jam`,`func_absen_jam`(`q_attlog_override_value`.`jam_masuk`,`q_attlog_override_value`.`jam_keluar`) AS `jam_target` from 
            (select `q_attlog_detail`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_detail`.`is_libur` AS `is_libur`,`q_attlog_detail`.`jam_masuk` AS `jam_masuk`,`q_attlog_detail`.`jam_keluar` AS `jam_keluar`,`q_attlog_detail`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog_detail`.`final` AS `final`,`q_attlog_detail`.`id_thl` AS `id_thl`,`q_attlog_detail`.`nm_thl` AS `nm_thl`,`q_attlog_detail`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_detail`.`masuk` AS `masuk`,`q_attlog_detail`.`keluar` AS `keluar`,`q_attlog_detail`.`kode_rule` AS `kode_rule`,`q_attlog_detail`.`id_det_status` AS `id_det_status`,`q_attlog_detail`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`q_attlog_detail`.`kode_status` AS `kode_status`,`q_attlog_detail`.`ket_det_status` AS `ket_det_status`,`q_attlog_detail`.`max_per_periode` AS `max_per_periode`,`q_attlog_detail`.`min_per_periode` AS `min_per_periode`,`q_attlog_detail`.`potongan_bef_min` AS `potongan_bef_min`,`q_attlog_detail`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`q_attlog_detail`.`potongan_aft_max` AS `potongan_aft_max`,`q_attlog_detail`.`kinerja` AS `kinerja`,`q_attlog_detail`.`automatic_status` AS `automatic_status`,`q_attlog_detail`.`override_value` AS `override_value`,`q_attlog_detail`.`red_add` AS `red_add`,`q_attlog_detail`.`ket_status` AS `ket_status`,`func_absen_potong`(`q_attlog_detail`.`tgl_kerja`,`q_attlog_detail`.`id_thl`,`q_attlog_detail`.`id_det_status`) AS `potong` from 
                (select `q_attlog`.`tgl_kerja` AS `tgl_kerja`,`q_attlog`.`is_libur` AS `is_libur`,`q_attlog`.`jam_masuk` AS `jam_masuk`,`q_attlog`.`jam_keluar` AS `jam_keluar`,`q_attlog`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_attlog`.`final` AS `final`,`q_attlog`.`id_thl` AS `id_thl`,`q_attlog`.`nm_thl` AS `nm_thl`,`q_attlog`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog`.`masuk` AS `masuk`,`q_attlog`.`keluar` AS `keluar`,`q_attlog`.`kode_rule` AS `kode_rule`,`puprarsip`.`tb_absen_detstatus_rule`.`id_det_status` AS `id_det_status`,`puprarsip`.`tb_absen_detstatus_rule`.`ket_detstatus_rule` AS `ket_detstatus_rule`,`puprarsip`.`tb_absen_detail_status`.`kode_status` AS `kode_status`,`puprarsip`.`tb_absen_detail_status`.`ket_det_status` AS `ket_det_status`,`puprarsip`.`tb_absen_detail_status`.`max_per_periode` AS `max_per_periode`,`puprarsip`.`tb_absen_detail_status`.`min_per_periode` AS `min_per_periode`,`puprarsip`.`tb_absen_detail_status`.`potongan_bef_min` AS `potongan_bef_min`,`puprarsip`.`tb_absen_detail_status`.`potongan_betw_min_max` AS `potongan_betw_min_max`,`puprarsip`.`tb_absen_detail_status`.`potongan_aft_max` AS `potongan_aft_max`,`puprarsip`.`tb_absen_detail_status`.`kinerja` AS `kinerja`,`puprarsip`.`tb_absen_detail_status`.`automatic_status` AS `automatic_status`,`puprarsip`.`tb_absen_detail_status`.`override_value` AS `override_value`,`puprarsip`.`tb_absen_detail_status`.`red_add` AS `red_add`,`puprarsip`.`tb_absen_status`.`ket_status` AS `ket_status` from (((
                    (select `q_all_date`.`tgl_kerja` AS `tgl_kerja`,`q_all_date`.`is_libur` AS `is_libur`,`q_all_date`.`jam_masuk` AS `jam_masuk`,`q_all_date`.`jam_keluar` AS `jam_keluar`,`q_all_date`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`q_all_date`.`final` AS `final`,`q_all_date`.`id_thl` AS `id_thl`,`q_all_date`.`nm_thl` AS `nm_thl`,`q_all_date`.`non_aktif_thl` AS `non_aktif_thl`,`q_att_data`.`masuk` AS `masuk`,`q_att_data`.`keluar` AS `keluar`,`func_absen_rule`(`q_all_date`.`tgl_kerja`,`q_all_date`.`id_thl`,`q_att_data`.`masuk`,`q_att_data`.`keluar`,`q_all_date`.`jam_masuk`,`q_all_date`.`jam_keluar`) AS `kode_rule` from (
                        (select `puprarsip`.`tb_absen_jam_kerja`.`tgl_kerja` AS `tgl_kerja`,`puprarsip`.`tb_absen_jam_kerja`.`is_libur` AS `is_libur`,`puprarsip`.`tb_absen_jam_kerja`.`jam_masuk` AS `jam_masuk`,`puprarsip`.`tb_absen_jam_kerja`.`jam_keluar` AS `jam_keluar`,`puprarsip`.`tb_absen_jam_kerja`.`ket_tgl_kerja` AS `ket_tgl_kerja`,`puprarsip`.`tb_absen_jam_kerja`.`final` AS `final`,`puprarsip`.`tb_absen_thl`.`id_thl` AS `id_thl`,`puprarsip`.`tb_absen_thl`.`nm_thl` AS `nm_thl`,`puprarsip`.`tb_absen_thl`.`non_aktif_thl` AS `non_aktif_thl` from (`puprarsip`.`tb_absen_jam_kerja` join `puprarsip`.`tb_absen_thl`)) AS `q_all_date`
                    left join 
                        (select `q_check_in`.`the_date` AS `the_date`,`q_check_in`.`id_thl` AS `id_thl`,`q_check_in`.`pagi` AS `masuk`,`q_check_out`.`sore` AS `keluar` from (
                            (select cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,`puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,min(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `pagi` from `puprarsip`.`tb_absen_attlog` WHERE ".$fltr1." group by cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),`puprarsip`.`tb_absen_attlog`.`id_thl`) AS `q_check_in`
                        left join 
                            (select cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,`puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,max(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `sore` from `puprarsip`.`tb_absen_attlog` WHERE ".$fltr1." group by cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),`puprarsip`.`tb_absen_attlog`.`id_thl`) AS `q_check_out`
                        on(((`q_check_in`.`the_date` = `q_check_out`.`the_date`) and (`q_check_in`.`id_thl` = `q_check_out`.`id_thl`) and (`q_check_in`.`pagi` <> `q_check_out`.`sore`))))) AS `q_att_data`
                    on(((`q_all_date`.`tgl_kerja` = `q_att_data`.`the_date`) and (`q_all_date`.`id_thl` = `q_att_data`.`id_thl`))))) AS `q_attlog`
                join `puprarsip`.`tb_absen_detstatus_rule` on((`puprarsip`.`tb_absen_detstatus_rule`.`kode_rule` = `q_attlog`.`kode_rule`))) join `puprarsip`.`tb_absen_detail_status` on((`puprarsip`.`tb_absen_detail_status`.`id_det_status` = `puprarsip`.`tb_absen_detstatus_rule`.`id_det_status`))) join `puprarsip`.`tb_absen_status` on((`puprarsip`.`tb_absen_status`.`kode_status` = `puprarsip`.`tb_absen_detail_status`.`kode_status`)))) AS `q_attlog_detail`
            ) AS `q_attlog_override_value`
        ) AS `q_attlog_main`
    where (`q_attlog_main`.`final` = '0')) AS `q_attlog_unfinal` 
    WHERE ".$fltr2."
    group by `q_attlog_unfinal`.`tgl_kerja`,`q_attlog_unfinal`.`id_thl`,`q_attlog_unfinal`.`kode_rule`
    ORDER by tgl_kerja asc, nm_thl ASC
    ";

    //echo $sql;
    $res=$con->query($sql);

    //echo $dBln;

    
    
?>



<div  class="page-header">
    <h1>Daftar Hadir</h1>
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

    $idThl=0;
    $nmThl="";
    $tglKerja=array();
    $jam=array();
    $jamTarget=array();
    $status=array();
    $ket=array();
    $libur=array();
    $redAdd=array();

    if($res->num_rows>0){
        while($row=$res->fetch_assoc()){
            $wlk=dateDayInt($row["tgl_kerja"]);
            $idThl=$row["id_thl"];
            $nmThl=$row["nm_thl"];
            $tglKerja[$wlk]=$row["tgl_kerja"];
            $jam[$wlk]=$row["jam"];
            $jamTarget[$wlk]=$row["jam_target"];
            $status[$wlk]=$row["kode_rule"];
            $ket[$wlk]=$row["ket_tgl_kerja"];
            $libur[$wlk]=(int)$row["is_libur"];
            $redAdd[$wlk]=(int)$row["red_add"];
            //echo $libur[$wlk]."<br>";
?>
<input type="hidden" id="<?php echo "jam".$wlk; ?>" value="<?php echo $jam[$wlk]; ?>">
<input type="hidden" id="<?php echo "jamTarget".$wlk; ?>" value="<?php echo $jamTarget[$wlk]; ?>">
<input type="hidden" id="<?php echo "ket".$wlk; ?>" value="<?php echo $ket[$wlk]; ?>">
<input type="hidden" id="<?php echo "lbr".$wlk; ?>" value="<?php echo $libur[$wlk]; ?>">
<?php
        }


?>

<div>
    
    <div id="tt" style="visibility:hidden; padding:8px; position:absolute; background-color:rgba(100,100,100,0.9); color:whitesmoke; border:solid; border-width:thin; border-radius:10px">
        <div id="ttKet1" align="center"></div>
        <div id="ttKet2" class="blink" align="center"></div>
        <div id="ttKet3" align="center"></div>
    </div>
    <h2><?php echo $nmThl; ?></h2>
    <table class="table table-fit">
        <thead>
            <tr>
                <th style="text-align:center">Sen</th>
                <th style="text-align:center">Sel</th>
                <th style="text-align:center">Rab</th>
                <th style="text-align:center">Kam</th>
                <th style="text-align:center">Jum</th>
                <th style="text-align:center">Sab</th>
                <th style="text-align:center">Min</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $calThn=$dThn;
            $calBln=$dBln;
            $wDate=DateTime::createFromFormat("Y-m-d", $calThn."-".str_pad($calBln,2,"0",STR_PAD_LEFT)."-01");
            $brs=1;
            do{
                if(dateMonthInt($wDate->format("Y-m-d"))==$calBln){
            ?>
            <tr>
            <?php
                    for($i=1;$i<=7;$i++){
                        if(dateDayWeekInt($wDate->format("Y-m-d"))==$i && dateMonthInt($wDate->format("Y-m-d"))==$calBln){
                            $bgclor1="whitesmoke";
                            $bgclor2="green";
                            $clor1="black";
                            $clor2="white";
                            if($libur[dateDayInt($wDate->format("Y-m-d"))]){
                                $bgclor1="darkred";
                                $bgclor2="whitesmoke";
                                $clor1="white";
                                $clor2="black";
                            }else{
                                $persen=(int) $redAdd[dateDayInt($wDate->format("Y-m-d"))];
                                $red=round($persen/100*255);
                                if($red>255)$red=255;
                                if($red!=0)$bgclor2="rgb(".$red.",0,0)";
                                if($wDate->format("Y-m-d")>date("Y-m-d"))$bgclor2="green";
                            }

                            
            ?>
                <td style="padding:5px; text-align:center">
                    <div id="<?php echo "tgl".dateDayInt($wDate->format("Y-m-d")); ?>" name="<?php echo (($brs<3)?"bwh_":"ats_").(($i<5)?"kn":"kr"); ?>" class="jmocaldate" style="border:solid; border-width:thin; border-radius:10px; margin:1px; vertical-align:middle; width:52px;height:52px; padding:1px">
                        <div style="height:26px; width:48px; font-weight:bold; border-top-left-radius:10px; border-top-right-radius:10px; margin:1px; font-size:20px; background-color:<?php echo $bgclor1; ?>; color:<?php echo $clor1; ?>">
            <?php
                            echo str_pad(dateDayInt($wDate->format("Y-m-d")),2,"0",STR_PAD_LEFT);
            ?>
                        </div>
                        <div style="margin:1px; width:48px; height:20px; font-weight:bold; border:solid; border-width:thin; border-bottom-left-radius:10px; border-bottom-right-radius:10px; font-size:12px; background-color:<?php echo $bgclor2; ?>; color:<?php echo $clor2; ?>">
            <?php
                            if($libur[dateDayInt($wDate->format("Y-m-d"))]){
                                echo "Libur";
                            }else{
                                if(date_create($wDate->format("Y-m-d"))<=date_create(date("Y-m-d"))){
                                    echo $status[dateDayInt($wDate->format("Y-m-d"))];
                                }
                            }
            ?>
                        </div>
                    </div>
                </td>
            <?php
                            date_add($wDate,date_interval_create_from_date_string("1 day"));
                        }else{
            ?>
                <td style="padding:5px; text-align:center">
                    <div style="border:solid; border-width:thin; border-radius:10px; margin:1px; vertical-align:middle; width:52px;height:52px; padding:1px; background-color:darkgray">
                        <div>
            <?php
                            echo "&nbsp;";
            ?>
                        </div>
                    </div>
                </td>
            <?php
                            
                        }
                    }
            ?>
                
            </tr>
            <?php
                }else{
                    break;
                }
                $brs++;
            }while(dateMonthInt($wDate->format("Y-m-d"))==$calBln);
            ?>
        </tbody>
        
    </table>
</div>
<?php
    }


?>    
  