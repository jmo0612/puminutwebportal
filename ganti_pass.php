<h2>Ganti Password</h2>
<div id="info">
<?php
    if(getPost("nipG")){
        $sqlPass="update tb_user set pass_user='". md5(getPost("passG"))."' where id_user='". getPost("nipG")."'";
        if($con->query($sqlPass)){
            $sqlAuth="delete from tb_auth where id_auth='". getGet("auth")."'";
            $con->query($sqlAuth);
            ?>
<div class="alert alert-success" role="alert">Ganti password berhasil.</div>                    
            <?php
            $_SESSION["usrId"]= getPost("nipG");
            redirect($sPage);
        }else{
            ?>
<div class="alert alert-danger" role="alert">Error update password.</div>                    
            <?php
        }
    }
?>
</div>
<form name="frmForget" id="frmLogin" action="" method="post">
    <div class="form-group">
        <label class="control-label" for="igNipG">NIP / ID</label>
        <div class="input-group" id="igNipG">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            </span>
            <input type="text" class="form-control" placeholder="email/NIP..." id="nipG" name="nipG" value="<?php echo $row["id_user"]; ?>" readonly>
        </div>
    </div>
    <div id="passGDiv" class="form-group">
        <label class="control-label" for="igPassG">Password Baru</label>
        <div id="igPassG" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </span>
            <input type="password" class="form-control" placeholder="Password Baru..." id="passG" name="passG" onkeyup="cekPass()">
            <span id="passGIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="passGStatus" class="sr-only">(success)</span>
        </div>
        <span id="passGHelp" class="help-block"></span>
    </div>
    <div id="passKGDiv" class="form-group">
        <label class="control-label" for="igPassKG">Konfirmasi Password</label>
        <div id="igPassKG" class="input-group">
            <span class="input-group-addon" style="background:#FFC312;border: none;color: black">
                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </span>
            <input type="password" class="form-control" placeholder="Konfirmasi Password..." id="passKG" name="passKG" onkeyup="cekPass()">
            <span id="passKGIcon" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="passKGStatus" class="sr-only">(success)</span>
        </div>
        <span id="passKGHelp" class="help-block"></span>
    </div>
    <br>
    <div style="text-align: right">
        <button id="btnSubmit" type="submit" class="btn btn-default disabled" style="background:#FFC312;border: none" disabled>Ubah Password</button>
    </div><br>
    
</form>
<div style="text-align: center;border-top-style: solid; border-top-width: 1px; border-top-color: rgba(0,0,0,0.2);padding: 10px">
    <p>Pegawai Dinas PUPR? <a href="?signin=1&pg=<?php echo getGet("pg"); ?>">Login</a> / <a>Daftar</a></p>
    <p>Non-Pegawai Dinas PUPR? <a href="?pg=<?php echo getGet("pg"); ?>">Gunakan Token</a></p>
</div>

<script type="text/javascript">
    /*var pencairan=function(){
        alert(this.id);
    }*/
    
    function unlockSubmit(){
        //alert('jimi');
        document.getElementById("btnSubmit").setAttribute("Class","btn btn-default");
        document.getElementById("btnSubmit").removeAttribute("disabled");
    }
    
    function lockSubmit(){
        document.getElementById("btnSubmit").setAttribute("Class","btn btn-default disabled");
        document.getElementById("btnSubmit").setAttribute("disabled","true");
    }
    
    function cekPass(){
        validP=false;
        validK=false;
        if(document.getElementById("passG").value!=''){
            if(document.getElementById("passG").value.length>=5){
                validP=true;
            }
        }
        if(document.getElementById("passKG").value!=''){
            if(document.getElementById("passG").value==document.getElementById("passKG").value){
                validK=true;
            }
        }
        
        inputGroupNormal('passG','');
        inputGroupNormal('passKG','');
        
        if(validP && validK){
            inputGroupSuccess('passG','');
            inputGroupSuccess('passKG','');
            unlockSubmit();
        }else{
            lockSubmit();
            if(!validP){
                if(document.getElementById("passG").value==''){
                    inputGroupError('passG','Password tidak boleh kosong');
                }else{
                    inputGroupError('passG','Password harus minimal 5 karakter');
                }
            }
            if(!validK){
                if(document.getElementById("passKG").value!=''){
                    inputGroupError('passKG','Konfirmasi password tidak sama');
                }
            }
        }
        if(validP){
            if(!(hasNumber(document.getElementById("passG").value) && hasUpperCase(document.getElementById("passG").value) && hasLowerCase(document.getElementById("passG").value))){
                inputGroupWarning('passG','Password sebaiknya menggunakan kombinasi huruf kecil, huruf besar, dan angka');
            }else{
                inputGroupSuccess('passG','');
            }
        }
    }
    
    
</script>