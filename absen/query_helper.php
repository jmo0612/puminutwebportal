<?php
    function q_check_in($fltr1){
        return "
        
            (
                select 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,
                    `puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,
                    min(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `pagi` 
                    
                from 
                `puprarsip`.`tb_absen_attlog` 

                WHERE 
                    ".$fltr1." 

                group by 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),
                    `puprarsip`.`tb_absen_attlog`.`id_thl`

            ) AS `q_check_in`
        
        ";
    }

    function q_check_out($fltr1){
        return "
        
            (
                select 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date) AS `the_date`,
                    `puprarsip`.`tb_absen_attlog`.`id_thl` AS `id_thl`,
                    max(`puprarsip`.`tb_absen_attlog`.`time_second`) AS `sore` 
                    
                from 
                    `puprarsip`.`tb_absen_attlog` 

                WHERE 
                    ".$fltr1." 

                group by 
                    cast(`puprarsip`.`tb_absen_attlog`.`time_second` as date),
                    `puprarsip`.`tb_absen_attlog`.`id_thl`

            ) AS `q_check_out`
        
        ";
    }

    function q_att_data($fltr1,$fltr2){
        return "
        
        (
            select 
                `q_check_in`.`the_date` AS `the_date`,
                `q_check_in`.`id_thl` AS `id_thl`,
                `q_check_in`.`pagi` AS `masuk`,
                `q_check_out`.`sore` AS `keluar` 
            
            from (
                ".q_check_in($fltr1)."

            left join 
                ".q_check_out($fltr1)."

            on
                `q_check_in`.`the_date` = `q_check_out`.`the_date` and 
                `q_check_in`.`id_thl` = `q_check_out`.`id_thl` and 
                `q_check_in`.`pagi` <> `q_check_out`.`sore`

            ) where ".$fltr2."

        ) AS `q_att_data`
        
        ";
    }

    function q_all_date($fltr3){
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

                where ".$fltr3."
            ) AS `q_all_date`
        
        ";
    }

    function q_attlog($fltr1,$fltr2,$fltr3){
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
                    ".q_all_date($fltr3)."

                left join 
                    ".q_att_data($fltr1,$fltr2)."

                on 
                    `q_all_date`.`tgl_kerja` = `q_att_data`.`the_date` and 
                    `q_all_date`.`id_thl` = `q_att_data`.`id_thl`

                where 
                    ".$fltr3."
            ) AS `q_attlog`";
    }

    function q_attlog_detail($fltr1,$fltr2,$fltr3){
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
                                ".q_attlog($fltr1,$fltr2,$fltr3)."

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
                    ".$fltr3."

            ) AS `q_attlog_detail`
        
        ";
    }

    function q_attlog_override_value($fltr1,$fltr2,$fltr3){
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
                    ".q_attlog_detail($fltr1,$fltr2,$fltr3)."
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
                    ".$fltr3."

            ) AS `q_attlog_override_value`
        
        ";
    }

    function q_attlog_main($fltr1,$fltr2,$fltr3){
        return "
        
            (
                select 
                    `q_attlog_override_value`.`tgl_kerja` AS `tgl_kerja`,
                    `q_attlog_override_value`.`is_libur` AS `is_libur`,
                    `q_attlog_override_value`.`jam_masuk` AS `jam_masuk`,
                    `q_attlog_override_value`.`jam_keluar` AS `jam_keluar`,
                    `q_attlog_override_value`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_attlog_override_value`.`final` AS `final`,
                    `q_attlog_override_value`.`id_thl` AS `id_thl`,
                    `q_attlog_override_value`.`nm_thl` AS `nm_thl`,
                    `q_attlog_override_value`.`no_sk` AS `no_sk`,
                    `q_attlog_override_value`.`honor_bulanan` AS `honor_bulanan`,
                    `q_attlog_override_value`.`status_kwn` AS `status_kwn`,
                    `q_attlog_override_value`.`npwp_thl` AS `npwp_thl`,
                    `q_attlog_override_value`.`nm_sk` AS `nm_sk`,
                    `q_attlog_override_value`.`hal_sk` AS `hal_sk`,
                    `q_attlog_override_value`.`tgl_sk` AS `tgl_sk`,
                    `q_attlog_override_value`.`kd_bidang` AS `kd_bidang`,
                    `q_attlog_override_value`.`kd_prog` AS `kd_prog`,
                    `q_attlog_override_value`.`kd_keg` AS `kd_keg`,
                    `q_attlog_override_value`.`nm_bidang` AS `nm_bidang`,
                    `q_attlog_override_value`.`id_kabid` AS `id_kabid`,
                    `q_attlog_override_value`.`nm_jab_kabid` AS `nm_jab_kabid`,
                    `q_attlog_override_value`.`ket_program` AS `ket_program`,
                    `q_attlog_override_value`.`ket_keg` AS `ket_keg`,
                    `q_attlog_override_value`.`id_pptk` AS `id_pptk`,
                    `q_attlog_override_value`.`nm_jab_pptk` AS `nm_jab_pptk`,
                    `q_attlog_override_value`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_attlog_override_value`.`masuk` AS `masuk`,
                    `q_attlog_override_value`.`keluar` AS `keluar`,
                    `q_attlog_override_value`.`kode_rule` AS `kode_rule`,
                    `q_attlog_override_value`.`id_det_status` AS `id_det_status`,
                    `q_attlog_override_value`.`ket_detstatus_rule` AS `ket_detstatus_rule`,
                    `q_attlog_override_value`.`kode_status` AS `kode_status`,
                    `q_attlog_override_value`.`ket_det_status` AS `ket_det_status`,
                    `q_attlog_override_value`.`max_per_periode` AS `max_per_periode`,
                    `q_attlog_override_value`.`min_per_periode` AS `min_per_periode`,
                    `q_attlog_override_value`.`potongan_bef_min` AS `potongan_bef_min`,
                    `q_attlog_override_value`.`potongan_betw_min_max` AS `potongan_betw_min_max`,
                    `q_attlog_override_value`.`potongan_aft_max` AS `potongan_aft_max`,
                    `q_attlog_override_value`.`kinerja` AS `kinerja`,
                    `q_attlog_override_value`.`automatic_status` AS `automatic_status`,
                    `q_attlog_override_value`.`override_value` AS `override_value`,
                    `q_attlog_override_value`.`red_add` AS `red_add`,
                    `q_attlog_override_value`.`ket_status` AS `ket_status`,
                    `q_attlog_override_value`.`potong` AS `potong`,
                    `func_absen_total`
                        (
                            `q_attlog_override_value`.`tgl_kerja`,
                            `q_attlog_override_value`.`id_thl`,
                            `q_attlog_override_value`.`id_det_status`
                        ) AS `total_status`,
                    `func_absen_tot_hari`
                        (
                            `q_attlog_override_value`.`tgl_kerja`
                        ) AS `total_hari_kerja`,
                    `func_absen_status_ke`
                        (
                            `q_attlog_override_value`.`tgl_kerja`,
                            `q_attlog_override_value`.`id_thl`,
                            `q_attlog_override_value`.`id_det_status`
                        ) AS `det_status_ke`,
                    `func_absen_potongan`
                        (
                            `q_attlog_override_value`.`max_per_periode`,
                            `q_attlog_override_value`.`min_per_periode`,
                            `q_attlog_override_value`.`potongan_bef_min`,
                            `q_attlog_override_value`.`potongan_betw_min_max`,
                            `q_attlog_override_value`.`potongan_aft_max`,
                            `func_absen_status_ke`
                                (
                                    `q_attlog_override_value`.`tgl_kerja`,
                                    `q_attlog_override_value`.`id_thl`,
                                    `q_attlog_override_value`.`id_det_status`
                                ),
                            `q_attlog_override_value`.`potong`
                        ) AS `potongan`,
                    `func_absen_jam`
                        (
                            `q_attlog_override_value`.`masuk`,
                            `q_attlog_override_value`.`keluar`
                        ) AS `jam`,
                    `func_absen_jam`
                        (
                            `q_attlog_override_value`.`jam_masuk`,
                            `q_attlog_override_value`.`jam_keluar`
                        ) AS `jam_target`

                from 
                    ".q_attlog_override_value($fltr1,$fltr2,$fltr3)."

                where 
                    ".$fltr3."
            ) AS `q_attlog_main`
        
        ";
    }

    function q_attlog_unfinal($fltr1,$fltr2,$fltr3){
        return "
        
            (
                select 
                    `q_attlog_main`.`tgl_kerja` AS `tgl_kerja`,
                    `q_attlog_main`.`id_thl` AS `id_thl`,
                    `q_attlog_main`.`id_det_status` AS `id_det_status`,
                    `q_attlog_main`.`is_libur` AS `is_libur`,
                    `q_attlog_main`.`jam_masuk` AS `jam_masuk`,
                    `q_attlog_main`.`jam_keluar` AS `jam_keluar`,
                    `q_attlog_main`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_attlog_main`.`final` AS `final`,
                    `q_attlog_main`.`nm_thl` AS `nm_thl`,
                    `q_attlog_main`.`no_sk` AS `no_sk`,
                    `q_attlog_main`.`honor_bulanan` AS `honor_bulanan`,
                    `q_attlog_main`.`status_kwn` AS `status_kwn`,
                    `q_attlog_main`.`npwp_thl` AS `npwp_thl`,
                    `q_attlog_main`.`nm_sk` AS `nm_sk`,
                    `q_attlog_main`.`hal_sk` AS `hal_sk`,
                    `q_attlog_main`.`tgl_sk` AS `tgl_sk`,
                    `q_attlog_main`.`kd_bidang` AS `kd_bidang`,
                    `q_attlog_main`.`kd_prog` AS `kd_prog`,
                    `q_attlog_main`.`kd_keg` AS `kd_keg`,
                    `q_attlog_main`.`nm_bidang` AS `nm_bidang`,
                    `q_attlog_main`.`id_kabid` AS `id_kabid`,
                    `q_attlog_main`.`nm_jab_kabid` AS `nm_jab_kabid`,
                    `q_attlog_main`.`ket_program` AS `ket_program`,
                    `q_attlog_main`.`ket_keg` AS `ket_keg`,
                    `q_attlog_main`.`id_pptk` AS `id_pptk`,
                    `q_attlog_main`.`nm_jab_pptk` AS `nm_jab_pptk`,
                    `q_attlog_main`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_attlog_main`.`masuk` AS `masuk`,
                    `q_attlog_main`.`keluar` AS `keluar`,
                    `q_attlog_main`.`kode_rule` AS `kode_rule`,
                    `q_attlog_main`.`ket_detstatus_rule` AS `ket_detstatus_rule`,
                    `q_attlog_main`.`kode_status` AS `kode_status`,
                    `q_attlog_main`.`ket_det_status` AS `ket_det_status`,
                    `q_attlog_main`.`max_per_periode` AS `max_per_periode`,
                    `q_attlog_main`.`min_per_periode` AS `min_per_periode`,
                    `q_attlog_main`.`potongan_bef_min` AS `potongan_bef_min`,
                    `q_attlog_main`.`potongan_betw_min_max` AS `potongan_betw_min_max`,
                    `q_attlog_main`.`potongan_aft_max` AS `potongan_aft_max`,
                    `q_attlog_main`.`kinerja` AS `kinerja`,
                    `q_attlog_main`.`automatic_status` AS `automatic_status`,
                    `q_attlog_main`.`override_value` AS `override_value`,
                    `q_attlog_main`.`red_add` AS `red_add`,
                    `q_attlog_main`.`ket_status` AS `ket_status`,
                    `q_attlog_main`.`potong` AS `potong`,
                    `q_attlog_main`.`total_status` AS `total_status`,
                    `q_attlog_main`.`total_hari_kerja` AS `total_hari_kerja`,
                    `q_attlog_main`.`det_status_ke` AS `det_status_ke`,
                    `q_attlog_main`.`potongan` AS `potongan`,
                    `q_attlog_main`.`jam` AS `jam`,
                    `q_attlog_main`.`jam_target` AS `jam_target`

                from 
                    ".q_attlog_main($fltr1,$fltr2,$fltr3)."

                where 
                    `q_attlog_main`.`final` = '0' and 
                    ".$fltr3."

            ) AS `q_attlog_unfinal` 
        
        ";
    }

    function q_attlog_unfinal_libur($fltr1,$fltr2,$fltr3){
        return "
        
            (
                select 
                    `q_attlog_main`.`tgl_kerja` AS `tgl_kerja`,
                    `q_attlog_main`.`id_thl` AS `id_thl`,
                    `q_attlog_main`.`id_det_status` AS `id_det_status`,
                    `q_attlog_main`.`is_libur` AS `is_libur`,
                    `q_attlog_main`.`jam_masuk` AS `jam_masuk`,
                    `q_attlog_main`.`jam_keluar` AS `jam_keluar`,
                    `q_attlog_main`.`ket_tgl_kerja` AS `ket_tgl_kerja`,
                    `q_attlog_main`.`final` AS `final`,
                    `q_attlog_main`.`nm_thl` AS `nm_thl`,
                    `q_attlog_main`.`no_sk` AS `no_sk`,
                    `q_attlog_main`.`honor_bulanan` AS `honor_bulanan`,
                    `q_attlog_main`.`status_kwn` AS `status_kwn`,
                    `q_attlog_main`.`npwp_thl` AS `npwp_thl`,
                    `q_attlog_main`.`nm_sk` AS `nm_sk`,
                    `q_attlog_main`.`hal_sk` AS `hal_sk`,
                    `q_attlog_main`.`tgl_sk` AS `tgl_sk`,
                    `q_attlog_main`.`kd_bidang` AS `kd_bidang`,
                    `q_attlog_main`.`kd_prog` AS `kd_prog`,
                    `q_attlog_main`.`kd_keg` AS `kd_keg`,
                    `q_attlog_main`.`nm_bidang` AS `nm_bidang`,
                    `q_attlog_main`.`id_kabid` AS `id_kabid`,
                    `q_attlog_main`.`nm_jab_kabid` AS `nm_jab_kabid`,
                    `q_attlog_main`.`ket_program` AS `ket_program`,
                    `q_attlog_main`.`ket_keg` AS `ket_keg`,
                    `q_attlog_main`.`id_pptk` AS `id_pptk`,
                    `q_attlog_main`.`nm_jab_pptk` AS `nm_jab_pptk`,
                    `q_attlog_main`.`non_aktif_thl` AS `non_aktif_thl`,
                    `q_attlog_main`.`masuk` AS `masuk`,
                    `q_attlog_main`.`keluar` AS `keluar`,
                    `q_attlog_main`.`kode_rule` AS `kode_rule`,
                    `q_attlog_main`.`ket_detstatus_rule` AS `ket_detstatus_rule`,
                    `q_attlog_main`.`kode_status` AS `kode_status`,
                    `q_attlog_main`.`ket_det_status` AS `ket_det_status`,
                    `q_attlog_main`.`max_per_periode` AS `max_per_periode`,
                    `q_attlog_main`.`min_per_periode` AS `min_per_periode`,
                    `q_attlog_main`.`potongan_bef_min` AS `potongan_bef_min`,
                    `q_attlog_main`.`potongan_betw_min_max` AS `potongan_betw_min_max`,
                    `q_attlog_main`.`potongan_aft_max` AS `potongan_aft_max`,
                    `q_attlog_main`.`kinerja` AS `kinerja`,
                    `q_attlog_main`.`automatic_status` AS `automatic_status`,
                    `q_attlog_main`.`override_value` AS `override_value`,
                    `q_attlog_main`.`red_add` AS `red_add`,
                    `q_attlog_main`.`ket_status` AS `ket_status`,
                    `q_attlog_main`.`potong` AS `potong`,
                    `q_attlog_main`.`total_status` AS `total_status`,
                    `q_attlog_main`.`total_hari_kerja` AS `total_hari_kerja`,
                    `q_attlog_main`.`det_status_ke` AS `det_status_ke`,
                    `q_attlog_main`.`potongan` AS `potongan`,
                    `q_attlog_main`.`jam` AS `jam`,
                    `q_attlog_main`.`jam_target` AS `jam_target`

                from 
                    ".q_attlog_main($fltr1,$fltr2,$fltr3)."

                where 
                    `q_attlog_main`.`final` = '0' and 
                    ".$fltr3." and is_libur='0'

            ) AS `q_attlog_unfinal` 
        
        ";
    }

    function qtb_det($fltr1,$fltr2,$fltr3){
        return "
        
        (
            select 
                `q_attlog_unfinal`.`tgl_kerja` AS `tgl_kerja`,
                `q_attlog_unfinal`.`id_thl` AS `id_thl`,
                `q_attlog_unfinal`.`nm_thl` AS `nm_thl`,
                `q_attlog_unfinal`.`jam` AS `jam`,
                `q_attlog_unfinal`.`kode_rule` AS `kode_rule`,
                `q_attlog_unfinal`.`is_libur` AS `is_libur`,
                `q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,
                sum(`q_attlog_unfinal`.`red_add`) AS `red_add`

            from 
                ".q_attlog_unfinal($fltr1,$fltr2,$fltr3)."

            WHERE 
                ".$fltr3." and 
                `is_libur`='0' and 
                `non_aktif_thl`='0'

            group by 
                `q_attlog_unfinal`.`tgl_kerja`,
                `q_attlog_unfinal`.`id_thl`,
                `q_attlog_unfinal`.`kode_rule`

            ORDER by 
                tgl_kerja asc, 
                nm_thl ASC

        ) AS qtb_det
        
        ";
    }

    function qtb_det_pot($fltr1,$fltr2,$fltr3){ 
        return "
        
        (
            select 
                Year(`q_attlog_unfinal`.`tgl_kerja`) AS `tahun`,
                Month(`q_attlog_unfinal`.`tgl_kerja`) AS `bulan`,
                `q_attlog_unfinal`.`id_thl` AS `id_thl`,
                `q_attlog_unfinal`.`kode_status` AS `kode_status`,
                `q_attlog_unfinal`.`potongan` AS `potongan`,
                `q_attlog_unfinal`.`nm_thl` AS `nm_thl`,
                `q_attlog_unfinal`.`no_sk` AS `no_sk`,
                `q_attlog_unfinal`.`nm_sk` AS `nm_sk`,
                `q_attlog_unfinal`.`hal_sk` AS `hal_sk`,
                `q_attlog_unfinal`.`tgl_sk` AS `tgl_sk`,
                `q_attlog_unfinal`.`kd_bidang` AS `kd_bidang`,
                `q_attlog_unfinal`.`kd_prog` AS `kd_prog`,
                `q_attlog_unfinal`.`kd_keg` AS `kd_keg`,
                `q_attlog_unfinal`.`nm_bidang` AS `nm_bidang`,
                `q_attlog_unfinal`.`id_kabid` AS `id_kabid`,
                `q_attlog_unfinal`.`nm_jab_kabid` AS `nm_jab_kabid`,
                `q_attlog_unfinal`.`ket_program` AS `ket_program`,
                `q_attlog_unfinal`.`ket_keg` AS `ket_keg`,
                `q_attlog_unfinal`.`id_pptk` AS `id_pptk`,
                `q_attlog_unfinal`.`nm_jab_pptk` AS `nm_jab_pptk`,
                `q_attlog_unfinal`.`non_aktif_thl` AS `non_aktif_thl`,
                `q_attlog_unfinal`.`ket_status` AS `ket_status`,
                `q_attlog_unfinal`.`total_hari_kerja` AS `total_hari_kerja`,
                count(potongan) AS kali_pot,
                (count(potongan) * potongan) AS tot_potongan,
                concat('(',count(potongan),'x',potongan,'% = ',(count(potongan) * potongan),'%)') AS ket_potongan


            from 
                ".q_attlog_unfinal_libur($fltr1,$fltr2,$fltr3)."

            WHERE 
                ".$fltr3." and 
                `non_aktif_thl`='0'

            group by 
                Year(`q_attlog_unfinal`.`tgl_kerja`),
                Month(`q_attlog_unfinal`.`tgl_kerja`),
                `q_attlog_unfinal`.`id_thl`,
                `q_attlog_unfinal`.`kode_status`,
                `q_attlog_unfinal`.`potongan`

            ORDER by  
                nm_thl ASC,potongan ASC

        ) AS qtb_det_pot
        
        ";
    }

    function qtb_det_pot_grouped($fltr1,$fltr2,$fltr3,$fltr4){ 
        return "
        
        (
            select 
                `qtb_det_pot`.`tahun` AS `tahun`,
                `qtb_det_pot`.`bulan` AS `bulan`,
                `qtb_det_pot`.`id_thl` AS `id_thl`,
                `qtb_det_pot`.`kode_status` AS `kode_status`,
                `qtb_det_pot`.`potongan` AS `potongan`,
                `qtb_det_pot`.`nm_thl` AS `nm_thl`,
                `qtb_det_pot`.`no_sk` AS `no_sk`,
                `qtb_det_pot`.`nm_sk` AS `nm_sk`,
                `qtb_det_pot`.`hal_sk` AS `hal_sk`,
                `qtb_det_pot`.`tgl_sk` AS `tgl_sk`,
                `qtb_det_pot`.`kd_bidang` AS `kd_bidang`,
                `qtb_det_pot`.`kd_prog` AS `kd_prog`,
                `qtb_det_pot`.`kd_keg` AS `kd_keg`,
                `qtb_det_pot`.`nm_bidang` AS `nm_bidang`,
                `qtb_det_pot`.`id_kabid` AS `id_kabid`,
                `qtb_det_pot`.`nm_jab_kabid` AS `nm_jab_kabid`,
                `qtb_det_pot`.`ket_program` AS `ket_program`,
                `qtb_det_pot`.`ket_keg` AS `ket_keg`,
                `qtb_det_pot`.`id_pptk` AS `id_pptk`,
                `qtb_det_pot`.`nm_jab_pptk` AS `nm_jab_pptk`,
                `qtb_det_pot`.`non_aktif_thl` AS `non_aktif_thl`,
                `qtb_det_pot`.`ket_status` AS `ket_status`,
                `qtb_det_pot`.`total_hari_kerja` AS `total_hari_kerja`,
                SUM(`qtb_det_pot`.`kali_pot`) AS `kali_pot`,
                SUM(`qtb_det_pot`.`tot_potongan`) AS `tot_potongan`,
                CONCAT(GROUP_CONCAT(`qtb_det_pot`.`ket_potongan` SEPARATOR ' + <br>'),'') AS `ket_potongan`


            from 
                ".qtb_det_pot($fltr1,$fltr2,$fltr3)."

            WHERE 
                ".$fltr4." and 
                `non_aktif_thl`='0'

            group by 
                `qtb_det_pot`.`tahun`,
                `qtb_det_pot`.`bulan`,
                `qtb_det_pot`.`id_thl`,
                `qtb_det_pot`.`kode_status`

            ORDER by  
                nm_thl ASC

        ) AS qtb_det_pot_grouped
        
        ";
    }
?>