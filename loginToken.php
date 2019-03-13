<h2>Masuk</h2>
<p>(Hubungi admin untuk dapatkan kode Token)</p><br>
<?php
    if(getGet("exp")!=NULL){
        ?>
<div class="alert alert-danger" role="alert">Token kedaluwarsa. Silahkan hubungi admin untuk mendapatkan token baru.</div>                    
        <?php
    }elseif(getGet("na")!=NULL){
        ?>
<div class="alert alert-danger" role="alert">Maaf, akun Guest tidak tersedia.</div>                    
        <?php
    }elseif(getGet("inact")!=NULL){
        ?>
<div class="alert alert-danger" role="alert">Maaf, akun anda telah dinon-aktifkan.</div>                    
        <?php
    }else{
        $tok= getPost("token1").getPost("token2").getPost("token3").getPost("token4");
        $sqlTok="select * from tb_token where token='".$tok."' and require_login=0";
        $res=$con->query($sqlTok);
        if($res->num_rows>0){
            $row=$res->fetch_assoc();
            $validToken=TRUE;
            if($row["limited_time"]==1){
                if(new DateTime(date("Y-m-d")) >= new DateTime($row["expire_date"])){
                    $validToken=FALSE;
                }
            }
            if($validToken){
                $_SESSION["usrToken"]=$tok;
                redirect("");
            }else{
                ?>
<div class="alert alert-danger" role="alert">Token kedaluwarsa.</div>                    
                <?php
            }
        }else{
            if($tok!=""){
                ?>
<div class="alert alert-danger" role="alert">Token salah.</div>                    
                <?php
            }
        }
    }
?>
<form name="frmLogin" id="frmLogin" action="" method="post">
    <div class="input-group">
        <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
        </span>
        <input id="token1" name="token1" type="password" maxlength="4" class="form-control" placeholder="0000">
        <span class="input-group-addon">-</span>
        <input id="token2" name="token2" type="password" maxlength="4" class="form-control" placeholder="0000">
        <span class="input-group-addon">-</span>
        <input id="token3" name="token3" type="password" maxlength="4" class="form-control" placeholder="0000">
        <span class="input-group-addon">-</span>
        <input id="token4" name="token4" type="password" maxlength="4" class="form-control" placeholder="0000">
    </div><br>
    <div style="text-align: right">
        <button type="submit" class="btn btn-default" style="background:#FFC312;border: none">Masuk</button>
    </div><br>
    
</form>
<div style="text-align: center;border-top-style: solid; border-top-width: 1px; border-top-color: rgba(0,0,0,0.2);padding: 10px">
    <p>Pegawai Dinas PUPR? <a href="?signin=1">Login</a></p>
</div>