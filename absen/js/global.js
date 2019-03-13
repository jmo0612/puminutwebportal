/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function inputGroupNormal(name,msg){
    //alert(document.getElementById("triwulan").getAttribute("value"));
    document.getElementById(name+"Div").setAttribute("Class","form-group");
    document.getElementById(name+"Icon").setAttribute("Class","glyphicon glyphicon-remove form-control-feedback");
    document.getElementById(name+"Status").innerHTML="(error)";
    document.getElementById(name+"Help").innerHTML=msg;
}
function inputGroupError(name,msg){
    //alert(document.getElementById("triwulan").getAttribute("value"));
    document.getElementById(name+"Div").setAttribute("Class","form-group has-error has-feedback");
    document.getElementById(name+"Icon").setAttribute("Class","glyphicon glyphicon-remove form-control-feedback");
    document.getElementById(name+"Status").innerHTML="(error)";
    document.getElementById(name+"Help").innerHTML=msg;
}
function inputGroupWarning(name,msg){
    //alert(document.getElementById("triwulan").getAttribute("value"));
    document.getElementById(name+"Div").setAttribute("Class","form-group has-warning has-feedback");
    document.getElementById(name+"Icon").setAttribute("Class","glyphicon glyphicon-warning-sign form-control-feedback");
    document.getElementById(name+"Status").innerHTML="(warning)";
    document.getElementById(name+"Help").innerHTML=msg;
}
function inputGroupSuccess(name,msg){
    //alert(document.getElementById("triwulan").getAttribute("value"));
    document.getElementById(name+"Div").setAttribute("Class","form-group has-success has-feedback");
    document.getElementById(name+"Icon").setAttribute("Class","glyphicon glyphicon-ok form-control-feedback");
    document.getElementById(name+"Status").innerHTML="(success)";
    document.getElementById(name+"Help").innerHTML=msg;
}

function hasNumber(myString) {
    return /\d/.test(myString);
}
function hasLowerCase(str) {
    return str.toUpperCase() !== str;
}
function hasUpperCase(str) {
    return str.toLowerCase() !== str;
}

function validateEmail(mail){
   var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
   if(mailformat.test(mail)){
    return true;
   }else{
    return false;
   }
}