
<div class="row container-fluid">
    <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
        <form name="frm" id="frm" action="" method="post">
            <input id="hTahun" name="tahun" type="hidden" value=""/>
            <input id="hTriwulan" name="triwulan" type="hidden" value=""/>
            <input id="hCari" name="cari" type="hidden" value=""/>
            <input id="hSpm" name="hSpm" type="hidden" value=""/>
            <input id="hSp2d" name="hSp2d" type="hidden" value=""/>
            <div class="form-group">
                <label for="exProg">Program</label>
                <select class="form-control" id="prog" name="prog" onchange="submitMe()">
                    <option value=""> - </option>
                    <?php
                        $res=$con->query("select * from tb_prog ". filterTahunSql()." order by tahun asc, kd_prog asc");
                        if($res->num_rows>0){
                            while($row=$res->fetch_assoc()){
                                ?>
                    <option value="<?php echo $row["kd_prog"] ?>" <?php if(getPost("prog")==$row["kd_prog"]) echo 'selected'; ?>><?php echo "[".$row["tahun"]."] ".$row["kd_prog"]." - ".$row["ket_program"] ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exKeg">Kegiatan</label>
                <select class="form-control" id="keg" name="keg" onchange="submitMe()">
                    <option value=""> - </option>
                    <?php
                        $res=$con->query("select * from tb_keg where kd_prog=". getPost("prog") . filterTahunSql(FALSE)." order by tahun asc, ket_keg asc");
                        if($res->num_rows>0){
                            while($row=$res->fetch_assoc()){
                                ?>
                    <option value="<?php echo $row["kd_keg"] ?>" <?php if(getPost("keg")==$row["kd_keg"]) echo 'selected'; ?>><?php echo "[".$row["tahun"]."] ".getPost("prog").".",$row["kd_keg"]." - ".$row["ket_keg"] ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
            
        </form>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <img src="images/defs.jpg" class="img-thumbnail" onerror="this.src='images/def.jpg'">
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="list-group">
            <?php
                if(getPost("keg")!=NULL){
                    $sqlStr="SELECT DISTINCT `tb_sp2d`.`no_spm` AS `no_spm`, `tb_sp2d`.`no_sp2d` AS `no_sp2d`, `tb_sp2d`.`tgl_sp2d` AS `tgl_sp2d`, `tb_sp2d`.`uraian_sp2d` AS `uraian_sp2d`, `tb_sp2d`.`nilai_sp2d` AS `nilai_sp2d`, `tb_sp2d`.`ket_sp2d` AS `ket_sp2d` FROM `puprarsip`.`tb_file` AS `tb_file`, `puprarsip`.`tb_sp2d` AS `tb_sp2d` WHERE `tb_file`.`no_spm` = `tb_sp2d`.`no_spm` AND `tb_file`.`no_sp2d` = `tb_sp2d`.`no_sp2d`";
                    $sqlStr.=" and tb_file.kd_prog=".getPost("prog")." and tb_file.kd_keg=".getPost("keg"). filterTahunSql(FALSE,"tb_file"). filterTriwulanSql(FALSE,"tb_sp2d")." order by tgl_sp2d asc";
                    $res=$con->query($sqlStr);
                    if($res->num_rows>0){
                        while($row=$res->fetch_assoc()){
                            ?>
            <a class="list-group-item <?php if((getPost("hSpm")==$row["no_spm"])&&(getPost("hSp2d")==$row["no_sp2d"])) echo 'active'; ?>" onclick="pencairan('<?php echo $row["no_spm"];?>','<?php echo $row["no_sp2d"];?>')">
                <h4 class="list-group-item-heading"><?php echo $row["no_sp2d"]."    (". dateMedium($row["tgl_sp2d"]).")"; ?></h4>
                <h5 class="list-group-item-heading"><?php echo money($row["nilai_sp2d"]); ?></h5>
                <p class="list-group-item-text"><?php echo $row["uraian_sp2d"] ?></p>
            </a>
            <div class="row container-fluid">
                <div id="arsip" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?php 
                        if((getPost("hSpm")==$row["no_spm"])&&(getPost("hSp2d")==$row["no_sp2d"])){
                            ?>
                    <div class="row jumbotron">
                        
                            <?php
                                $sqlStr2="SELECT `tb_file`.`tahun` AS `tahun`, `tb_file`.`kd_prog` AS `kd_prog`, `tb_file`.`kd_keg` AS `kd_keg`, `tb_file`.`no_spm` AS `no_spm`, `tb_file`.`no_sp2d` AS `no_sp2d`, `tb_file`.`nm_file` AS `nm_file`, `tb_file`.`id_file_index` AS `id_file_index`, `tb_file_index`.`nm_file_index` AS `nm_file_index`, `tb_file_index`.`is_fisik` AS `is_fisik`, `tb_file`.`url_file` AS `url_file` FROM `puprarsip`.`tb_file` AS `tb_file`, `puprarsip`.`tb_file_index` AS `tb_file_index` WHERE `tb_file`.`id_file_index` = `tb_file_index`.`id_file_index`";
                                $sqlStr2.= filterTahunSql(FALSE,"tb_file")." and tb_file.kd_prog=". getPost("prog")." and tb_file.kd_keg=". getPost("keg")." and tb_file.no_spm='". getPost("hSpm")."' and tb_file.no_sp2d='". getPost("hSp2d")."' order by nm_file_index asc";
                                $res2=$con->query($sqlStr2);
                                if($res2->num_rows>0){
                                    while($row2=$res2->fetch_assoc()){
                                        ?>
                        <a href="<?php echo $row2["url_file"]; ?>">
                            <div class="col-xs-12 col-sm-9 col-md-6 col-lg-3 well well-sm">
                                <?php echo $row2["nm_file_index"]; ?>
                            </div>
                        </a>
                                        <?php
                                    }
                                }
                            ?>
                        
                    </div>    
                            <?php
                        } 
                    ?>
                </div>
            </div>
                            <?php
                        }
                    }
                }
                
            ?>
        </div>
        
    </div>
</div>


<script type="text/javascript">
    /*var pencairan=function(){
        alert(this.id);
    }*/
    function submitMe(){
        //alert(document.getElementById("triwulan").getAttribute("value"));
        document.getElementById("hTahun").setAttribute("value",document.getElementById("tahun").value);
        document.getElementById("hTriwulan").setAttribute("value",document.getElementById("triwulan").value);
        document.getElementById("hCari").setAttribute("value",document.getElementById("cari").value);
        document.forms["frm"].submit();
    }
    
    function pencairan(spm,sp2d){
        document.getElementById("hSpm").setAttribute("value",spm);
        document.getElementById("hSp2d").setAttribute("value",sp2d);
        submitMe();
    }
    
    
</script>

<style type="text/css">
    .list-group-item.active, .list-group-item.active:hover {
    background-color: #222222;
    border-color: #aed248;
    }
</style>