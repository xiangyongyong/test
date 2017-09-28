/**
 * Created by GL on 2017/4/22.
 */
//地图
/*var map = new AMap.Map('container', {
        resizeEnable: true,
        zoom:11,
        mapStyle:'blue_night',
        center: [114.305214,30.592934]
});*/

$(function(){
    //页面加载事件
    $(window).load(function(){
        $("#chart_box_l").show().animate({left: '0px'},400);
        $("#chart_box_r").show().animate({top: '100px'},400);
    });

    //点击事件

    //侧边栏
    var flag=1;
    $('.switch').click(function(){
        if(flag==1){
            $(".chart-box-l").animate({left: '-500px'},400);
            $(this).animate({right: '0px'},400);
            flag=0;
        }
        else{
            $(".chart-box-l").animate({left: '0'},400);
            $(this).animate({right: '0'},400).css('background','none');
            flag=1;
        }
    });

    //地图标记点击事件
    /*marker.on('click',function(){

    });*/

    $("#chart_details_l .close").click(function(){
        $("#chart_details_l").animate({left: '-500px'},300,function(){
            $(this).hide();
        });

        $("#chart_details_r").animate({top: '-450px'},300,function(){
            $(this).hide(400)
        });

        $("#chart_box_l").animate({left: '0'}).show();
        $("#chart_box_r").animate({top: '100px'},500).show();
    });

    //滚动条美化
    $(".chart-content").niceScroll({
        cursorborder: "0 none",
        cursorcolor: "none",
        cursoropacitymin: "0",
        boxzoom: false
    });
});

// marker上的点击
function markerClick() {
    $("#chart_box_l").animate({left: '-500px'},300,function(){
        $(this).hide()
    });
    $("#chart_box_r").animate({top: '-450px'},300,function(){
        $(this).hide(400)
    });

    $("#chart_details_l").animate({left: '0'}).show();
    $("#chart_details_r").animate({top: '100px'},500).show();
}