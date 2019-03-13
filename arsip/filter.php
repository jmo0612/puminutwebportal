<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$cari= getPost("cari");
$totPage=0;
if(getGet("tPage")!=NULL){
    $totPage= getGet("tPage");
}else{
    $sqlStr="SELECT `tb_lembar`.`tahun` AS `tahun`, `tb_lembar`.`kd_prog` AS `kd_prog`, `tb_lembar`.`kd_keg` AS `kd_keg`, `tb_lembar`.`no_spm` AS `no_spm`, `tb_lembar`.`no_sp2d` AS `no_sp2d`, `tb_lembar`.`nm_file` AS `nm_file`, `tb_lembar`.`no_lembar` AS `no_lembar`, `tb_lembar`.`ocr` AS `ocr`, `tb_file`.`id_file_index` AS `id_file_index`, `tb_file`.`url_file` AS `url_file`, `tb_file`.`uploader_id` AS `uploader_id`, `tb_file_index`.`nm_file_index` AS `nm_file_index`, `tb_file_index`.`is_fisik` AS `is_fisik`, `tb_user`.`email_user` AS `email_user`, `tb_user`.`pass_user` AS `pass_user`, `tb_user`.`jns_user` AS `jns_user`, `tb_user`.`id_user_tipe` AS `id_user_tipe`, `tb_user`.`token` AS `token`, `tb_user`.`aktif` AS `aktif`, `tb_user`.`nm_user` AS `nm_user`, `tb_user`.`url_foto` AS `url_foto`, `tb_keg`.`ket_keg` AS `ket_keg`, `tb_keg`.`anggaran` AS `anggaran`, `tb_prog`.`ket_program` AS `ket_program`, `tb_sp2d`.`tgl_sp2d` AS `tgl_sp2d`, `tb_sp2d`.`uraian_sp2d` AS `uraian_sp2d`, `tb_sp2d`.`nilai_sp2d` AS `nilai_sp2d`, `tb_sp2d`.`ket_sp2d` AS `ket_sp2d`, `tb_spm`.`tgl_spm` AS `tgl_spm`, `tb_spm`.`uraian_spm` AS `uraian_spm`, `tb_spm`.`nilai_spm` AS `nilai_spm`, `tb_spm`.`ket_spm` AS `ket_spm` FROM `puprarsip`.`tb_file` AS `tb_file`, `puprarsip`.`tb_lembar` AS `tb_lembar`, `puprarsip`.`tb_keg` AS `tb_keg`, `puprarsip`.`tb_prog` AS `tb_prog`, `puprarsip`.`tb_sp2d` AS `tb_sp2d`, `puprarsip`.`tb_spm` AS `tb_spm`, `puprarsip`.`tb_file_index` AS `tb_file_index`, `puprarsip`.`tb_user` AS `tb_user` WHERE `tb_file`.`tahun` = `tb_lembar`.`tahun` AND `tb_file`.`kd_prog` = `tb_lembar`.`kd_prog` AND `tb_file`.`kd_keg` = `tb_lembar`.`kd_keg` AND `tb_file`.`no_spm` = `tb_lembar`.`no_spm` AND `tb_file`.`no_sp2d` = `tb_lembar`.`no_sp2d` AND `tb_file`.`nm_file` = `tb_lembar`.`nm_file` AND `tb_keg`.`tahun` = `tb_file`.`tahun` AND `tb_keg`.`kd_prog` = `tb_file`.`kd_prog` AND `tb_keg`.`kd_keg` = `tb_file`.`kd_keg` AND `tb_prog`.`tahun` = `tb_keg`.`tahun` AND `tb_prog`.`kd_prog` = `tb_keg`.`kd_prog` AND `tb_sp2d`.`no_spm` = `tb_file`.`no_spm` AND `tb_sp2d`.`no_sp2d` = `tb_file`.`no_sp2d` AND `tb_spm`.`no_spm` = `tb_sp2d`.`no_spm` AND `tb_file_index`.`id_file_index` = `tb_file`.`id_file_index` AND `tb_user`.`id_user` = `tb_file`.`uploader_id`";
    $sqlStr.= filterTahunSql(FALSE, "tb_lembar"). filterTriwulanSql(FALSE, "tb_sp2d"). filterCari(FALSE);
    $sqlStr.=" order by tahun asc, kd_prog asc, kd_keg asc, tgl_sp2d asc, tgl_spm asc, no_spm asc, no_sp2d asc, nm_file_index asc, no_lembar asc";
    $res=$con->query($sqlStr);
    $totPage= ceil($res->num_rows/100);
}

$curPage=0;
if(getGet("cPage")!=NULL){
    $curPage= getGet("cPage");
}else{
    if($totPage>0)$curPage=1;
}

