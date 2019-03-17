
<div>
    <div id="tt" style="visibility:hidden; padding:8px; width:100px; height:60px; position:absolute; background-color:rgba(100,100,100,0.9); color:whitesmoke; border:solid; border-width:thin; border-radius:10px">
        hallo
    </div>
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
            $calThn=2019;
            $calBln=5;
            $wDate=DateTime::createFromFormat("Y-m-d", $calThn."-".str_pad($calBln,2,"0",STR_PAD_LEFT)."-01");
            $brs=1;
            do{
                if(dateMonthInt($wDate->format("Y-m-d"))==$calBln){
            ?>
            <tr>
            <?php
                    for($i=1;$i<=7;$i++){
                        if(dateDayWeekInt($wDate->format("Y-m-d"))==$i && dateMonthInt($wDate->format("Y-m-d"))==$calBln){
            ?>
                <td style="padding:5px; text-align:center">
                    <div name="<?php echo (($brs<3)?"bwh_":"ats_").(($i<5)?"kn":"kr"); ?>" class="jmocaldate" style="border:solid; border-width:thin; border-radius:10px; margin:1px; vertical-align:middle; width:52px;height:52px; padding:1px">
                        <div style="height:26px; width:48px; font-weight:bold; border-top-left-radius:10px; border-top-right-radius:10px; margin:1px; font-size:20px; background-color:whitesmoke">
            <?php
                            echo str_pad(dateDayInt($wDate->format("Y-m-d")),2,"0",STR_PAD_LEFT);
                            date_add($wDate,date_interval_create_from_date_string("1 day"));
            ?>
                        </div>
                        <div style="margin:1px; width:48px; height:20px; font-weight:bold; border:solid; border-width:thin; border-bottom-left-radius:10px; border-bottom-right-radius:10px; font-size:12px; background-color:green; color:white">
                            T, TAT
                        </div>
                    </div>
                </td>
            <?php
                            
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