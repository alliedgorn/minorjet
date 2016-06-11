/*
 * Application
 */

$(document).ready(function(){
    
    $(document).tooltip({
        selector: "[data-toggle=tooltip]"
    });
    
    $('nav').affix({
      offset: {
        top: $('header').height()
      }
    });
    
    homeModule.init();
    
});

