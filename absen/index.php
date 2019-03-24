<?php
    include '../konek.php';
    include '../master.php';
    include '../module.php';
    include 'global.php';
    
    session_start();
    if(isset($_GET["out"])){
        session_unset();
        session_destroy();
        redirect("login.php");
    }else{
        if(!isset($_SESSION["usrId"])){
            if(!isset($_SESSION["usrToken"])){
                redirect("login.php?pg=abs");
            }else{
                initIndex();
            }
        }else{
            initIndex();
        }
    }
    
    
    //initIndex();
    
    //echo $admAbsen;
    
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">

    <title>e-Absen PUPR</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bootstrap-date-picker-dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../bootstrap-3.3.7-dist/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../bootstrap-3.3.7-dist/css/dashboard.css" rel="stylesheet"> 

    
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../bootstrap-3.3.7-dist/js/ie-emulation-modes-warning.js"></script>
    
    
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    

      <?php
      
      ?>
      
      <nav class="navbar navbar-inverse navbar-fixed-top container-fluid" style="background-color: rgb(34, 34, 34)">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

        </div>
        <a class="navbar-brand" href="?">e-Absen</a>
        <div id="navbar" class="navbar-collapse collapse">
            <!--  navbar-right -->
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Rekapan <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="?p=dhAll">Daftar Hadir Bulanan</a></li>
                        <li><a href="?p=rDh">Rekapan Daftar Hadir Bulanan</a></li>
                        <li><a href="?p=rGj">Rekapan Gaji Bulanan</a></li>
                    </ul>
                </li>

                <li class="dropdown pull-left">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo "../".$urlPropic; ?>" class="img-rounded"  width="26px" height="26px">
                        <?php echo $nmUser; ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="dropdown-submenu pull-left <?php ($admAbsen==1)?"":"disabled"; ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pengaturan</a>
                            <ul class="dropdown-menu" style="position:absolute;right:100%">
                                <li><a href="index.php?p=adAl">Kalender Kerja</a></li>
                                <li><a href="index.php?p=dfThl">Daftar THL</a></li>
                                <li><a href="index.php?p=adPk">Program/Kegiatan</a></li>
                                <li><a href="index.php?p=adGj">Gaji</a></li>
                                <li><a href="index.php?p=adSt">Status Kehadiran</a></li>
                                <li><a href="index.php?p=adTh">Data Pegawai</a></li>
                            </ul>
                        </li>
                        <li><a href="?out=1">Keluar</a></li>
                        <li><a href="?akun=0">Ganti Password</a></li>
                    </ul>
                
                </li>

            </ul>

        </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 main">
            <?php
            //updateUrlGet("","");
            if(getGet("p")){
                if(getGet("p")=="dhAll"){
                    include 'view_att_all.php';
                }elseif(getGet("p")=="rDh"){
                    //include 'view_att_all.php';
                }elseif(getGet("p")=="rGj"){
                    //include 'view_att_all.php';
                }elseif(getGet("p")=="adAl"){
                    if($admAbsen==1)include 'kalender.php';
                }elseif(getGet("p")=="detHk"){
                    if($admAbsen==1)include 'det_kalender.php';
                }elseif(getGet("p")=="detAtt"){
                    if($admAbsen==1)include 'det_att.php';
                }elseif(getGet("p")=="dfThl"){
                    if($admAbsen==1)include 'daftar_thl.php';
                }else{

                }
            }else{
                include 'view_att.php';
				//include 'kalender.php';
            }
            ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../bootstrap-3.3.7-dist/js/jquery.min.js"></script>
    <script src="jmoCalendar/calendar.js"></script>
    <!--<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>-->
    <script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../bootstrap-date-picker-dist/js/bootstrap-datepicker.min.js"></script>
    <script src="../bootstrap-date-picker-dist/locales/bootstrap-datepicker.id.min.js"></script>

    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="../bootstrap-3.3.7-dist/js/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../bootstrap-3.3.7-dist/js/ie10-viewport-bug-workaround.js"></script>

    <script src="../module.js"></script>
    
  </body>
</html>


<script type="text/javascript">
    $('#dtSpan').datepicker({
        format: 'yyyy-mm-dd',
        language: 'id'
    });

    $('#dtSpan').on('changeDate', function() {
        var curDt=new Date();
        var newDt=new Date($('#dtSpan').datepicker('getFormattedDate'));
        window.location.href=updateUrlGet("dDt",dateDiff("d",curDt,newDt,true));
    });

    $('#dtSpanMonth').datepicker({
        format: "yyyy-mm-dd",
        viewMode: "months", 
        minViewMode: "months"
    });

    $('#dtSpanMonth').on('changeDate', function() {
        var curDt=new Date();
        var newDt=new Date($('#dtSpanMonth').datepicker('getFormattedDate'));
        //alert(monthDiff(curDt,newDt));
        window.location.href=updateUrlGet("dMn",monthDiff(curDt,newDt));
    });
    $('#kalThn').change(function(){
		$('#frmThn').submit();
	});


    (function($){
        $(document).ready(function(){
            $('ul.dropdown-menu [data-toggle=dropdown]').on('mouseover', function(event) {
                event.preventDefault(); 
                event.stopPropagation(); 
                $(this).parent().siblings().removeClass('open');
                $(this).parent().toggleClass('open');
            });
        });
    })(jQuery);
    /* http://www.bootply.com/nZaxpxfiXz */



    $('.jmocaldate').mouseover(function(){
        $(this).click();
    });

    $('.jmocaldate').mouseout(function(){
        $('#tt').css("visibility","hidden");
        $('#ttKet1').text('');
        $('#ttKet2').text('');
        $('#ttKet3').text('');
        
    });

    $('.jmocaldate').click(function(){
        var myId=$(this).attr("id");
        var ind=myId.substr(3,myId.length+1);
        var jam=$('#jam'+ind).val();
        var jamT=$('#jamTarget'+ind).val();
        if(jam=='Tidak Hadir')jam='-';
        var tmp=jam;

        
        
        
        $('#ttKet3').text($('#ket'+ind).val());
        if($('#lbr'+ind).val()==1){
            
            tmp='Libur';
        }else{
            $('#ttKet2').text('('+jamT+')');
        }
        $('#ttKet1').text(tmp);
        

        $('#tt').css("visibility","visible");
        $('#tt').css("border-radius","15px");
        
        var n=$(this).attr("name");

        var t=$(this).offset().top-$("#tt").height()+5;
        var radT="bottom";
        if(n.substr(0,3)=="bwh"){
            t=$(this).offset().top+$(this).height()-5;
            radT="top";
        }
        

        var l=$(this).offset().left-$("#tt").width()+5;
        var radL="right";
        if(n.substr(4,6)=="kn"){
            l=$(this).offset().left+$(this).width()-5;
            radL="left";
        }

        $('#tt').css("border-"+radT+"-"+radL+"-radius","0px");

        $('#tt').offset({
            left:l,
            top:t
        });
    });
    
</script>