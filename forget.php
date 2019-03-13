<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
?>
<h2>Lupa Password</h2>
<div id="info">
    <?php
        $sent=FALSE;
        if(getPost("emailF") && getPost("emailEF")){
            $sqlUser="select * from tb_user where email_user='".getPost("emailF")."@".getPost("emailEF")."' and aktif=1";
            $resUser=$con->query($sqlUser);
            if($resUser->num_rows>0){
                $rowUser=$resUser->fetch_assoc();
                
                
                
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
                    $mail->addAddress($rowUser["email_user"], $rowUser["nm_user"]);     // Add a recipient
                    //$mail->addAddress('ellen@example.com');               // Name is optional
                    //$mail->addReplyTo('info@example.com', 'Information');
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    //Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                    //Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Konfirmasi Ganti Password';
                    $htmlMsg='<p>Sdr/i <b>'.$rowUser["nm_user"].'</b>,</p>';
                    $htmlMsg.='<p>Anda melakukan permintaan ganti password untuk akun anda pada aplikasi e-Archive PUPR Minut ';
                    $htmlMsg.='tanggal '. dateMedium(date('Y-m-d')) .'. Untuk melanjutkan terlebih dahulu pastikan anda terhubung pada jaringan lokal Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Minahasa Utara.</p>';
                    $theAuth= md5($rowUser["id_user"]. date("Y-m-d H:i:s"). rand(0, 1000000));
                    $htmlMsg.='<p>Silahkan klik di <a href="'. getUrl("login.php?auth=").$theAuth.'">sini</a> untuk mengganti password anda. Terima kasih.</p>';
                    $mail->Body    = $htmlMsg;
                    $mail->AltBody = 'Link ganti password anda: '. getUrl("login.php?auth=").$theAuth;

                    
                    $sqlMakeAuth="replace into tb_auth(id_auth,id_user,for_activation) values('".$theAuth."','".$rowUser["id_user"]."',0)";
                    if($con->query($sqlMakeAuth)){
                        $mail->send();
                        $sent=TRUE;
                        ?>
    <div class="alert alert-success" role="alert">Link untuk ganti password telah dikirim ke email anda.</div>                    
                        <?php
                    }else{
                        $sent=FALSE;
                        ?>
    <div class="alert alert-danger" role="alert">Maaf, terjadi kesalahan pada sistem.</div>                    
                        <?php
                    }
                } catch (Exception $e) {
                    $sent=FALSE;
                    ?>
    <div class="alert alert-danger" role="alert">Maaf, saat ini server tidak bisa mengirim email.</div>                    
                    <?php
                    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                }
            }else{
                ?>
    <div class="alert alert-danger" role="alert">Maaf, email ini tidak terdaftar atau telah di-nonaktifkan.</div>                    
                <?php
            }
        }
    ?>
</div>
<form name="frmForget" id="frmLogin" action="" method="post">
    <div class="input-group">
        <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        </span>
        <input type="text" class="form-control" placeholder="email..." id="emailF" name="emailF" value="<?php if(getPost("emailF"))echo getPost("emailF");?>">
        <span class="input-group-addon">@</span>
        <input type="text" class="form-control" placeholder="contoh.com" id="emailEF" name="emailEF" value="<?php if(getPost("emailEF"))echo getPost("emailEF");?>">
    </div><br>
    <div style="text-align: right">
        <button type="submit" class="btn btn-default" style="background:#FFC312;border: none"> <?php if($sent){echo 'Kirim ulang email';} else{ echo 'Ubah Password';}?></button>
    </div><br>
    
</form>
<div style="text-align: center;border-top-style: solid; border-top-width: 1px; border-top-color: rgba(0,0,0,0.2);padding: 10px">
    <p>Pegawai Dinas PUPR? <a href="?signin=1<?php echo (getGet("pg"))?"&pg=".getGet("pg"):""; ?>">Login</a> / <a href="login.php?signup=1<?php echo (getGet("pg"))?"&pg=".getGet("pg"):""; ?>">Daftar</a></p>
    <p>Non-Pegawai Dinas PUPR? <a href="?<?php echo (getGet("pg"))?"pg=".getGet("pg"):""; ?>">Gunakan Token</a></p>
</div>