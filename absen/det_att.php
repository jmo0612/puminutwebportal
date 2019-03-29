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

    


    $dBln=dateMonthInt($curDateDBStr);
    $dThn=dateYearInt($curDateDBStr);

    $fltr1="Year(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dThn."' and Month(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dBln."'";
    $fltr2="Year(`q_check_in`.`the_date`)='". $dThn ."' and Month(`q_check_in`.`the_date`)='". $dBln ."'";
    $fltr3="Year(`tgl_kerja`)='". $dThn ."' and Month(`tgl_kerja`)='". $dBln ."'";

    //$fltr1="Year(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dThn."' and Month(cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date))='".$dBln."'";
    //$fltr2="id_thl='".getGet("idThl")."' and Year(`tgl_kerja`)='". $dThn ."' and Month(`tgl_kerja`)='". $dBln ."' and `non_aktif_thl`='0'";

    $sql="select `q_attlog_unfinal`.`tgl_kerja` AS `tgl_kerja`,`q_attlog_unfinal`.`id_thl` AS `id_thl`,`q_attlog_unfinal`.`nm_thl` AS `nm_thl`,`q_attlog_unfinal`.`jam` AS `jam`,`q_attlog_unfinal`.`kode_rule` AS `kode_rule`,`q_attlog_unfinal`.`is_libur` AS `is_libur`,`q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,`q_attlog_unfinal`.`jam_target` AS `jam_target`,`q_attlog_unfinal`.`ket_tgl_kerja` AS `ket_tgl_kerja`,sum(`q_attlog_unfinal`.`red_add`) AS `red_add`,`q_attlog_unfinal`.`jam_masuk` as `jam_masuk` from 
    ".q_attlog_unfinal($fltr1,$fltr2,$fltr3)." 
    WHERE ".$fltr3." and id_thl='".getGet("idThl")."' and `non_aktif_thl`='0'
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
  