?>
<nav class="navbar navbar-header">
    <h3>Hasil Pencarian</h3>
    <?php
        if($totPage>0){
            ?>
    <nav aria-label="Search results pages">
        <ul class="pagination">
            <?php
                if($curPage>1){
                    ?>
            <li>
                <a class="navLink" href="?tPage=<?php echo $totPage; ?>&cPage=1" aria-label="First"  onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=1')">
                    <span aria-hidden="true">&lt;&lt;</span>
                </a>
            </li>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage-1; ?>" aria-label="Previous" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage-1; ?>')">
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            
                    <?php
                }
                $p0=$curPage-2;
                if($p0<1)$p0=1;
                $p1=$p0+4;
                if($p1>$totPage)$p1=$totPage;
                for($i=$p0;$i<=$p1;$i++){
                    ?>
            <li class="<?php if($i==$curPage) echo 'active'; ?>"><a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $i; ?>" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $i; ?>')"><?php echo $i; ?></a></li>            
                    <?php
                }
                if($curPage<$totPage){
                    ?>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage+1; ?>" aria-label="Next" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage+1; ?>')">
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $totPage; ?>" aria-label="Last" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $totPage; ?>')">
                    <span aria-hidden="true">&gt;&gt;</span>
                </a>
            </li>
                    <?php
                }
            ?>
            
        </ul>
    </nav>            
            <?php
        }
    ?>
    
</nav>

<div class="row container-fluid">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form name="frm" id="frm" action="" method="post">
            <input id="hTahun" name="tahun" type="hidden" value=""/>
            <input id="hTriwulan" name="triwulan" type="hidden" value=""/>
            <input id="hCari" name="cari" type="hidden" value=""/>
        </form>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Arsip</th>
                    <th scope="col">SP2D</th>
                    <th scope="col">Kegiatan</th>
                    <th scope="col">Program</th>
                </tr>
                
                
                    <?php
                        $sqlStr="SELECT `tb_lembar`.`tahun` AS `tahun`, `tb_lembar`.`kd_prog` AS `kd_prog`, `tb_lembar`.`kd_keg` AS `kd_keg`, `tb_lembar`.`no_spm` AS `no_spm`, `tb_lembar`.`no_sp2d` AS `no_sp2d`, `tb_lembar`.`nm_file` AS `nm_file`, `tb_lembar`.`no_lembar` AS `no_lembar`, `tb_lembar`.`ocr` AS `ocr`, `tb_file`.`id_file_index` AS `id_file_index`, `tb_file`.`url_file` AS `url_file`, `tb_file`.`uploader_id` AS `uploader_id`, `tb_file_index`.`nm_file_index` AS `nm_file_index`, `tb_file_index`.`is_fisik` AS `is_fisik`, `tb_user`.`email_user` AS `email_user`, `tb_user`.`pass_user` AS `pass_user`, `tb_user`.`jns_user` AS `jns_user`, `tb_user`.`id_user_tipe` AS `id_user_tipe`, `tb_user`.`token` AS `token`, `tb_user`.`aktif` AS `aktif`, `tb_user`.`nm_user` AS `nm_user`, `tb_user`.`url_foto` AS `url_foto`, `tb_keg`.`ket_keg` AS `ket_keg`, `tb_keg`.`anggaran` AS `anggaran`, `tb_prog`.`ket_program` AS `ket_program`, `tb_sp2d`.`tgl_sp2d` AS `tgl_sp2d`, `tb_sp2d`.`uraian_sp2d` AS `uraian_sp2d`, `tb_sp2d`.`nilai_sp2d` AS `nilai_sp2d`, `tb_sp2d`.`ket_sp2d` AS `ket_sp2d`, `tb_spm`.`tgl_spm` AS `tgl_spm`, `tb_spm`.`uraian_spm` AS `uraian_spm`, `tb_spm`.`nilai_spm` AS `nilai_spm`, `tb_spm`.`ket_spm` AS `ket_spm` FROM `puprarsip`.`tb_file` AS `tb_file`, `puprarsip`.`tb_lembar` AS `tb_lembar`, `puprarsip`.`tb_keg` AS `tb_keg`, `puprarsip`.`tb_prog` AS `tb_prog`, `puprarsip`.`tb_sp2d` AS `tb_sp2d`, `puprarsip`.`tb_spm` AS `tb_spm`, `puprarsip`.`tb_file_index` AS `tb_file_index`, `puprarsip`.`tb_user` AS `tb_user` WHERE `tb_file`.`tahun` = `tb_lembar`.`tahun` AND `tb_file`.`kd_prog` = `tb_lembar`.`kd_prog` AND `tb_file`.`kd_keg` = `tb_lembar`.`kd_keg` AND `tb_file`.`no_spm` = `tb_lembar`.`no_spm` AND `tb_file`.`no_sp2d` = `tb_lembar`.`no_sp2d` AND `tb_file`.`nm_file` = `tb_lembar`.`nm_file` AND `tb_keg`.`tahun` = `tb_file`.`tahun` AND `tb_keg`.`kd_prog` = `tb_file`.`kd_prog` AND `tb_keg`.`kd_keg` = `tb_file`.`kd_keg` AND `tb_prog`.`tahun` = `tb_keg`.`tahun` AND `tb_prog`.`kd_prog` = `tb_keg`.`kd_prog` AND `tb_sp2d`.`no_spm` = `tb_file`.`no_spm` AND `tb_sp2d`.`no_sp2d` = `tb_file`.`no_sp2d` AND `tb_spm`.`no_spm` = `tb_sp2d`.`no_spm` AND `tb_file_index`.`id_file_index` = `tb_file`.`id_file_index` AND `tb_user`.`id_user` = `tb_file`.`uploader_id`";
                        $sqlStr.= filterTahunSql(FALSE, "tb_lembar"). filterTriwulanSql(FALSE, "tb_sp2d"). filterCari(FALSE);
                        $sqlStr.=" order by tahun asc, kd_prog asc, kd_keg asc, tgl_sp2d asc, tgl_spm asc, no_spm asc, no_sp2d asc, nm_file_index asc, no_lembar asc";
                        $sqlStr.=" LIMIT 100 OFFSET ".($curPage-1)*100;
                        //$sqlStr.=" LIMIT 100";
                        $res=$con->query($sqlStr);
                        if($res->num_rows>0){
                            $n=(($curPage-1)*100)+1;
                            while($row=$res->fetch_assoc()){
                                ?>
                <tr>
                    <th scope="row"><?php echo $n; ?></th>
                    <td><a href="<?php echo $row["url_file"]."#page=".$row["no_lembar"]; ?>"><?php echo $row["nm_file_index"]." [Lembar-".$row["no_lembar"]."]"; ?></a></td>                
                    <td>
                        <h6><?php echo $row["no_sp2d"]." [". dateMedium($row["tgl_sp2d"])."]"; ?></h6>
                        <p><?php echo $row["uraian_sp2d"]; ?></p>
                    </td>
                    <td><?php echo $row["ket_keg"]; ?></td>
                    <td><?php echo $row["ket_program"]; ?></td>
                </tr>
                
                                <?php
                                $n++;
                            }
                        }
                    ?>
                
            </table>
        </div>
    </div>
