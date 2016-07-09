var homeModule = (function($){
    
    var init = function(){
        
        initFocusedAircrafts();
        
    };
    
    var initFocusedAircrafts = function(){
        
        var itemWidth;
        
        if ( screen.width > 375 ) {
            itemWidth = 259;
        } else {
            itemWidth = screen.width;
        }
        
        $('#focused-aircrafts').flexslider({ 
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: itemWidth,
            itemMargin: 34,
            move: 1
        });
    };
    
    return {
        init : init
    };
    
})(jQuery);