jQuery(document).ready(function($) { 
    var screen = scriptParams.screen;
    if( screen == 'tags' ){
        $('label[for=parent], label[for=tag-description]').parent().remove(); 
    }else if(screen == 'term'){
        $('.term-description-wrap').remove();
    }  
});