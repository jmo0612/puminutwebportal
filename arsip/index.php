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
                redirect("login.php?pg=ars");
            }else{
                initIndex();
            }
        }else{
            initIndex();
        }
    }
    
    
    //initIndex();
    
    
    
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

    <title>e-Arsip PUPR</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

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
        <a class="navbar-brand" href="#">e-Arsip</a>
        <div id="navbar" class="navbar-collapse collapse">
            <form id="filterInput" class="navbar-form navbar-right" method="post" action="">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">TA</span>
                    <input type="text" class="form-control" placeholder="Tahun" name="tahun" id="tahun" value="<?php if($tahun>0)echo $tahun; ?>" <?php if($lockTahun) echo 'readonly'; ?> onchange="submitMe()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">Triwulan</span>
                    <select class="form-control" name="triwulan" id="triwulan"  <?php if($lockTahun) echo 'disabled'; ?> onchange="submitMe()">
                        <option value="0" <?php if($triwulan==0) echo 'selected'; ?>>[SEMUA]</option>
                        <option value="1" <?php if($triwulan==1) echo 'selected'; ?>>I</option>
                        <option  value="2" <?php if($triwulan==2) echo 'selected'; ?>>II</option>
                        <option value="3" <?php if($triwulan==3) echo 'selected'; ?>>III</option>
                        <option value="4" <?php if($triwulan==4) echo 'selected'; ?>>IV</option>
                    </select>
                </div>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Cari..." id="cari" name="cari" value="<?php echo getPost("cari"); ?>" onchange="submitMe()">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </span>
                </div><!-- /input-group -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 1px">
                        <img src="<?php echo "../".$urlPropic; ?>" class="img-rounded" width="26px" height="26px">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="?out=1">Keluar</a></li>
                        <li><a href="?akun=0">Ganti Password</a></li>
                    </ul>
                </div>
                
            </form>
        </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 main">
            <?php
            if(getPost("cari")!=NULL){
                if(getPost("cari")!=""){
                    include 'filter.php';
                }else{
                    include 'main.php';
                }
            }else{
                include 'main.php';
            }
            ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../bootstrap-3.3.7-dist/js/jquery.min.js"></script>
    <!--<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>-->
    <script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="../bootstrap-3.3.7-dist/js/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../bootstrap-3.3.7-dist/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>


<script type="text/javascript">
    //alert(document.getElementById("frm").action);
</script>