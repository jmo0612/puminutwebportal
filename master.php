<?php
date_default_timezone_set ( "Asia/Makassar");
setlocale (LC_TIME, 'id_ID.UTF8', 'id_ID.UTF-8', 'id_ID.8859-1', 'id_ID', 'IND.UTF8', 'IND.UTF-8', 'IND.8859-1', 'IND', 'Indonesian.UTF8', 'Indonesian.UTF-8', 'Indonesian.8859-1', 'Indonesian', 'Indonesia', 'id', 'ID', 'en_US.UTF8', 'en_US.UTF-8', 'en_US.8859-1', 'en_US', 'American', 'ENG', 'English');
    
$extraUrl="/portal";

$tahun=0;
$idUser="";
$nmUser="";
$emailUser="";
$urlPropic="";
$triwulan=0;
$isGuest=FALSE;

$lockTahun=FALSE;
$lockTriwulan=FALSE;

function initIndex(){
    global $tahun,$idUser,$nmUser,$emailUser,$urlPropic,$triwulan,$extraUrl,$isGuest,$lockTahun,$lockTriwulan,$con;
    if(isset($_SESSION["usrToken"])){
        $res=$con->query("select * from tb_user where token='" . $_SESSION["usrToken"]."' and aktif=1");
        if($res->num_rows==0){
            $isGuest=TRUE;
        }
    }


    if($isGuest){
        $res=$con->query("select * from tb_user where jns_user='Guest' and aktif=1");
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $idUser=$row["id_user"];
            $nmUser=$row["nm_user"];
            $emailUser=$row["email_user"];
            $urlPropic=$row["url_foto"];
            $res2=$con->query("select * from tb_token where token='".$_SESSION["usrToken"]."'");
            $tokenValid=TRUE;
            if($res2->num_rows>0){
                $row2=$res2->fetch_assoc();
                if(getPost("tahun")!=NULL){
                    $tahun= getPost("tahun");
                }else{
                    $tahun=$row2["tahun_akses"];
                }
                if($row2["tahun_akses"]>0)$lockTahun=TRUE;
                if(getPost("triwulan")!=NULL){
                    $triwulan=getPost("triwulan");
                }else{
                    $triwulan=$row2["triwulan_akses"];
                }
                if($row2["triwulan_akses"]>0)$lockTriwulan=TRUE;
                if($row2["limited_time"]){
                    $dateNow=new DateTime(date("Y-m-d"));
                    $dateToken= new DateTime($row2["expire_date"]);
                    if($dateNow>=$dateToken){
                        $tokenValid=FALSE;
                    }
                }
            }else{
                $tokenValid=FALSE;
            }
            if(!$tokenValid){
                redirect("login.php?exp=1");
            }
        }else{
            redirect("login.php?na=1");
        }
    }else{
        $sqlStr="SELECT `tb_user`.`id_user` AS `id_user`, `tb_user`.`email_user` AS `email_user`, `tb_user`.`pass_user` AS `pass_user`, `tb_user`.`jns_user` AS `jns_user`, `tb_user`.`id_user_tipe` AS `id_user_tipe`, `tb_user`.`token` AS `token`, `tb_user`.`aktif` AS `aktif`, `tb_user`.`nm_user` AS `nm_user`, `tb_user`.`url_foto` AS `url_foto`, `tb_token`.`require_login` AS `require_login`, `tb_token`.`limited_time` AS `limited_time`, `tb_token`.`expire_date` AS `expire_date`, `tb_token`.`tahun_akses` AS `tahun_akses`, `tb_token`.`triwulan_akses` AS `triwulan_akses` FROM `puprarsip`.`tb_user` AS `tb_user`, `puprarsip`.`tb_token` AS `tb_token` WHERE `tb_user`.`token` = `tb_token`.`token`";
        $sqlStr.=" and tb_user.id_user='".$_SESSION["usrId"]."' and tb_user.aktif=1";
        $res=$con->query($sqlStr);
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $idUser=$row["id_user"];
            $nmUser=$row["nm_user"];
            $emailUser=$row["email_user"];
            $urlPropic=$row["url_foto"];
            if(getPost("tahun")!=NULL){
                $tahun= getPost("tahun");
            }else{
                $tahun=$row["tahun_akses"];
            }
            if($row["tahun_akses"]>0)$lockTahun=TRUE;
            if(getPost("triwulan")!=NULL){
                $triwulan=getPost("triwulan");
            }else{
                $triwulan=$row["triwulan_akses"];
            }
            if($row["triwulan_akses"]>0)$lockTriwulan=TRUE;
            if($row["limited_time"]){
                $dateNow=new DateTime(date("Y-m-d"));
                $dateToken= new DateTime($row["expire_date"]);
                if($dateNow>=$dateToken){
                    redirect("login.php?exp=1");
                }
            }
        }else{
            redirect("login.php?inact=1");
        }
    }
}
