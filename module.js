function updateUrlGet(theVar, theValue){
    var ret="";
    var url=window.location.toString();
    var urlData=url.split("?");

    url=urlData[0];

    var prms=null;
    if(urlData.length>1){
        prms=urlData[1].split("&");
        if(prms.length>0){
            var found=false;
            for(var i=0;i<prms.length;i++){
                var els=prms[i].split("=");
                if(els[0]==theVar){
                    found=true;
                    prms[i]=theVar+"="+theValue;
                }
            }
            urlData[1]=prms.join("&");
            if(!found){
                urlData[1]+="&"+theVar+"="+theValue;
            }
        }
        url=urlData.join("?");    
    }else{
        url=urlData[0]+"?"+theVar+"="+theValue;
    }
    

    ret=url;
    return ret;
}

function dateDiff(datepart, fromdate, todate, ignoreTime) {	
    fDateN=Date.UTC(fromdate.getFullYear(),fromdate.getMonth(),fromdate.getDate());
    tDateN=Date.UTC(todate.getFullYear(),todate.getMonth(),todate.getDate());
    if(ignoreTime=false){
        fDateN=Date.UTC(fromdate.getFullYear(),fromdate.getMonth(),fromdate.getDate(),fromdate.getHours(),fromdate.getMinutes(),fromdate.getSeconds());
        tDateN=Date.UTC(todate.getFullYear(),todate.getMonth(),todate.getDate(),todate.getHours(),todate.getMinutes(),todate.getSeconds());
    }
    datepart = datepart.toLowerCase();	
    var diff = tDateN - fDateN;
    var n=1;
    if(diff<0)n=-1;
    diff=Math.abs(diff);	
    var divideBy = { w:604800000, 
                     d:86400000, 
                     h:3600000, 
                     n:60000, 
                     s:1000 };	
    
    var ret=Math.floor( diff/divideBy[datepart]);
    
    return ret*n;
}

function monthDiff(fromMonth, toMonth) {	
    var myStamp=fromMonth.getFullYear()*12+fromMonth.getMonth();
    var theStamp=toMonth.getFullYear()*12+toMonth.getMonth();
    var ret=theStamp-myStamp;
    return ret;
}

