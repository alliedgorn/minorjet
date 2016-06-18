var homeModule = (function($){
    
    var init = function(){
        
        initFocusedAircrafts();
        
    };
    
    var initFocusedAircrafts = function(){
        $('#focused-aircrafts').flexslider({ 
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: 259,
            itemMargin: 34,
            move: 1
        });
    };
    
    return {
        init : init
    };
    
})(jQuery);