</div>

<nav class="navbar navbar-header">
    <h3>Hasil Pencarian</h3>
    <?php
        if($totPage>0){
            ?>
    <nav aria-label="Search results pages">
        <ul class="pagination">
            <?php
                if($curPage>1){
                    ?>
            <li>
                <a class="navLink" href="?tPage=<?php echo $totPage; ?>&cPage=1" aria-label="First"  onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=1')">
                    <span aria-hidden="true">&lt;&lt;</span>
                </a>
            </li>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage-1; ?>" aria-label="Previous" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage-1; ?>')">
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            
                    <?php
                }
                $p0=$curPage-2;
                if($p0<1)$p0=1;
                $p1=$p0+4;
                if($p1>$totPage)$p1=$totPage;
                for($i=$p0;$i<=$p1;$i++){
                    ?>
            <li class="<?php if($i==$curPage) echo 'active'; ?>"><a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $i; ?>" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $i; ?>')"><?php echo $i; ?></a></li>            
                    <?php
                }
                if($curPage<$totPage){
                    ?>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage+1; ?>" aria-label="Next" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $curPage+1; ?>')">
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li>
                <a class="navLink"  href="?tPage=<?php echo $totPage; ?>&cPage=<?php echo $totPage; ?>" aria-label="Last" onclick="return setSubmit('?tPage=<?php echo $totPage; ?>&cPage=<?php echo $totPage; ?>')">
                    <span aria-hidden="true">&gt;&gt;</span>
                </a>
            </li>
                    <?php
                }
            ?>
            
        </ul>
    </nav>            
            <?php
        }
    ?>
    
</nav>

<script type="text/javascript">
    
    function submitMe(){
        //alert(document.getElementById("triwulan").getAttribute("value"));
        document.getElementById("hTahun").setAttribute("value",document.getElementById("tahun").value);
        document.getElementById("hTriwulan").setAttribute("value",document.getElementById("triwulan").value);
        document.getElementById("hCari").setAttribute("value",document.getElementById("cari").value);
        document.forms["frm"].submit();
    }
    function setSubmit(phpPage){
        //document.getElementById("frm").setAttribute("action","http://192.168.1.2/arsip/"+phpPage);
        document.getElementById("frm").setAttribute("action","http://localhost/arsip/"+phpPage);
        //alert(document.getElementById("frm").action);
        submitMe();
        return false;
    }
</script>