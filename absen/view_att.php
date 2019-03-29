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
            <th>Jam</th>
            <th>Status</th>
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
            <th scope="row"><?php echo $urt; ?></td>
            <td><?php echo $row["nm_thl"]; ?></td>
            <td><?php echo $row["jam"]; ?></td>
            <th style="color:rgb(<?php echo $red; ?>,0,0)"><?php echo $row["kode_rule"]; ?></th>
        </tr>    
            <?php
            $urt++;
        }
    }
    
?>    
    </tbody>
</table>
     