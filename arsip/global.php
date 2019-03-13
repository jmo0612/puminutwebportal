<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//global $tahun,$idUser,$nmUser,$emailUser,$urlPropic,$triwulan,$extraUrl,$isGuest,$lockTahun,$lockTriwulan;

$cari="";

function filterCari($first=TRUE,$val=NULL){
    global $cari;
    $cr= $cari;
    if($val!=NULL)$cr=$val;
    $ret="1";
    if($cr!=NULL){
        if(noDoubleSpaceStr($cr)!=" "){
            $ret="`tb_lembar`.`no_spm` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_lembar`.`no_sp2d` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_lembar`.`ocr` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_file`.`id_file_index` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_file_index`.`nm_file_index` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_keg`.`ket_keg` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_prog`.`ket_program` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_sp2d`.`ket_sp2d` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_spm`.`uraian_spm` LIKE '". likeFormatStr($cr)."'";
            $ret.=" OR `tb_spm`.`ket_spm` LIKE '". likeFormatStr($cr)."'";
            if(isNumber(noDoubleSpaceStr($cr))){
                $ret.=" OR `tb_keg`.`anggaran` = ". noDoubleSpaceStr($cr);
                $ret.=" OR `tb_sp2d`.`nilai_sp2d` = ". noDoubleSpaceStr($cr);
                $ret.=" OR `tb_spm`.`nilai_spm` = ". noDoubleSpaceStr($cr);
            }
            if(isDate(noDoubleSpaceStr($cr))){
                $ret.=" OR `tb_sp2d`.`tgl_sp2d` = {d '". dateDBFormat(noDoubleSpaceStr($cr))."' }";
                $ret.=" OR `tb_spm`.`tgl_spm` = {d '". dateDBFormat(noDoubleSpaceStr($cr))."' }";
            }
        }
    }
    $ret="(".$ret.")";
    if($first){
        return " where ".$ret;
    }else{
        return " and ".$ret;
    }
}

function filterTahunSql($first=TRUE,$tbName="",$val=NULL){
    global $tahun;
    $thn= $tahun;
    if($val!=NULL)$thn=$val;
    if($tbName!="")$tbName.=".";
    $ret="1";
    if($thn>0){
        $ret=$tbName."tahun=".$thn;
    }
    if($first){
        return " where ".$ret;
    }else{
        return " and ".$ret;
    }
}

function filterTriwulanSql($first=TRUE,$tbName="",$val=NULL){
    global $triwulan;
    $tri=$triwulan;
    if($val!=NULL)$tri=$val;
    if($tbName!="")$tbName.=".";
    $ret="1";
    if($tri>0){
        if($tri==1){
            $m1=1;
            $m2=2;
            $m3=3;
        }elseif($tri==2){
            $m1=4;
            $m2=5;
            $m3=6;
        }elseif($tri==3){
            $m1=7;
            $m2=8;
            $m3=9;
        }else{
            $m1=10;
            $m2=11;
            $m3=12;
        }
        $ret="DATE_FORMAT(".$tbName."tgl_sp2d,'%c')=".$m1;
        $ret.=" or "."DATE_FORMAT(".$tbName."tgl_sp2d,'%c')=".$m2;
        $ret.=" or "."DATE_FORMAT(".$tbName."tgl_sp2d,'%c')=".$m3;
        $ret="(".$ret.")";
    }
    if($first){
        return " where ".$ret;
    }else{
        return " and ".$ret;
    }
}



