window.onload = function() {
    $(".loading-anime").fadeOut("slow", function () {
        //$('#loading-page').css("display", "none");
        $('#loading-page').remove();
        $('.loading-anime').remove();
    });
};


//按鈕效果
$(function () {
        //按鈕效果 背景不透明 JPG
        $('.animebtn').children('img').css({opacity: 0});

        $('.animebtn').bind('mouseenter', function () {
            $(this).children('img').stop().animate({
                opacity: 1
            }, 100);
            //$('.startbtn').css({"background-image":"none"}, 100);
        });

        $('.animebtn').bind('mouseleave', function () {
            $(this).children('img').stop().animate({
                opacity: 0
            }, 100);
           //$('.startbtn').css({"background":"url(images/startbtn.png) no-repeat"});
        });
    
        //按鈕效果 mouseover
        $('.mouseover').bind('mouseenter', function () {
            $(this).stop().animate({
                opacity: 0.7
            }, 100);
        });

        $('.mouseover').bind('mouseleave', function () {
            $(this).stop().animate({
                opacity: 1
            }, 100);
           //$('.startbtn').css({"background":"url(images/startbtn.png) no-repeat"});
        });
});