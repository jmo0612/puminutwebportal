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
            $stm="GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',masuk,'') SEPARATOR '') as "."M".$rowD["tgl"];
            $stm.=", GROUP_CONCAT(IF(tgl_kerja='".$rowD["tgl_kerja"]."',keluar,'') SEPARATOR '') as "."K".$rowD["tgl"];
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
            tb_absen_thl.id_thl as id_thl,
            tb_absen_thl.nm_thl as nm_thl,
            tb_absen_thl.nonaktif_thl as nonaktif_thl
            FROM
            tb_absen_jam_kerja
            cross JOIN
            tb_absen_thl
            order by tgl_kerja asc, id_thl asc ) AS all_date
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
            WHERE is_libur='0' and nonaktif_thl='0' 
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
            <th colspan="3"  style="text-align: center"><?php echo $theDates[$i]."<br>(".dateDayWeekMin($dThn."-".str_pad($dBln,2,"0",STR_PAD_LEFT)."-".str_pad($theDates[$i],2,"0",STR_PAD_LEFT)).")"; ?></th>
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
            <th style="text-align: center;width: 10px;min-width: 10px">D</th>
            <th style="text-align: center;width: 10px;min-width: 10px">P</th>
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
            ?>
            <td style="text-align: center"><?php echo dateClockMin($row["M".$i]); ?></td>
            <td style="text-align: center"><?php echo dateClockMin($row["K".$i]); ?></td>
            <td style="text-align: center"><?php echo $row["S".$i]; ?></td>
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
     