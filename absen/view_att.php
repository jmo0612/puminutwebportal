<?php

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
            ON tb_absen_thl_bulan.Thn='".dateYearInt($curDateDBStr)."' AND tb_absen_thl_bulan.bln='".dateMonthInt($curDateDBStr)."' order by tgl_kerja asc, id_thl asc ) AS all_date
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
            WHERE tgl_kerja='". $curDateDBStr ."' and tgl_kerja<='".date("Y-m-d")."' and is_libur='0' and nonaktif_thl='0' order by nm_thl asc";

    $res=$con->query($sql);
    
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
</div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Datang</th>
            <th>Pulang</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
<?php
    if($res->num_rows>0){
        $urt=1;
        while($row=$res->fetch_assoc()){
            //echo $row["nm_thl"]." --------> ".$row["kode_rule"]."<br>";
            ?>
        <tr onclick="window.location='<?php echo getUrl('absen/index.php?p=detAtt&idThl='.$row['id_thl']); ?>';">
            <th scope="row"><?php echo $urt; ?></td>
            <td><?php echo $row["nm_thl"]; ?></td>
            <td><?php echo dateClock($row["masuk"]); ?></td>
            <td><?php echo dateClock($row["keluar"]); ?></td>
            <td><?php echo $row["kode_rule"]; ?></td>
        </tr>    
            <?php
            $urt++;
        }
    }
?>    
    </tbody>
</table>
     