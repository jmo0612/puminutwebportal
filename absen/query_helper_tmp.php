<?php
    function q_check_in($fltrTgl,$fltrId){
        return "
        
            (
                select 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,
                    `puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,
                    min(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `pagi` 
                    
                from 
                `puprarsip`.`tb_absen_attlog` 

                WHERE 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date)=".$fltrTgl." and
                    `puprarsip`.`tb_absen_attlog`.`id_thl`=".$fltrId." 

                group by 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),
                    `puprarsip`.`tb_absen_attlog`.`id_thl`

            ) AS `q_check_in`
        
        ";
    }

    function q_check_out($fltrTgl,$fltrId){
        return "
        
            (
                select 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,
                    `puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,
                    max(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `sore` 
                    
                from 
                    `puprarsip`.`tb_absen_attlog` 

                WHERE 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date)=".$fltrTgl." and
                    `puprarsip`.`tb_absen_attlog`.`id_thl`=".$fltrId." 

                group by 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),
                    `puprarsip`.`tb_absen_attlog`.`id_thl`

            ) AS `q_check_out`
        
        ";
    }

    function q_att_data($fltrTgl,$fltrId){
        return "
        
        (
            select 
                `q_check_in`.`the_date` AS `the_date`,
                `q_check_in`.`id_thl` AS `id_thl`,
                `q_check_in`.`pagi` AS `masuk`,
                `q_check_out`.`sore` AS `keluar` 
            
            from (
                ".q_check_in($fltrTgl,$fltrId)."

            left join 
                ".q_check_out($fltrTgl,$fltrId)."

            on
                `q_check_in`.`the_date` = `q_check_out`.`the_date` and 
                `q_check_in`.`id_thl` = `q_check_out`.`id_thl` and 
                `q_check_in`.`pagi` <> `q_check_out`.`sore`

            ) where 
                `q_check_in`.`the_date`=".$fltrTgl." and
                `q_check_in`.`id_thl`=".$fltrId."

        ) AS `q_att_data`
        
        ";
    }

    function q_all_date($fltrTgl,$fltrId){
        return "
        
            (
                select 
                    `puprarsip`.`tb_absen_jam_kerja`.`tgl_kerja` AS `tgl_kerja`,
                    `puprarsip`.`tb_absen_jam_kerja`.`is_libur` AS `is_libur`,
                    `puprarsip`.`tb_absen_jam_kerja`.`jam_masuk` AS `jam_masuk`,
                    `puprarsip`.`tb_absen_jam_kerja`.`jam_keluar` AS `jam_keluar`,
                    `puprarsip`.`tb_absen_jam_kerja`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `puprarsip`.`tb_absen_jam_kerja`.`final` AS `final`,
                    `puprarsip`.`tb_absen_thl`.`id_thl` AS `id_thl`,
                    `puprarsip`.`tb_absen_thl`.`nm_thl` AS `nm_thl`,
                    `puprarsip`.`tb_absen_thl`.`no_sk` AS `no_sk`,
                    `puprarsip`.`tb_absen_thl`.`honor_bulanan` AS `honor_bulanan`,
                    `puprarsip`.`tb_absen_thl`.`status_kwn` AS `status_kwn`,
                    `puprarsip`.`tb_absen_thl`.`npwp_thl` AS `npwp_thl`,
                    `puprarsip`.`tb_absen_thl`.`non_aktif_thl` AS `non_aktif_thl` 

                from 
                    `puprarsip`.`tb_absen_jam_kerja` join 
                    `puprarsip`.`tb_absen_thl` 

                where 
                    `puprarsip`.`tb_absen_jam_kerja`.`tgl_kerja`=".$fltrTgl." and
                    `puprarsip`.`tb_absen_thl`.`id_thl`=".$fltrId."

            ) AS `q_all_date`
        
        ";
    }

    function q_attlog($fltrTgl,$fltrId){
        return "
            (
                select 
                    `q_all_date`.`tgl_kerja` AS `tgl_kerja`,
                    `q_all_date`.`is_libur` AS `is_libur`,
                    `q_all_date`.`jam_masuk` AS `jam_masuk`,
                    `q_all_date`.`jam_keluar` AS `jam_keluar`,
                    `q_all_date`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_all_date`.`final` AS `final`,
                    `q_all_date`.`id_thl` AS `id_thl`,
                    `q_all_date`.`nm_thl` AS `nm_thl`,
                    `q_all_date`.`no_sk` AS `no_sk`,
                    `q_all_date`.`honor_bulanan` AS `honor_bulanan`,
                    `q_all_date`.`status_kwn` AS `status_kwn`,
                    `q_all_date`.`npwp_thl` AS `npwp_thl`,
                    `q_all_date`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_att_data`.`masuk` AS `masuk`,
                    `q_att_data`.`keluar` AS `keluar`,
                    `func_absen_rule`
                        (
                            `q_all_date`.`tgl_kerja`,
                            `q_all_date`.`id_thl`,
                            `q_att_data`.`masuk`,
                            `q_att_data`.`keluar`,
                            `q_all_date`.`jam_masuk`,
                            `q_all_date`.`jam_keluar`
                        ) AS `kode_rule` 

                from 
                    ".q_all_date($fltrTgl,$fltrId)."

                left join 
                    ".q_att_data($fltrTgl,$fltrId)."

                on 
                    `q_all_date`.`tgl_kerja` = `q_att_data`.`the_date` and 
                    `q_all_date`.`id_thl` = `q_att_data`.`id_thl`

                where 
                    `q_all_date`.`tgl_kerja`=".$fltrTgl." and
                    `q_all_date`.`id_thl`=".$fltrId."

            ) AS `q_attlog`";
    }

    function q_attlog_detail($fltrTgl,$fltrId){
        return "
        
            (
                select 
                    `q_attlog`.`tgl_kerja` AS `tgl_kerja`,
                    `q_attlog`.`is_libur` AS `is_libur`,
                    `q_attlog`.`jam_masuk` AS `jam_masuk`,
                    `q_attlog`.`jam_keluar` AS `jam_keluar`,
                    `q_attlog`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_attlog`.`final` AS `final`,
                    `q_attlog`.`id_thl` AS `id_thl`,
                    `q_attlog`.`nm_thl` AS `nm_thl`,
                    `q_attlog`.`no_sk` AS `no_sk`,
                    `q_attlog`.`honor_bulanan` AS `honor_bulanan`,
                    `q_attlog`.`status_kwn` AS `status_kwn`,
                    `q_attlog`.`npwp_thl` AS `npwp_thl`,
                    `q_attlog`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_attlog`.`masuk` AS `masuk`,
                    `q_attlog`.`keluar` AS `keluar`,
                    `q_attlog`.`kode_rule` AS `kode_rule`,
                    `puprarsip`.`tb_absen_detstatus_rule`.`id_det_status` AS `id_det_status`,
                    `puprarsip`.`tb_absen_detstatus_rule`.`ket_detstatus_rule` AS `ket_detstatus_rule`,
                    `puprarsip`.`tb_absen_detail_status`.`kode_status` AS `kode_status`,
                    `puprarsip`.`tb_absen_detail_status`.`ket_det_status` AS `ket_det_status`,
                    `puprarsip`.`tb_absen_detail_status`.`max_per_periode` AS `max_per_periode`,
                    `puprarsip`.`tb_absen_detail_status`.`min_per_periode` AS `min_per_periode`,
                    `puprarsip`.`tb_absen_detail_status`.`potongan_bef_min` AS `potongan_bef_min`,
                    `puprarsip`.`tb_absen_detail_status`.`potongan_betw_min_max` AS `potongan_betw_min_max`,
                    `puprarsip`.`tb_absen_detail_status`.`potongan_aft_max` AS `potongan_aft_max`,
                    `puprarsip`.`tb_absen_detail_status`.`kinerja` AS `kinerja`,
                    `puprarsip`.`tb_absen_detail_status`.`automatic_status` AS `automatic_status`,
                    `puprarsip`.`tb_absen_detail_status`.`override_value` AS `override_value`,
                    `puprarsip`.`tb_absen_detail_status`.`red_add` AS `red_add`,
                    `puprarsip`.`tb_absen_status`.`ket_status` AS `ket_status` 

                from 
                    (
                        (
                            (
                                ".q_attlog($fltrTgl,$fltrId)."

                                join 
                                    `puprarsip`.`tb_absen_detstatus_rule` 

                                on
                                    `puprarsip`.`tb_absen_detstatus_rule`.`kode_rule` = `q_attlog`.`kode_rule`

                            )

                            join 
                                `puprarsip`.`tb_absen_detail_status` 

                            on 
                                `puprarsip`.`tb_absen_detail_status`.`id_det_status` = `puprarsip`.`tb_absen_detstatus_rule`.`id_det_status`

                        )

                        join 

                            `puprarsip`.`tb_absen_status` 

                        on 
                            `puprarsip`.`tb_absen_status`.`kode_status` = `puprarsip`.`tb_absen_detail_status`.`kode_status`

                    )

                where 
                    `q_attlog`.`tgl_kerja`=".$fltrTgl." and
                    `q_attlog`.`id_thl`=".$fltrId."

            ) AS `q_attlog_detail`
        
        ";
    }

    function q_attlog_override_value($fltrTgl,$fltrId){
        return "
        
            (
                select 
                    `q_attlog_detail`.`tgl_kerja` AS `tgl_kerja`,
                    `q_attlog_detail`.`is_libur` AS `is_libur`,
                    `q_attlog_detail`.`jam_masuk` AS `jam_masuk`,
                    `q_attlog_detail`.`jam_keluar` AS `jam_keluar`,
                    `q_attlog_detail`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_attlog_detail`.`final` AS `final`,
                    `q_attlog_detail`.`id_thl` AS `id_thl`,
                    `q_attlog_detail`.`nm_thl` AS `nm_thl`,
                    `q_attlog_detail`.`no_sk` AS `no_sk`,
                    `q_attlog_detail`.`honor_bulanan` AS `honor_bulanan`,
                    `q_attlog_detail`.`status_kwn` AS `status_kwn`,
                    `q_attlog_detail`.`npwp_thl` AS `npwp_thl`,
                    `tb_absen_sk_thl`.`nm_sk` AS `nm_sk`,
                    `tb_absen_sk_thl`.`hal_sk` AS `hal_sk`,
                    `tb_absen_sk_thl`.`tgl_sk` AS `tgl_sk`,
                    `tb_absen_sk_thl`.`kd_bidang` AS `kd_bidang`,
                    `tb_absen_sk_thl`.`kd_prog` AS `kd_prog`,
                    `tb_absen_sk_thl`.`kd_keg` AS `kd_keg`,
                    `tb_absen_bidang`.`nm_bidang` AS `nm_bidang`,
                    `tb_absen_bidang`.`id_kabid` AS `id_kabid`,
                    `tb_absen_bidang`.`nm_jab_kabid` AS `nm_jab_kabid`,
                    `tb_absen_program`.`ket_program` AS `ket_program`,
                    `tb_absen_kegiatan`.`ket_keg` AS `ket_keg`,
                    `tb_absen_kegiatan`.`id_pptk` AS `id_pptk`,
                    `tb_absen_kegiatan`.`nm_jab_pptk` AS `nm_jab_pptk`,
                    `q_attlog_detail`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_attlog_detail`.`masuk` AS `masuk`,
                    `q_attlog_detail`.`keluar` AS `keluar`,
                    `q_attlog_detail`.`kode_rule` AS `kode_rule`,
                    `q_attlog_detail`.`id_det_status` AS `id_det_status`,
                    `q_attlog_detail`.`ket_detstatus_rule` AS `ket_detstatus_rule`,
                    `q_attlog_detail`.`kode_status` AS `kode_status`,
                    `q_attlog_detail`.`ket_det_status` AS `ket_det_status`,
                    `q_attlog_detail`.`max_per_periode` AS `max_per_periode`,
                    `q_attlog_detail`.`min_per_periode` AS `min_per_periode`,
                    `q_attlog_detail`.`potongan_bef_min` AS `potongan_bef_min`,
                    `q_attlog_detail`.`potongan_betw_min_max` AS `potongan_betw_min_max`,
                    `q_attlog_detail`.`potongan_aft_max` AS `potongan_aft_max`,
                    `q_attlog_detail`.`kinerja` AS `kinerja`,
                    `q_attlog_detail`.`automatic_status` AS `automatic_status`,
                    `q_attlog_detail`.`override_value` AS `override_value`,
                    `q_attlog_detail`.`red_add` AS `red_add`,
                    `q_attlog_detail`.`ket_status` AS `ket_status`,
                    `func_absen_potong`
                        (
                            `q_attlog_detail`.`tgl_kerja`,
                            `q_attlog_detail`.`id_thl`,
                            `q_attlog_detail`.`id_det_status`
                        ) AS `potong` 

                from 
                    ".q_attlog_detail($fltrTgl,$fltrId)."
                , 
                    `puprarsip`.`tb_absen_sk_thl`
                ,
                    `puprarsip`.`tb_absen_kegiatan`
                ,
                    `puprarsip`.`tb_absen_program`
                ,
                    `puprarsip`.`tb_absen_bidang` 

                WHERE 
                    `puprarsip`.`tb_absen_sk_thl`.`kd_bidang`=`puprarsip`.`tb_absen_kegiatan`.`kd_bidang` and 
                    `puprarsip`.`tb_absen_sk_thl`.`kd_prog`=`puprarsip`.`tb_absen_kegiatan`.`kd_prog` and 
                    `puprarsip`.`tb_absen_sk_thl`.`kd_keg`=`puprarsip`.`tb_absen_kegiatan`.`kd_keg` and 
                    `puprarsip`.`tb_absen_kegiatan`.`kd_bidang`=`puprarsip`.`tb_absen_program`.`kd_bidang` and 
                    `puprarsip`.`tb_absen_kegiatan`.`kd_prog`=`puprarsip`.`tb_absen_program`.`kd_prog` and 
                    `puprarsip`.`tb_absen_program`.`kd_bidang`=`puprarsip`.`tb_absen_bidang`.`kd_bidang` and  
                    `puprarsip`.`tb_absen_sk_thl`.`no_sk`=`q_attlog_detail`.`no_sk` and 
                    `q_attlog_detail`.`tgl_kerja`=".$fltrTgl." and
                    `q_attlog_detail`.`id_thl`=".$fltrId."

            ) AS `q_attlog_override_value`
        
        ";
    }

    
?>