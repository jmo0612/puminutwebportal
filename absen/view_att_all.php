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

    $sql="
        select id_thl, nm_thl".$sel." FROM
            ".qtb_det($fltr1,$fltr2,$fltr3)."
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
     