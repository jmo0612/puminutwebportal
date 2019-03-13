<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
?>
<h2>Daftar</h2>
<div id="info">
<?php
    function sentActivationEmail($eId,$addr,$eName){
        require 'PHPMailer-master/src/Exception.php';
        require 'PHPMailer-master/src/PHPMailer.php';
        require 'PHPMailer-master/src/SMTP.php';

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'puprino920@gmail.com';                 // SMTP username
            $mail->Password = 'dinaspupr';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;//587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('puprino920@gmail.com', 'e-Archive PUPR Minut');
            $mail->addAddress($addr, $eName);     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Aktivasi User';
            $htmlMsg='<p>Sdr/i <b>'.$eName.'</b>,</p>';
            $htmlMsg.='<p>Terima kasih telah melakukan pendaftaran akun pada aplikasi e-Archive PUPR Minut ';
            $htmlMsg.='tanggal '. dateMedium(date('Y-m-d')) .'. Untuk melanjutkan terlebih dahulu pastikan anda terhubung pada jaringan lokal Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Minahasa Utara.</p>';
            $theAuth= md5($eId. date("Y-m-d H:i:s"). rand(0, 1000000));
            $htmlMsg.='<p>Silahkan klik di <a href="'. getUrl("login.php?auth=").$theAuth.'">sini</a> untuk aktivasi akun anda. Terima kasih.</p>';
            $mail->Body    = $htmlMsg;
            $mail->AltBody = 'Link aktivasi akun anda: '. getUrl("login.php?auth=").$theAuth;

            //echo $eId;
            global $con;
            $con->query("delete from tb_auth where id_user='".$eId."'");

            $sqlMakeAuth="replace into tb_auth(id_auth,id_user,for_activation) values('".$theAuth."','".$eId."',1)";
            if($con->query($sqlMakeAuth)){
                $mail->send();
                ?>
<div class="alert alert-success" role="alert">Link untuk aktivasi telah dikirim ke email anda.</div>                    
                <?php
            }else{
                ?>
<div class="alert alert-danger" role="alert">Maaf, terjadi kesalahan pada sistem.</div>                    
                <?php
            }
        } catch (Exception $e) {
            ?>
<div class="alert alert-danger" role="alert">Maaf, saat ini server tidak bisa mengirim email.</div>                    
            <?php
            //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }


    $nmDftr= getPost("dftrNama");
    $sukses= getPost("dftrSukses")=="1";
    if($sukses){
        //sentActivationEmail(getPost("dftrNip"), getPost("dftrEmail"), getPost("dftrNama"));
    }else{
        if(getPost("dftrNip")){
            $sqlCekDftr="select * from tb_user where id_user='". getPost("dftrNip")."'";
            $resCekDftr=$con->query($sqlCekDftr);
            if($resCekDftr->num_rows==0){
                $sqlDftr="select * from tb_pegawai where id_pegawai='". getPost("dftrNip")."'";
                $resDftr=$con->query($sqlDftr);
                if($resDftr->num_rows>0){
                    $rowDftr=$resDftr->fetch_assoc();
                    $nmDftr=$rowDftr["nm_pegawai"];
                    if(getPost("dftrEmail")){
                        $sqlCekEmail="select * from tb_user where email_user='". getPost("dftrEmail")."'";
                        $resCekEmail=$con->query($sqlCekEmail);
                        if($resCekEmail->num_rows==0){
                            if(getPost("dftrPass")!=NULL && getPost("dftrPassK")!=NULL){
                                if(getPost("dftrPass")==getPost("dftrPassK")){
                                    $sqlUpDftr="insert into tb_user(id_user,email_user,pass_user,jns_user,id_user_tipe,token,aktif,nm_user,url_foto) ";
                                    $sqlUpDftr.="values('". getPost("dftrNip")."','". getPost("dftrEmail")."','". md5(getPost("dftrPass"))."','Pegawai','user','1111111111111111',0,'". getPost("dftrNama")."','propic/". getPost("dftrNip").".jpg')";
                                    if($con->query($sqlUpDftr)){
                                        $sukses=TRUE;
                                        sentActivationEmail(getPost("dftrNip"), getPost("dftrEmail"), getPost("dftrNama"));
                                        ?>
        <div class="alert alert-success" role="alert">Pendaftaran user berhasil. kami telah mengirimkan email aktivasi akun ke <?php echo getPost("dftrEmail"); ?>. Silahkan cek email anda untuk melakukan aktivasi.</div>                    
                <?php
                                    }else{
                                        ?>
    <div class="alert alert-danger" role="alert">Maaf, terjadi kesalahan.</div>                    
                <?php
                                    }
                                }else{
                                    ?>
    <div class="alert alert-danger" role="alert">Konfirmasi Password tidak sama.</div>                    
                <?php
                                }
                            }else{
                                ?>
    <div class="alert alert-danger" role="alert">Kolom password harus diisi.</div>                    
                <?php
                            }
                        }else{
                            ?>
    <div class="alert alert-danger" role="alert">Maaf, email ini sudah terdaftar</div>                    
                <?php
                        }
                    }else{

                    }
                }else{
                    ?>
    <div class="alert alert-danger" role="alert">NIP / ID tidak ditemukan</div>                    
                <?php
                }
            }else{
                ?>
    <div class="alert alert-danger" role="alert">Maaf, NIP / ID sudah sudah terdaftar</div>                    
                <?php
            }

        }
    }
?>
</div>
<form name="frmForget" id="frmLogin" action="" method="post">
    <input type="hidden" class="form-control" id="dftrSukses" name="dftrSukses" value="<?php if($sukses==TRUE){echo '1';}else{echo '0';} ?>">
    <div id="dftrNipDiv" class="form-group">
        <label class="control-label" for="igDftrNip">NIP / ID</label>
        <div id="igDftrNip" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            </span>
            <input type="text" class="form-control" placeholder="NIP / ID..." id="dftrNip" name="dftrNip" value="<?php echo getPost("dftrNip"); ?>" <?php if($nmDftr!=""){    echo ' readonly="true"';} ?> onchange="unlockSubmit()">
            <span id="dftrNipIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="dftrNipStatus" class="sr-only">(success)</span>
        </div>
        <span id="dftrNipHelp" class="help-block"></span>
    </div>
    <?php
    if(getPost("dftrNip") && $nmDftr!=""){
        ?>
    <div id="dftrNamaDiv" class="form-group">
        <label class="control-label" for="igDftrNama">Nama Pegawai</label>
        <div id="igDftrNama" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            </span>
            <input type="text" class="form-control" placeholder="Nama Pegawai" id="dftrNama" name="dftrNama" value="<?php echo $nmDftr; ?>" readonly="true">
            <span id="dftrNamaIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="dftrNamaStatus" class="sr-only">(success)</span>
        </div>
        <span id="dftrNamaHelp" class="help-block"></span>
    </div>
    
    <div id="dftrEmailDiv" class="form-group">
        <label class="control-label" for="igDftrEmail">email</label>
        <div id="igDftrEmail" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            </span>
            <input type="text" class="form-control" placeholder="email..." id="dftrEmail" name="dftrEmail" value="<?php echo getPost("dftrEmail"); ?>" onkeyup="cekValid()">
            <span id="dftrEmailIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="dftrEmailStatus" class="sr-only">(success)</span>
        </div>
        <span id="dftrEmailHelp" class="help-block"></span>
    </div>
    <div id="dftrPassDiv" class="form-group">
        <label class="control-label" for="igDftrPass">Password</label>
        <div id="igDftrPass" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </span>
            <input type="password" class="form-control" placeholder="Password..." id="dftrPass" name="dftrPass" value="<?php echo getPost("dftrPass"); ?>" onkeyup="cekValid()">
            <span id="dftrPassIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="dftrPassStatus" class="sr-only">(success)</span>
        </div>
        <span id="dftrPassHelp" class="help-block"></span>
    </div>
    <div id="dftrPassKDiv" class="form-group">
        <label class="control-label" for="igDftrPassK">Konfirmasi Password</label>
        <div id="igDftrPassK" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </span>
            <input type="password" class="form-control" placeholder="Konfirmasi Password..." id="dftrPassK" name="dftrPassK" value="<?php echo getPost("dftrPassK"); ?>" onkeyup="cekValid()">
            <span id="dftrPassKIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="dftrPassKStatus" class="sr-only">(success)</span>
        </div>
        <span id="dftrPassKHelp" class="help-block"></span>
    </div>    
        <?php
    }
    ?>
    <br>
    <div style="text-align: right">
        <button id="btnSubmit" type="submit" class="btn btn-default <?php if(!$sukses){echo 'disabled';} ?>" style="background:#FFC312;border: none" <?php if(!$sukses){echo 'disabled';} ?>><?php if($sukses){echo 'Kirim lagi email';}else{echo 'Daftar';} ?></button>
    </div><br>
    
</form>
<div style="text-align: center;border-top-style: solid; border-top-width: 1px; border-top-color: rgba(0,0,0,0.2);padding: 10px">
    <p>Sudah pernah mendaftar? <a href="?signin=1<?php echo (getGet("pg"))?"&pg=".getGet("pg"):""; ?>">Login</a></p>
    <p>Non-Pegawai Dinas PUPR? <a href="?<?php echo (getGet("pg"))?"pg=".getGet("pg"):""; ?>">Gunakan Token</a></p>
</div>

<script type="text/javascript">
    /*var pencairan=function(){
        alert(this.id);
    }*/
    
    function cekValid(){
        validEmail=false;
        validPass=false;
        validPassK=false;
        if(document.getElementById("dftrNama").value!=''){
            lockSubmit();
            if(document.getElementById("dftrEmail").value!=''){
                validEmail=validateEmail(document.getElementById("dftrEmail").value);
            }
            if(document.getElementById("dftrPass").value!=''){
                if(document.getElementById("dftrPass").value.length>=5){
                    validPass=true;
                }
            }
            if(document.getElementById("dftrPassK").value!=''){
                if(document.getElementById("dftrPass").value==document.getElementById("dftrPassK").value){
                    validPassK=true;
                }
            }
            if(validPass && validPassK && validEmail){
                inputGroupSuccess('dftrPass','');
                inputGroupSuccess('dftrPassK','');
                inputGroupSuccess('dftrEmail','');
                unlockSubmit();
            }else{
                if(!validPass){
                    if(document.getElementById("dftrPass").value==''){
                        //alert('pass');
                        inputGroupError('dftrPass','Password tidak boleh kosong');
                    }else{
                        inputGroupError('dftrPass','Password harus minimal 5 karakter');
                    }
                }
                if(!validPassK){
                    //alert('passK');
                    if(document.getElementById("dftrPassK").value!=''){
                        inputGroupError('dftrPassK','Konfirmasi password tidak sama');
                    }
                }
                if(!validEmail){
                    //alert('email');
                    if(document.getElementById("dftrEmail").value!=''){
                        inputGroupError('dftrEmail','Email salah');
                    }
                }else{
                    inputGroupSuccess('dftrEmail','');
                }
            }
            if(validPass){
                if(!(hasNumber(document.getElementById("dftrPass").value) && hasUpperCase(document.getElementById("dftrPass").value) && hasLowerCase(document.getElementById("dftrPass").value))){
                    inputGroupWarning('dftrPass','Password sebaiknya menggunakan kombinasi huruf kecil, huruf besar, dan angka');
                }else{
                    inputGroupSuccess('dftrPass','');
                }
            }
            if(document.getElementById("dftrSukses").value=='1'){
                unlockSubmit();
            }
        }
    }
    
    function unlockSubmit(){
        //alert('jimi');
        document.getElementById("btnSubmit").setAttribute("Class","btn btn-default");
        document.getElementById("btnSubmit").removeAttribute("disabled");
    }
    
    function lockSubmit(){
        document.getElementById("btnSubmit").setAttribute("Class","btn btn-default disabled");
        document.getElementById("btnSubmit").setAttribute("disabled","true");
    }
    
    
</script>