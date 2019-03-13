<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class JmoDateTime extends DateTime{
    public function JmoDateTime($time="now",$format="Y-m-d H:i:s"){
        if($time=="now") {
            parent::__construct();
        }else{
            $tmp=DateTime::createFromFormat($format,$time);
            parent::__construct($tmp->format("Y-m-d H:i:s"));
        }
    }
    public function toString($format="Y-m-d H:i:s") {
      return $this->format($format);
    }
    public function diffInSeconds($date){
        $d=(int)$this->diff($date)->format("%r%a");
        $h=(int)$this->diff($date)->format("%r%h");
        $m=(int)$this->diff($date)->format("%r%i");
        $s=(int)$this->diff($date)->format("%r%s");
        return $d*24*60*60+$h*60*60+$m*60+$s;
    }
    public function diffInMinutes($date){
        return (int)($this->diffInSeconds($date)/60);
    }
    public function diffInHours($date){
        return (int)($this->diffInSeconds($date)/3600);
    } 
    public function diffInDays($date){
        return (int)$this->diff($date)->format("%r%a");
    }
    public function diffInMonths30($date){
        return (int)($this->diffInDays($date)/30);
    }
    public function diffInYears30($date){
        return (int)($this->diffInMonths30($date)/12);
    }
    public function diffInMonths($date){
        $myStamp=dateYearInt($this->toString())*12;
        $theStamp=dateYearInt($date->toString())*12;
        $myStamp+=dateMonthInt($this->toString());
        $theStamp+=dateMonthInt($date->toString());
        return $theStamp-$myStamp;
    }
}

function dateDBFormat($str){
    if(isDate($str)){
        return date_format(new \DateTime($str), "Y-m-d");
    }
    return NULL;
}

function isDate($str) {
    if (!$str) {
        return false;
    }

    try {
        new \DateTime($str);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function isNumber($str) {
    return (is_numeric($str));
}

function noDoubleSpaceStr($str) {
    return (preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $str));
}

function likeFormatStr($str) {
    return (str_replace(" ", "%", noDoubleSpaceStr(" ".$str." ")));
}



function redirect($phpFile){
    global $extraUrl;
    $page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    header("Location: ".$page_url.$extraUrl."/".$phpFile);
}

function getUrl($phpFile){
    global $extraUrl;
    $page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    return $page_url.$extraUrl."/".$phpFile;
}

function getPost($var){
    if(!isset($_POST[$var]))return NULL;
    return $_POST[$var];
}

function getGet($var){
    if(!isset($_GET[$var]))return NULL;
    return $_GET[$var];
}

function money($num){
    return "Rp. ". number_format($num,2,",",".");
}

function dateMedium($date){
    return strftime( "%d-%b-%Y", strtotime(date_format(DateTime::createFromFormat("Y-m-d",$date), "Y-m-d")));
    //return date_format(DateTime::createFromFormat("Y-m-d",$date), "Y-m-d");
}

function dateComplete($date){
    return strftime( "%A, %d %B %Y", strtotime(date_format(DateTime::createFromFormat("Y-m-d",$date), "Y-m-d")));
}

function dateClock($date){
    if(!$date)return "";
    return date_format(DateTime::createFromFormat("Y-m-d H:i:s",$date), "H:i:s");
}

function dateClockMin($date){
    if(!$date)return "";
    return date_format(DateTime::createFromFormat("Y-m-d H:i:s",$date), "H:i");
}

function dateDayWeekMin($date){
    return strftime( "%a", strtotime(date_format(DateTime::createFromFormat("Y-m-d",$date), "Y-m-d")));
}

function dateMonth($date){
    return strftime( "%B %Y", strtotime(date_format(DateTime::createFromFormat("Y-m-d",$date), "Y-m-d")));
}

function dateMonthInt($str){
    if(isDate($str)){
        return date_format(new \DateTime($str), "n");
    }
    return 0;
}

function dateYearInt($str){
    if(isDate($str)){
        return date_format(new \DateTime($str), "Y");
    }
    return 0;
}

function dateDayInt($str){
    if(isDate($str)){
        return date_format(new \DateTime($str), "j");
    }
    return 0;
}

function dateDayWeekInt($str){
    if(isDate($str)){
        return date_format(new \DateTime($str), "N");
    }
    return 0;
}

function updateUrlGet($theVar, $theValue){
    $ret="";
    $url=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
    "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
    $_SERVER['REQUEST_URI']; 

    $urlData=explode("?",$url);
            
    $url=$urlData[0];
            
    $prms=null;
    if(sizeof($urlData)>1){
        $prms=explode("&",$urlData[1]);
        if(sizeof($prms)>0){
            $found=false;
            for($i=0;$i<sizeof($prms);$i++){
                $els=explode("=",$prms[$i]);
                if($els[0]==$theVar){
                    $found=true;
                    $prms[$i]=$theVar."=".$theValue;
                }
            }
            
            $urlData[1]=implode("&",$prms);
            if(!$found){
                $urlData[1].="&".$theVar."=".$theValue;
            }
        }
        $url=implode("?",$urlData);    
    }else{
        $url=$urlData[0]."?".$theVar."=".$theValue;
    }
    

    $ret=$url;
    return $ret;
}

