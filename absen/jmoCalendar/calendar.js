$('.jmocaldate').mouseover(function(){
    $(this).click();
});

$('.jmocaldate').mouseout(function(){
    $('#tt').css("visibility","hidden");
    
});

$('.jmocaldate').click(function(){
    $('#tt').css("visibility","visible");
    $('#tt').css("border-radius","15px");
    
    var n=$(this).attr("name");

    var t=$(this).offset().top-$("#tt").height()+5;
    var radT="bottom";
    if(n.substr(0,3)=="bwh"){
        t=$(this).offset().top+$(this).height()-5;
        radT="top";
    }
    

    var l=$(this).offset().left-$("#tt").width()+5;
    var radL="right";
    if(n.substr(4,6)=="kn"){
        l=$(this).offset().left+$(this).width()-5;
        radL="left";
    }

    $('#tt').css("border-"+radT+"-"+radL+"-radius","0px");

    $('#tt').offset({
        left:l,
        top:t
    });
});