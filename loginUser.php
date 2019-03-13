<h2>Masuk</h2>
<p>(Khusus pegawai Dinas PUPR Minut)</p>
<p style="text-align: right">Non-Pegawai Dinas PUPR? <a href="?">Gunakan Token</a></p>
<?php
    if(getPost("nip")!=NULL && getPost("pass")!=NULL){
        $res=$con->query("select * from tb_user where email_user='" . getPost("nip")."' and pass_user='" . md5(getPost("pass"))."' and aktif=1");
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $_SESSION["usrId"]=$row["id_user"];
            redirect("arsip");
        }else{
            $res=$con->query("select * from tb_user where id_user='" . getPost("nip")."' and pass_user='" . md5(getPost("pass"))."' and aktif=1");
            if($res->num_rows>0){
                $row=$res->fetch_assoc();
                $_SESSION["usrId"]=$row["id_user"];
                redirect("arsip");
            }else{
                ?>
<div class="alert alert-danger" role="alert">Login gagal. Periksa kembali ID dan Password anda.</div>                    
                <?php
            }
        }
    }
?>
<form name="frmLogin" id="frmLogin" action="" method="post">
    <div class="input-group">
        <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        </span>
        <input type="text" class="form-control" placeholder="email/NIP..." id="nip" name="nip" value="<?php if(getPost("nip"))echo getPost("nip");?>">
    </div><br>
    <div class="input-group">
        <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
        </span>
        <input type="password" class="form-control" placeholder="Password..." id="pass" name="pass">
    </div><br>
    <div style="text-align: right">
        <button type="submit" class="btn btn-default" style="background:#FFC312;border: none">Masuk</button>
    </div><br>
    
</form>
<div style="text-align: center;border-top-style: solid; border-top-width: 1px; border-top-color: rgba(0,0,0,0.2);padding: 10px">
    Belum punya akun? <a href="login.php?signup=1">Daftar</a><br>
    <a href="login.php?forget=1">Lupa password?</a>
</div>