<?php

if(getPost("txtTgl")){
        
    $sqlUp="update tb_absen_jam_kerja set jam_masuk='".getPost("txtTgl")." ".str_pad(getPost("ciH"),2,"0",STR_PAD_LEFT).":".str_pad(getPost("ciM"),2,"0",STR_PAD_LEFT).":00', jam_keluar='".getPost("txtTgl")." ".str_pad(getPost("coH"),2,"0",STR_PAD_LEFT).":".str_pad(getPost("coM"),2,"0",STR_PAD_LEFT).":00', ket_tgl_kerja='".getPost("txtKet")."', is_libur='".getPost("selLibur")."' where tgl_kerja='".getPost("txtTgl")."'";
    //echo $sqlUp;
    if($con->query($sqlUp)){
        ?>
        <div class="alert alert-success" role="alert">Data tersimpan.</div>
        <?php
    }else{
        ?>
        <div class="alert alert-danger" role="alert">Error. Gagal menyimpan.</div>
        <?php
    }
}

$validUrl=false;
if(getGet("edHk")){
    $curDateStr=getGet("edHk");
    $curDateArr=date_parse_from_format("Ymd",$curDateStr);
    if($curDateArr["error_count"]==0)$validUrl=true;
}
if($validUrl){
    $curDate=date_create($curDateArr["year"]."-".str_pad($curDateArr["month"],2,"0",STR_PAD_LEFT)."-".str_pad($curDateArr["day"],2,"0",STR_PAD_LEFT));
    $curDateStr=dateDBFormat(date_format($curDate,"Y-m-d"));

    $sql="select * from tb_absen_jam_kerja where tgl_kerja='".$curDateStr."'";
    $res=$con->query($sql);

    $status=0;
    $cin="";
    $cout="";
    $ket="";
    if($res->num_rows>0){
        $row=$res->fetch_assoc();
        $status=$row["is_libur"];
        $cin=date_parse_from_format("Y-m-d H:i:s",$row["jam_masuk"]);
        $cout=date_parse_from_format("Y-m-d H:i:s",$row["jam_keluar"]);
        $ket=$row["ket_tgl_kerja"];
    }else{
        $validUrl=false;
    }
}
if($validUrl){
        

?>
<div  class="page-header">
	<div>
        <h2>Edit Hari Kerja</h2>
        <h3><?php echo dateComplete($curDateStr); ?></h3>
    </div>  
    
</div>



<form id="frmEdHk" method="post">
    <input type="hidden" name="txtTgl" id="txtTgl" value="<?php echo $curDateStr; ?>">
    <table>
        <tr>
            <td colspan="5">
                <select class="form-control" name="selLibur" id="selLibur" value="<?php echo $status; ?>">
                    <option value="0" <?php echo ($status==0)?"selected":""; ?>>Hari Kerja</option>
                    <option value="1" <?php echo ($status==1)?"selected":""; ?>>Hari Libur</option>
                </select>
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td style="padding:3px;text-align:right;border-top:1px solid">Check-in : </td>
            <td style="padding:3px;border-top:1px solid">Jam</td>
            <td style="padding:3px;border-top:1px solid">
                <select class="form-control" name="ciH" id="ciH" value="<?php echo $cin["hour"]; ?>">
                    <?php
                        $hh=$cin["hour"];
                        for($j=0;$j<24;$j++){
                            if($j==$hh){
                                echo "<option value='".$j."' selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }else{
                                echo "<option value='".$j."'>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }
                            
                        }
                    ?>
                </select>
            </td>
            <td style="padding:3px;border-top:1px solid">Menit</td>
            <td style="padding:3px;border-top:1px solid;border-right:1px solid">
                <select class="form-control" name="ciM" id="ciM" value="<?php echo $cin["minute"]; ?>">
                    <?php
                        for($j=0;$j<60;$j++){
                            if($j==$cin["minute"]){
                                echo "<option value='".$j."' selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }else{
                                echo "<option value='".$j."'>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
                
        <tr>
            <td style="padding:3px;text-align:right">Check-out : </td>
            <td style="padding:3px">Jam</td>
            <td style="padding:3px">
                <select class="form-control" name="coH" id="coH" value="<?php echo $cout["hour"]; ?>">
                    <?php
                        $hh=$cout["hour"];
                        for($j=0;$j<24;$j++){
                            if($j==$hh){
                                echo "<option value='".$j."' selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }else{
                                echo "<option value='".$j."'>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }
                        }
                    ?>
                </select>
            </td>
            <td style="padding:3px">Menit</td>
            <td style="padding:3px;border-right:1px solid">
                <select class="form-control" name="coM" id="coM" value="<?php echo $cout["minute"]; ?>">
                    <?php
                        for($j=0;$j<60;$j++){
                            if($j==$cout["minute"]){
                                echo "<option value='".$j."' selected>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }else{
                                echo "<option value='".$j."'>".str_pad($j, 2, '0', STR_PAD_LEFT)."</option>";
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="5">
                <input type="text" class="form-control" placeholder="Keterangan" name="txtKet" id="txtKet" value="<?php echo $ket; ?>">
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="2" style="text-align:left">
                <button type="button" onclick="window.location='<?php echo getUrl('absen?p=adAl'.((getGet('dMn'))?'&dMn='.getGet('dMn'):'')); ?>';" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span><span style="margin:5px">Batal</span></button>
            </td>
            <td colspan="3" style="text-align:right">
                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span><span style="margin:5px">Simpan</span></button>
            </td>
        </tr>
    </table>
</form>





<?php
}
?>

