<?php
    session_start();
    include 'konek.php';
    include 'master.php';
    include 'module.php';
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

        <title>e-Archive PUPR</title>

        <!-- Bootstrap core CSS -->
        <link href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link href="bootstrap-3.3.7-dist/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="bootstrap-3.3.7-dist/css/dashboard.css" rel="stylesheet">

        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="bootstrap-3.3.7-dist/js/ie-emulation-modes-warning.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body style="background-image: url('images/arsipbg.jpg');background-size: cover; background-repeat: no-repeat">
        <div class="container modal-dialog">
            <div class="panel panel-default" style="background-color:rgba(0,0,0,0.5);border: none">
                <div class="panel-body" style="color: white">
                    
                    <?php
                        if(getGet("auth")){
                            //echo 'jhdkhdhkhdkhdkkdjkdgk';
                          
                            $strSql="SELECT `tb_auth`.`id_auth` AS `id_auth`, `tb_auth`.`id_user` AS `id_user`, `tb_auth`.`for_activation` AS `for_activation`, `tb_user`.`email_user` AS `email_user`, `tb_user`.`pass_user` AS `pass_user`, `tb_user`.`jns_user` AS `jns_user`, `tb_user`.`id_user_tipe` AS `id_user_tipe`, `tb_user`.`token` AS `token`, `tb_user`.`aktif` AS `aktif`, `tb_user`.`nm_user` AS `nm_user`, `tb_user`.`url_foto` AS `url_foto` FROM `puprarsip`.`tb_auth` AS `tb_auth`, `puprarsip`.`tb_user` AS `tb_user` WHERE `tb_auth`.`id_user` = `tb_user`.`id_user`";
                            $strSql.=" and tb_auth.id_auth='". getGet("auth")."'";
                            $res=$con->query($strSql);
                            if($res->num_rows>0){
                                $row=$res->fetch_assoc();
                                if($row["for_activation"]==0){
                                    include 'ganti_pass.php';
                                }else{
                                    $sqlAktif="update tb_user set aktif=1 where id_user='".$row["id_user"]."'";
                                    if($con->query($sqlAktif)){
                                        $sqlUp="delete from tb_auth where id_auth='". getGet("auth")."'";
                                        if($con->query($sqlUp)){
                                            $_SESSION["usrId"]=$row["id_user"];
                                            //echo 'kewde';
                                            redirect("");
                                        }else{
                                            //echo 'tolor';
                                            redirect("login.php");
                                        }
                                    }else{
                                        redirect("login.php");
                                        //echo $row["id_user"];
                                    }
                                }
                            }else{
                                //echo 'laso';
                                redirect("login.php");
                            }
                        }else{
                            if(getGet("forget")){
                                include 'forget.php';
                            }else{
                                if(getGet("signup")){
                                    include 'daftar.php';
                                }else{
                                    if(getGet("signin")){
                                        include 'loginUser.php';
                                    }else{
                                        //Default
                                        include 'loginToken.php';
                                        //include 'daftar.php';
                                    }
                                }
                            }
                        }
                        
                    ?>
                </div>
            </div>
            
        </div>
        
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="bootstrap-3.3.7-dist/js/jquery.min.js"></script>
        <!--<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>-->
        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
        <script src="bootstrap-3.3.7-dist/js/holder.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="bootstrap-3.3.7-dist/js/ie10-viewport-bug-workaround.js"></script>
        
        <script src="bootstrap-3.3.7-dist/js/global.js"></script>
    </body>
</html>